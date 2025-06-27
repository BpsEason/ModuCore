<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

/**
 * Class MakeModule
 * @package App\Console\Commands
 *
 * Artisan 命令：用於快速創建模組結構。
 *
 * 使用範例：
 * php artisan make:module Payment
 * php artisan make:module Product --all
 * php artisan make:module Blog --only-controller --only-model
 */
class MakeModule extends Command
{
    /**
     * 命令的名稱和簽名。
     *
     * @var string
     */
    protected $signature = 'make:module {name}
                                       {--all : 生成控制器、服務、模型、路由、合約、提供者、遷移和配置檔案}
                                       {--controller : 生成控制器}
                                       {--service : 生成服務}
                                       {--model : 生成模型}
                                       {--routes : 生成路由檔案}
                                       {--contract : 生成合約介面}
                                       {--provider : 生成服務提供者}
                                       {--migration : 生成遷移檔案}
                                       {--config : 生成模組配置檔案}
                                       {--test : 生成測試檔案}'; # Added --test option

    /**
     * 命令的描述。
     *
     * @var string
     */
    protected $description = '創建模組的骨架 (控制器、服務、模型、路由、合約、提供者、遷移、配置和測試檔案)'; # Updated description

    /**
     * 執行命令。
     *
     * @return int
     */
    public function handle(): int
    {
        $name = Str::studly($this->argument('name')); // 確保模組名稱是 StudlyCase
        $modulePath = app_path("Modules/{$name}");

        // 檢查模組是否已存在
        if (File::isDirectory($modulePath)) {
            $this->error("模組 [{$name}] 已經存在！");
            return Command::FAILURE;
        }

        // 建立模組主目錄
        File::makeDirectory($modulePath, 0755, true);
        $this->info("模組目錄 [{$modulePath}] 已建立。");

        // 判斷需要生成哪些組件
        $all = $this->option('all');
        $shouldMakeController = $this->option('controller') || $all;
        $shouldMakeService = $this->option('service') || $all;
        $shouldMakeModel = $this->option('model') || $all;
        $shouldMakeRoutes = $this->option('routes') || $all;
        $shouldMakeContract = $this->option('contract') || $all;
        $shouldMakeProvider = $this->option('provider') || $all;
        $shouldMakeMigration = $this->option('migration') || $all;
        $shouldMakeConfig = $this->option('config') || $all;
        $shouldMakeTest = $this->option('test') || $all; # Added test option

        // 如果沒有指定任何選項且沒有 --all，則預設生成所有核心組件
        if (!$shouldMakeController && !$shouldMakeService && !$shouldMakeModel && !$shouldMakeRoutes && !$shouldMakeContract && !$shouldMakeProvider && !$shouldMakeMigration && !$shouldMakeConfig && !$shouldMakeTest && !$all) {
            $shouldMakeController = true;
            $shouldMakeService = true;
            $shouldMakeModel = true;
            $shouldMakeRoutes = true;
            $shouldMakeProvider = true;
            $shouldMakeMigration = true;
            $shouldMakeConfig = true; // 預設也生成配置檔案
            $shouldMakeTest = true; // 預設也生成測試檔案
        }

        // 生成控制器
        if ($shouldMakeController) {
            $this->createController($name, $modulePath);
        }

        // 生成服務
        if ($shouldMakeService) {
            $this->createService($name, $modulePath);
        }

        // 生成模型
        if ($shouldMakeModel) {
            $this->createModel($name, $modulePath);
        }

        // 生成路由檔案
        if ($shouldMakeRoutes) {
            $this->createRoutes($name, $modulePath);
        }

        // 生成合約介面
        if ($shouldMakeContract) {
            $this->createContract($name, $modulePath);
        }
        
        // 生成服務提供者
        if ($shouldMakeProvider) {
            $this->createProvider($name, $modulePath);
        }

        // 生成遷移檔案
        if ($shouldMakeMigration) {
            $this->createMigration($name, $modulePath);
        }

        // 生成模組配置檔案
        if ($shouldMakeConfig) {
            $this->createConfig($name, $modulePath);
        }

        // 生成測試檔案
        if ($shouldMakeTest) {
            $this->createTest($name, $modulePath);
        }

        $this->info("模組 [{$name}] 骨架已成功生成！");
        $this->warn("請記得在 config/modules.php 中註冊此模組的服務提供者。");

        return Command::SUCCESS;
    }

    /**
     * 建立控制器檔案。
     *
     * @param string $name 模組名稱
     * @param string $modulePath 模組路徑
     * @return void
     */
    protected function createController(string $name, string $modulePath): void
    {
        $controllerDir = "{$modulePath}/Controllers";
        File::makeDirectory($controllerDir, 0755, true);

        $stub = File::get(__DIR__ . '/stubs/module.controller.stub');
        $stub = str_replace(
            ['{{ ModuleName }}', '{{ ModuleNameLower }}'],
            [$name, Str::lcfirst($name)],
            $stub
        );

        $path = "{$controllerDir}/{$name}Controller.php";
        File::put($path, $stub);
        $this->info("  控制器 [{$path}] 已建立。");
    }

    /**
     * 建立服務檔案。
     *
     * @param string $name 模組名稱
     * @param string $modulePath 模組路徑
     * @return void
     */
    protected function createService(string $name, string $modulePath): void
    {
        $serviceDir = "{$modulePath}/Services";
        File::makeDirectory($serviceDir, 0755, true);

        $stub = File::get(__DIR__ . '/stubs/module.service.stub');
        $stub = str_replace(['{{ ModuleName }}', '{{ ModuleNameLower }}'], [$name, Str::lcfirst($name)], $stub);

        $path = "{$serviceDir}/{$name}Service.php";
        File::put($path, $stub);
        $this->info("  服務 [{$path}] 已建立。");
    }

