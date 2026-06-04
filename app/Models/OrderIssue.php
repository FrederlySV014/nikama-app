<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'order_id',
    'user_id',
    'issue_type',
    'description',
    'status',
    'resolution_action',
    'refund_amount',
    'assigned_admin_id',
])]
class OrderIssue extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'order_issues';

    public const STATUS_OPEN = 'open';

    public const STATUS_INVESTIGATING = 'investigating';

    public const STATUS_RESOLVED = 'resolved';

    public const STATUS_REJECTED = 'rejected';

    public const ACTION_REFUND = 'refund_issued';

    public const ACTION_COUPON = 'coupon_gifted';

    public const ACTION_NONE = 'no_action';

    /**
     * Get issue statuses.
     *
     * @return array<int, string>
     */
    public static function statuses(): array
    {
        return [
            self::STATUS_OPEN,
            self::STATUS_INVESTIGATING,
            self::STATUS_RESOLVED,
            self::STATUS_REJECTED,
        ];
    }

    /**
     * Get resolution actions.
     *
     * @return array<int, string>
     */
    public static function resolutions(): array
    {
        return [
            self::ACTION_REFUND,
            self::ACTION_COUPON,
            self::ACTION_NONE,
        ];
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'refund_amount' => 'float',
        ];
    }

    /**
     * Get the order associated with the issue.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the user who reported the issue.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the admin agent assigned to the issue.
     */
    public function assignedAdmin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_admin_id');
    }
}
