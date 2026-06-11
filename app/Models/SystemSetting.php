<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'key',
    'value',
    'description',
])]
class SystemSetting extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'system_settings';

    /**
     * Get the full hierarchy of regions, provinces and districts in Peru.
     *
     * @return array<string, array<string, array<int, string>>>
     */
    public static function getAllPeruCoverageHierarchy(): array
    {
        return [
            'Lambayeque' => [
                'Chiclayo' => [
                    'Cayaltí', 'Chiclayo', 'Chongoyape', 'Eten', 'José Leonardo Ortiz',
                    'La Victoria', 'Lagunas', 'Monsefú', 'Nueva Arica', 'Oyotún',
                    'Pátapo', 'Picsi', 'Pimentel', 'Pomalca', 'Pucalá', 'Puerto Eten',
                    'Reque', 'Santa Rosa', 'Tumán', 'Zaña',
                ],
                'Lambayeque' => [
                    'Lambayeque', 'Chochope', 'Illimo', 'Jayanca', 'Mochumí',
                    'Mórrope', 'Motupe', 'Olmos', 'Pacora', 'Salas', 'San José', 'Túcume',
                ],
                'Ferreñafe' => [
                    'Ferreñafe', 'Cañaris', 'Incahuasi', 'Manuel Antonio Mesones Muro',
                    'Pítipo', 'Pueblo Nuevo',
                ],
            ],
            'Lima' => [
                'Lima' => [
                    'Cercado de Lima', 'Ate', 'Barranco', 'Breña', 'Carabayllo', 'Chaclacayo',
                    'Chorrillos', 'Cieneguilla', 'Comas', 'El Agustino', 'Independencia',
                    'Jesús María', 'La Molina', 'La Victoria', 'Lince', 'Los Olivos',
                    'Lurigancho', 'Lurín', 'Magdalena del Mar', 'Miraflores', 'Pachacámac',
                    'Pucusana', 'Pueblo Libre', 'Puente Piedra', 'Punta Hermosa',
                    'Punta Negra', 'Rímac', 'San Bartolo', 'San Borja', 'San Isidro',
                    'San Juan de Lurigancho', 'San Juan de Miraflores', 'San Martín de Porres',
                    'San Miguel', 'Santa Anita', 'Santa María del Mar', 'Santa Rosa',
                    'Santiago de Surco', 'Surquillo', 'Villa El Salvador', 'Villa María del Triunfo',
                ],
                'Callao' => [
                    'Callao', 'Bellavista', 'Carmen de la Legua', 'La Perla', 'La Punta',
                    'Mi Perú', 'Ventanilla',
                ],
            ],
            'La Libertad' => [
                'Trujillo' => [
                    'Trujillo', 'El Porvenir', 'Florencia de Mora', 'Huanchaco',
                    'La Esperanza', 'Laredo', 'Moche', 'Poroto', 'Salaverry', 'Simbal',
                    'Víctor Larco Herrera',
                ],
            ],
            'Arequipa' => [
                'Arequipa' => [
                    'Arequipa', 'Alto Selva Alegre', 'Cayma', 'Cerro Colorado', 'Characato',
                    'Chiguata', 'Jacobo Hunter', 'José Luis Bustamante y Rivero', 'La Joya',
                    'Mariano Melgar', 'Miraflores', 'Mollebaya', 'Paucarpata', 'Sabandía',
                    'Sachaca', 'San Juan de Siguas', 'San Juan de Tarucani',
                    'Santa Isabel de Siguas', 'Santa Rita de Siguas', 'Socabaya', 'Tiabaya',
                    'Uchumayo', 'Vítor', 'Yanahuara', 'Yarabamba', 'Yura',
                ],
            ],
            'Piura' => [
                'Piura' => [
                    'Piura', 'Castilla', 'Catacaos', 'Cura Mori', 'El Tallán',
                    'La Arena', 'La Unión', 'Las Lomas', 'Tambogrande', 'Veintiséis de Octubre',
                ],
            ],
        ];
    }

    /**
     * Get the list of active coverage districts.
     *
     * @return array<int, string>
     */
    public static function getActiveDistricts(): array
    {
        $setting = self::where('key', 'active_districts')->first();
        if (! $setting || ! $setting->value) {
            return [
                'Lambayeque|Chiclayo|Chiclayo',
                'Lambayeque|Chiclayo|José Leonardo Ortiz',
                'Lambayeque|Chiclayo|La Victoria',
                'Lambayeque|Chiclayo|Pimentel',
            ];
        }

        return json_decode($setting->value, true) ?: [];
    }

    /**
     * Check if a specific district is active for coverage.
     * Supports qualified checking to prevent name collision across provinces/departments.
     */
    public static function isDistrictActive(?string $dept, ?string $prov, ?string $dist): bool
    {
        if (! $dist) {
            return false;
        }

        $active = self::getActiveDistricts();
        $deptLower = strtolower(trim($dept ?? ''));
        $provLower = strtolower(trim($prov ?? ''));
        $distLower = strtolower(trim($dist));

        foreach ($active as $qualified) {
            $parts = explode('|', $qualified);
            if (count($parts) === 3) {
                // If department and province are provided, validate them
                $matchDept = empty($deptLower) || strtolower(trim($parts[0])) === $deptLower;
                $matchProv = empty($provLower) || strtolower(trim($parts[1])) === $provLower;
                $matchDist = strtolower(trim($parts[2])) === $distLower;

                if ($matchDept && $matchProv && $matchDist) {
                    return true;
                }
            } else {
                // Fallback to simple matching if stored format is plain (for backwards compatibility)
                if (strtolower(trim($qualified)) === $distLower) {
                    return true;
                }
            }
        }

        return false;
    }
}
