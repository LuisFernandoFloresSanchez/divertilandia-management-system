<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     * Ahora usa únicamente el ProductionDataSeeder que contiene todos los datos reales.
     */
    public function run(): void
    {
        $this->command->info('🚀 Iniciando seeding de la base de datos...');
        
        // Crear usuario de prueba (opcional - puedes comentar si no lo necesitas)
        User::factory()->create([
            'name' => 'Admin Divertilandia',
            'email' => 'admin@divertilandia.com',
        ]);

        // Ejecutar el seeder con todos los datos reales de producción
        $this->call(ProductionDataSeeder::class);
        
        $this->command->info('✅ Base de datos poblada exitosamente con datos de producción!');
    }
}
