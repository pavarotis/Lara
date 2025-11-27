<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;

class OptimizeForProduction extends Command
{
    protected $signature = 'app:optimize-production';
    protected $description = 'Optimize the application for production deployment';

    public function handle(): int
    {
        $this->info('🚀 Optimizing LaraShop for production...');
        $this->newLine();

        // Clear existing caches
        $this->info('Clearing existing caches...');
        $this->call('cache:clear');
        $this->call('config:clear');
        $this->call('route:clear');
        $this->call('view:clear');

        $this->newLine();

        // Rebuild caches
        $this->info('Building optimized caches...');
        $this->call('config:cache');
        $this->call('route:cache');
        $this->call('view:cache');

        $this->newLine();

        // Optimize autoloader
        $this->info('Optimizing Composer autoloader...');
        exec('composer dump-autoload --optimize --no-dev 2>&1', $output, $returnCode);
        
        if ($returnCode === 0) {
            $this->info('✓ Autoloader optimized');
        } else {
            $this->warn('⚠ Could not optimize autoloader (run manually: composer dump-autoload -o)');
        }

        $this->newLine();
        $this->info('✅ Production optimization complete!');
        $this->newLine();

        $this->table(['Optimization', 'Status'], [
            ['Config Cache', '✓ Cached'],
            ['Route Cache', '✓ Cached'],
            ['View Cache', '✓ Cached'],
            ['Autoloader', $returnCode === 0 ? '✓ Optimized' : '⚠ Manual'],
        ]);

        return Command::SUCCESS;
    }
}

