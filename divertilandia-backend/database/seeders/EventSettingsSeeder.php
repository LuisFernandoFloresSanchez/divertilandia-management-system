<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EventSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            [
                'setting_key' => 'extra_hour_cost',
                'setting_name' => 'Costo por Hora Extra',
                'setting_value' => '100.00',
                'description' => 'Costo en pesos mexicanos por cada hora extra en eventos (configurable)',
                'is_active' => true,
            ],
            [
                'setting_key' => 'advance_payment_amount',
                'setting_name' => 'Monto de Anticipo',
                'setting_value' => '300.00',
                'description' => 'Monto fijo del anticipo requerido para confirmar un evento',
                'is_active' => true,
            ],
            [
                'setting_key' => 'default_event_duration',
                'setting_name' => 'Duración por Defecto del Evento',
                'setting_value' => '4',
                'description' => 'Duración en horas de un evento estándar',
                'is_active' => true,
            ],
            [
                'setting_key' => 'max_extra_hours',
                'setting_name' => 'Máximo de Horas Extras',
                'setting_value' => '3',
                'description' => 'Número máximo de horas extras que se pueden agregar a un evento',
                'is_active' => true,
            ],
        ];

        foreach ($settings as $setting) {
            \DB::table('event_settings')->updateOrInsert(
                ['setting_key' => $setting['setting_key']],
                $setting
            );
        }
    }
}
