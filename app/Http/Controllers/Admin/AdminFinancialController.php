<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\BusinessCommission;
use App\Models\BusinessPayout;
use App\Models\CustomerProfile;
use App\Models\DriverPayout;
use App\Models\DriverProfile;
use App\Models\WalletTransaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AdminFinancialController extends Controller
{
    /**
     * Mostrar listado de solicitudes de liquidación (Payouts).
     */
    public function payouts(Request $request): View
    {
        $type = $request->query('type', 'businesses'); // businesses o drivers
        $status = $request->query('status', 'pending'); // pending, processed, failed, all

        // Contadores rápidos de solicitudes pendientes
        $pendingBusinessesCount = BusinessPayout::where('status', BusinessPayout::STATUS_PENDING)->count();
        $pendingDriversCount = DriverPayout::where('status', DriverPayout::STATUS_PENDING)->count();

        // Totales procesados y fallidos generales
        $totalProcessedAmount = BusinessPayout::where('status', BusinessPayout::STATUS_PROCESSED)->sum('net_amount')
            + DriverPayout::where('status', DriverPayout::STATUS_PROCESSED)->sum('amount');

        $totalPendingAmount = BusinessPayout::where('status', BusinessPayout::STATUS_PENDING)->sum('net_amount')
            + DriverPayout::where('status', DriverPayout::STATUS_PENDING)->sum('amount');

        if ($type === 'drivers') {
            $query = DriverPayout::with('driver.user');

            if ($status !== 'all') {
                $query->where('status', $status);
            }

            $payoutsList = $query->latest()->paginate(20)->withQueryString();
        } else {
            $query = BusinessPayout::with('business');

            if ($status !== 'all') {
                $query->where('status', $status);
            }

            $payoutsList = $query->latest()->paginate(20)->withQueryString();
        }

        return view('admin.financial.payouts', compact(
            'payoutsList',
            'type',
            'status',
            'pendingBusinessesCount',
            'pendingDriversCount',
            'totalProcessedAmount',
            'totalPendingAmount'
        ));
    }

    /**
     * Procesar o rechazar una liquidación (Payout).
     */
    public function processPayout(Request $request, string $type, string $id): RedirectResponse
    {
        $request->validate([
            'status' => 'required|in:processed,failed',
            'transaction_reference' => 'required_if:status,processed|nullable|string|max:100',
            'notes' => 'nullable|string|max:500',
        ]);

        $newStatus = $request->input('status');
        $reference = $request->input('transaction_reference');
        $notes = $request->input('notes');

        DB::beginTransaction();
        try {
            if ($type === 'driver') {
                $payout = DriverPayout::findOrFail($id);

                if ($payout->status !== DriverPayout::STATUS_PENDING) {
                    return redirect()->back()->with('warning', 'Esta liquidación ya ha sido procesada.');
                }

                $payout->status = $newStatus;
                $payout->transaction_reference = $reference;
                $payout->notes = $notes;
                $payout->processed_at = now();
                $payout->save();

                if ($newStatus === DriverPayout::STATUS_PROCESSED) {
                    // Debitar el monto de la billetera virtual del Repartidor
                    WalletTransaction::create([
                        'holder_type' => DriverProfile::class,
                        'holder_id' => $payout->driver_profile_id,
                        'amount' => $payout->amount,
                        'type' => WalletTransaction::TYPE_DEBIT,
                        'transaction_type' => 'withdrawal',
                        'reference_id' => $payout->id,
                        'description' => 'Liquidación de fondos autorizada por administración.',
                    ]);
                }
            } else {
                $payout = BusinessPayout::findOrFail($id);

                if ($payout->status !== BusinessPayout::STATUS_PENDING) {
                    return redirect()->back()->with('warning', 'Esta liquidación ya ha sido procesada.');
                }

                $payout->status = $newStatus;
                $payout->transaction_reference = $reference;
                $payout->notes = $notes;
                $payout->processed_at = now();
                $payout->save();

                if ($newStatus === BusinessPayout::STATUS_PROCESSED) {
                    // Debitar el monto neto de la billetera virtual del Comercio
                    WalletTransaction::create([
                        'holder_type' => Business::class,
                        'holder_id' => $payout->business_id,
                        'amount' => $payout->net_amount,
                        'type' => WalletTransaction::TYPE_DEBIT,
                        'transaction_type' => 'withdrawal',
                        'reference_id' => $payout->id,
                        'description' => 'Liquidación de fondos autorizada por administración.',
                    ]);
                }
            }

            DB::commit();

            $msg = $newStatus === 'processed' ? 'Liquidación marcada como pagada con éxito.' : 'Liquidación rechazada/marcada como fallida.';
            $alertType = $newStatus === 'processed' ? 'success' : 'warning';

            return redirect()->back()->with($alertType, $msg);
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->with('warning', 'Ocurrió un error al procesar el pago: '.$e->getMessage());
        }
    }

    /**
     * Ver comisiones de los comercios (Businesses).
     */
    public function commissions(Request $request): View
    {
        $search = $request->query('search');

        // Cargar comercios para el selector en el formulario de registro de comisiones
        $businesses = Business::where('status', Business::STATUS_APPROVED)->orderBy('business_name')->get();

        $query = BusinessCommission::with('business');

        if ($search) {
            $query->whereHas('business', function ($q) use ($search) {
                $q->where('business_name', 'like', "%{$search}%");
            });
        }

        $commissionsList = $query->latest()->paginate(20)->withQueryString();

        return view('admin.financial.commissions', compact('commissionsList', 'businesses', 'search'));
    }

    /**
     * Crear una nueva regla de comisión.
     */
    public function storeCommission(Request $request): RedirectResponse
    {
        $request->validate([
            'business_id' => 'required|exists:businesses,id',
            'commission_type' => 'required|in:percentage,fixed',
            'commission_value' => 'required|numeric|min:0',
            'starts_at' => 'required|date',
            'ends_at' => 'nullable|date|after:starts_at',
        ]);

        // Desactivar comisiones previas activas para el mismo negocio
        BusinessCommission::where('business_id', $request->input('business_id'))
            ->where('is_active', true)
            ->update(['is_active' => false]);

        BusinessCommission::create([
            'business_id' => $request->input('business_id'),
            'commission_type' => $request->input('commission_type'),
            'commission_value' => $request->input('commission_value'),
            'is_active' => true,
            'starts_at' => $request->input('starts_at'),
            'ends_at' => $request->input('ends_at'),
        ]);

        return redirect()->back()->with('success', 'Regla de comisión registrada y activada con éxito.');
    }

    /**
     * Alternar el estado activo de una comisión.
     */
    public function toggleCommissionStatus(BusinessCommission $commission): RedirectResponse
    {
        $commission->is_active = ! $commission->is_active;
        $commission->save();

        $statusText = $commission->is_active ? 'activada' : 'desactivada';

        return redirect()->back()->with('success', "La comisión ha sido {$statusText}.");
    }

    /**
     * Listar transacciones de billeteras virtuales.
     */
    public function walletTransactions(Request $request): View
    {
        $holderType = $request->query('holder_type', 'all');
        $type = $request->query('type', 'all'); // credit o debit
        $search = $request->query('search'); // id de holder u otros

        $query = WalletTransaction::query();

        if ($holderType === 'customer') {
            $query->where('holder_type', CustomerProfile::class);
        } elseif ($holderType === 'seller') {
            $query->where('holder_type', Business::class);
        } elseif ($holderType === 'driver') {
            $query->where('holder_type', DriverProfile::class);
        }

        if ($type !== 'all') {
            $query->where('type', $type);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('holder_id', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('transaction_type', 'like', "%{$search}%");
            });
        }

        $transactionsList = $query->latest()->paginate(20)->withQueryString();

        return view('admin.financial.transactions', compact('transactionsList', 'holderType', 'type', 'search'));
    }
}