    /**
     * 建立模型檔案。
     *
     * @param string $name 模組名稱
     * @param string $modulePath 模組路徑
     * @return void
     */
    protected function createModel(string $name, string $modulePath): void
    {
        $modelDir = "{$modulePath}/Models";
        File::makeDirectory($modelDir, 0755, true);

        $stub = File::get(__DIR__ . '/stubs/module.model.stub');
        $stub = str_replace(
            ['{{ ModuleName }}', '{{ ModuleNameLower }}s'],
            [$name, Str::snake(Str::plural($name))],
            $stub
        );

        $path = "{$modelDir}/{$name}.php";
        File::put($path, $stub);
        $this->info("  模型 [{$path}] 已建立。");
    }

    /**
     * 建立路由檔案。
     *
     * @param string $name 模組名稱
     * @param string $modulePath 模組路徑
     * @return void
     */
    protected function createRoutes(string $name, string $modulePath): void
    {
        $stub = File::get(__DIR__ . '/stubs/module.routes.stub');
        $stub = str_replace(
            ['{{ ModuleName }}', '{{ ModuleNameLower }}'],
            [$name, Str::lcfirst($name)],
            $stub
        );

        $path = "{$modulePath}/routes.php";
        File::put($path, $stub);
        $this->info("  路由檔案 [{$path}] 已建立。");
    }

    /**
     * 建立合約介面檔案。
     *
     * @param string $name 模組名稱
     * @param string $modulePath 模組路徑
     * @return void
     */
    protected function createContract(string $name, string $modulePath): void
    {
        $contractDir = "{$modulePath}/Contracts";
        File::makeDirectory($contractDir, 0755, true);

        $stub = File::get(__DIR__ . '/stubs/module.contract.stub');
        $stub = str_replace('{{ ModuleName }}', $name, $stub);

        $path = "{$contractDir}/{$name}Interface.php"; // Changed to Interface
        File::put($path, $stub);
        $this->info("  合約介面 [{$path}] 已建立。");
    }

    /**
     * 生成模組專屬服務提供者。
     *
     * @param string $name 模組名稱
     * @param string $modulePath 模組路徑
     * @return void
     */
    protected function createProvider(string $name, string $modulePath): void
    {
        $providerDir = "{$modulePath}/Providers";
        File::makeDirectory($providerDir, 0755, true);
        $stub = File::get(__DIR__ . '/stubs/module.provider.stub');
        $stub = str_replace('{{ ModuleName }}', $name, $stub);
        $path = "{$providerDir}/{$name}ServiceProvider.php";
        File::put($path, $stub);
        $this->info("  服務提供者 [{$path}] 已建立。");
    }

    /**
     * 在 MakeModule.php 中添加生成遷移檔案的邏輯。
     *
     * @param string $name 模組名稱
     * @param string $modulePath 模組路徑
     * @return void
     */
    protected function createMigration(string $name, string $modulePath): void
    {
        $migrationDir = base_path('database/migrations');
        $timestamp = now()->format('Y_m_d_His');
        $tableName = Str::snake(Str::plural($name));
        $stub = File::get(__DIR__ . '/stubs/module.migration.stub');

        // Dynamically replace table columns based on module name
        $tableColumns = '';
        if ($name === 'Payment') {
            $tableColumns = "
            \$table->foreignId('user_id')->constrained()->onDelete('cascade');
            \$table->string('transaction_id')->unique();
            \$table->decimal('amount', 12, 2);
            \$table->string('status')->default('pending');
            \$table->string('gateway')->default('ecpay');
            ";
        } elseif ($name === 'Sms') {
            $tableColumns = "
            \$table->foreignId('user_id')->constrained()->onDelete('cascade');
            \$table->string('phone_number');
            \$table->text('message');
            \$table->string('status')->default('pending');
            \$table->string('gateway')->default('twilio');
            ";
        } else {
            // Default columns for other modules
            $tableColumns = "
            \$table->string('name');
            \$table->text('description')->nullable();
            \$table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            ";
        }

        $stub = str_replace(['{{ TableName }}', '{{ ModuleName }}', '{{ TableColumns }}'], [$tableName, $name, $tableColumns], $stub);
        $path = "{$migrationDir}/{$timestamp}_create_{$tableName}_table.php";
        File::put($path, $stub);
        $this->info("  遷移檔案 [{$path}] 已建立。");
    }

    /**
     * 建立模組配置檔案。
     *
     * @param string $name 模組名稱
     * @param string $modulePath 模組路徑
     * @return void
     */
    protected function createConfig(string $name, string $modulePath): void
    {
        $configDir = "{$modulePath}/Config";
        File::makeDirectory($configDir, 0755, true);

        $stub = File::get(__DIR__ . '/stubs/module.config.stub');
        $stub = str_replace('{{ ModuleNameLower }}', Str::lcfirst($name), $stub);

        $path = "{$configDir}/config.php";
        File::put($path, $stub);
        $this->info("  配置檔案 [{$path}] 已建立。");
    }

    /**
     * 建立模組測試檔案。
     *
     * @param string $name 模組名稱
     * @param string $modulePath 模組路徑
     * @return void
     */
    protected function createTest(string $name, string $modulePath): void
    {
        $testDir = base_path('tests/Feature');
        File::makeDirectory($testDir, 0755, true);
        $stub = File::get(__DIR__ . '/stubs/module.test.stub');
        $stub = str_replace(['{{ ModuleName }}', '{{ ModuleNameLower }}'], [$name, Str::lcfirst($name)], $stub);
        $path = "{$testDir}/{$name}ModuleTest.php"; # Changed to ModuleTest.php for clarity
        File::put($path, $stub);
        $this->info("  測試檔案 [{$path}] 已建立。");
    }
}
