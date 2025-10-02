<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Package;
use App\Models\Game;
use App\Models\ToyType;
use App\Models\Event;
use App\Models\EventSetting;
use App\Models\ExpenseCategory;
use App\Models\Expense;
use App\Models\Vehicle;
use App\Models\EventVehicleUsage;
use App\Models\FuelPrice;
use App\Models\ToyClause;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class ExportProductionData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'export:production-data {--output=database/seeders/ProductionDataSeeder.php}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Exporta todos los datos de la base de datos actual para crear un seeder de producción';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Exportando datos de producción...');

        $seederContent = $this->generateSeederContent();
        
        $outputPath = $this->option('output');
        File::put(base_path($outputPath), $seederContent);
        
        $this->info("✅ Seeder generado exitosamente en: {$outputPath}");
        $this->info('📋 Para usar en producción:');
        $this->info('   1. Sube este archivo al servidor');
        $this->info('   2. Ejecuta: php artisan migrate:fresh');
        $this->info('   3. Ejecuta: php artisan db:seed --class=ProductionDataSeeder');
        
        return 0;
    }

    private function generateSeederContent(): string
    {
        $toyTypes = $this->exportToyTypes();
        $games = $this->exportGames();
        $toyClauses = $this->exportToyClauses();
        $gameClauseRelations = $this->exportGameClauseRelations();
        $packages = $this->exportPackages();
        $packageGameRelations = $this->exportPackageGameRelations();
        $eventSettings = $this->exportEventSettings();
        $events = $this->exportEvents();
        $expenseCategories = $this->exportExpenseCategories();
        $expenses = $this->exportExpenses();
        $vehicles = $this->exportVehicles();
        $eventVehicleUsage = $this->exportEventVehicleUsage();
        $fuelPrices = $this->exportFuelPrices();

        return <<<PHP
<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Package;
use App\Models\Game;
use App\Models\ToyType;
use App\Models\Event;
use App\Models\EventSetting;
use App\Models\ExpenseCategory;
use App\Models\Expense;
use App\Models\Vehicle;
use App\Models\EventVehicleUsage;
use App\Models\FuelPrice;
use App\Models\ToyClause;
use Illuminate\Support\Facades\DB;

class ProductionDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Este seeder contiene todos los datos reales exportados de la base de datos local.
     * Generado automáticamente el: {$this->getCurrentDateTime()}
     */
    public function run(): void
    {
        \$this->command->info('🚀 Iniciando importación de datos de producción...');

        // Deshabilitar verificaciones de claves foráneas temporalmente
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        try {
            \$this->seedToyTypes();
            \$this->seedGames();
            \$this->seedToyClauses();
            \$this->seedGameToyClauseRelations();
            \$this->seedPackages();
            \$this->seedPackageGameRelations();
            \$this->seedEventSettings();
            \$this->seedEvents();
            \$this->seedExpenseCategories();
            \$this->seedExpenses();
            \$this->seedVehicles();
            \$this->seedEventVehicleUsage();
            \$this->seedFuelPrices();
        } finally {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }

        \$this->command->info('✅ Importación de datos completada exitosamente!');
    }

    private function seedToyTypes()
    {
        \$this->command->info('📦 Importando tipos de juguetes...');
        
        \$toyTypes = {$toyTypes};

        foreach (\$toyTypes as \$toyType) {
            ToyType::updateOrCreate(['id' => \$toyType['id']], \$toyType);
        }
    }

    private function seedGames()
    {
        \$this->command->info('🎮 Importando juegos/inventario...');
        
        \$games = {$games};

        foreach (\$games as \$game) {
            Game::create(\$game);
        }
    }

    private function seedToyClauses()
    {
        \$this->command->info('📋 Importando cláusulas de juguetes...');
        
        \$clauses = {$toyClauses};

        foreach (\$clauses as \$clause) {
            ToyClause::create(\$clause);
        }
    }

    private function seedGameToyClauseRelations()
    {
        \$this->command->info('🔗 Importando relaciones juego-cláusulas...');
        
        \$relations = {$gameClauseRelations};

        if (!empty(\$relations)) {
            DB::table('game_toy_clause')->insert(\$relations);
        }
    }

    private function seedPackages()
    {
        \$this->command->info('📦 Importando paquetes...');
        
        \$packages = {$packages};

        foreach (\$packages as \$package) {
            Package::create(\$package);
        }
    }

    private function seedPackageGameRelations()
    {
        \$this->command->info('🔗 Importando relaciones paquete-juegos...');
        
        \$relations = {$packageGameRelations};

        if (!empty(\$relations)) {
            DB::table('package_games')->insert(\$relations);
        }
    }

    private function seedEventSettings()
    {
        \$this->command->info('⚙️ Importando configuraciones de eventos...');
        
        \$settings = {$eventSettings};

        if (!empty(\$settings)) {
            foreach (\$settings as \$setting) {
                EventSetting::create(\$setting);
            }
        }
    }

    private function seedEvents()
    {
        \$this->command->info('📅 Importando eventos...');
        
        \$events = {$events};

        foreach (\$events as \$event) {
            Event::create(\$event);
        }
    }

    private function seedExpenseCategories()
    {
        \$this->command->info('💰 Importando categorías de gastos...');
        
        \$categories = {$expenseCategories};

        foreach (\$categories as \$category) {
            ExpenseCategory::create(\$category);
        }
    }

    private function seedExpenses()
    {
        \$this->command->info('💸 Importando gastos...');
        
        \$expenses = {$expenses};

        foreach (\$expenses as \$expense) {
            Expense::create(\$expense);
        }
    }

    private function seedVehicles()
    {
        \$this->command->info('🚗 Importando vehículos...');
        
        \$vehicles = {$vehicles};

        foreach (\$vehicles as \$vehicle) {
            Vehicle::create(\$vehicle);
        }
    }

    private function seedEventVehicleUsage()
    {
        \$this->command->info('⛽ Importando uso de vehículos...');
        
        \$usage = {$eventVehicleUsage};

        foreach (\$usage as \$item) {
            EventVehicleUsage::create(\$item);
        }
    }

    private function seedFuelPrices()
    {
        \$this->command->info('⛽ Importando precios de combustible...');
        
        \$prices = {$fuelPrices};

        foreach (\$prices as \$price) {
            FuelPrice::create(\$price);
        }
    }
}
PHP;
    }

    private function exportToyTypes(): string
    {
        $toyTypes = ToyType::all()->toArray();
        return $this->arrayToPhpString($toyTypes);
    }

    private function exportGames(): string
    {
        $games = Game::all()->toArray();
        return $this->arrayToPhpString($games);
    }

    private function exportToyClauses(): string
    {
        $clauses = ToyClause::all()->toArray();
        return $this->arrayToPhpString($clauses);
    }

    private function exportGameClauseRelations(): string
    {
        $relations = DB::table('game_toy_clause')->get()->toArray();
        return $this->arrayToPhpString($relations);
    }

    private function exportPackages(): string
    {
        $packages = Package::all()->toArray();
        return $this->arrayToPhpString($packages);
    }

    private function exportPackageGameRelations(): string
    {
        $relations = DB::table('package_games')->get()->toArray();
        return $this->arrayToPhpString($relations);
    }

    private function exportEventSettings(): string
    {
        $settings = EventSetting::all()->toArray();
        return $this->arrayToPhpString($settings);
    }

    private function exportEvents(): string
    {
        $events = Event::all()->toArray();
        return $this->arrayToPhpString($events);
    }

    private function exportExpenseCategories(): string
    {
        $categories = ExpenseCategory::all()->toArray();
        return $this->arrayToPhpString($categories);
    }

    private function exportExpenses(): string
    {
        $expenses = Expense::all()->toArray();
        return $this->arrayToPhpString($expenses);
    }

    private function exportVehicles(): string
    {
        $vehicles = Vehicle::all()->toArray();
        return $this->arrayToPhpString($vehicles);
    }

    private function exportEventVehicleUsage(): string
    {
        $usage = EventVehicleUsage::all()->toArray();
        return $this->arrayToPhpString($usage);
    }

    private function exportFuelPrices(): string
    {
        $prices = FuelPrice::all()->toArray();
        return $this->arrayToPhpString($prices);
    }

    private function arrayToPhpString(array $array): string
    {
        return var_export($array, true);
    }

    private function getCurrentDateTime(): string
    {
        return now()->format('Y-m-d H:i:s');
    }
}
