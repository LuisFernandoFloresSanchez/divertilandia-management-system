<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\FuelPrice;

class FuelPricesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Precios iniciales de combustible
        $initialPrices = [
            [
                'fuel_type' => 'regular',
                'price_per_liter' => 22.50, // Gasolina Roja - precio ejemplo
                'effective_date' => now()->format('Y-m-d'),
                'is_active' => true,
                'notes' => 'Precio inicial de gasolina roja'
            ],
            [
                'fuel_type' => 'premium',
                'price_per_liter' => 24.50, // Gasolina Verde - precio ejemplo
                'effective_date' => now()->format('Y-m-d'),
                'is_active' => true,
                'notes' => 'Precio inicial de gasolina verde'
            ]
        ];

        foreach ($initialPrices as $priceData) {
            FuelPrice::create($priceData);
        }

        $this->command->info('Precios iniciales de combustible creados exitosamente.');
    }
}
