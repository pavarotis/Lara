<?php

namespace App\Filament\Pages\System\Maintenance;

use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;

class BackupRestore extends Page
{
    protected static string|\UnitEnum|null $navigationGroup = 'System';

    protected static ?int $navigationSort = 17;

    protected string $view = 'filament.pages.system.maintenance.backup-restore';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-arrow-path';

    protected static ?string $navigationLabel = 'Backup / Restore';

    public function getTitle(): string
    {
        return 'Backup / Restore';
    }

    /**
     * Find mysqldump executable path
     * Tries common Laragon paths and system PATH
     */
    protected function findMysqldumpPath(): ?string
    {
        // Check if mysqldump is in system PATH (Windows)
        if (PHP_OS_FAMILY === 'Windows') {
            $process = Process::run(['where.exe', 'mysqldump']);
            if ($process->successful()) {
                $output = trim($process->output());
                if (! empty($output)) {
                    $paths = explode("\n", $output);
                    foreach ($paths as $path) {
                        $path = trim($path);
                        if (! empty($path) && File::exists($path)) {
                            return $path;
                        }
                    }
                }
            }
        } else {
            // Linux/Mac - use which
            $process = Process::run(['which', 'mysqldump']);
            if ($process->successful()) {
                $path = trim($process->output());
                if (! empty($path) && File::exists($path)) {
                    return $path;
                }
            }
        }

        // Try Laragon common paths (Windows)
        if (PHP_OS_FAMILY === 'Windows') {
            $laragonBase = 'C:\\laragon\\bin\\mysql';
            if (is_dir($laragonBase)) {
                // Find MySQL directories
                $mysqlDirs = glob($laragonBase.'\\mysql-*', GLOB_ONLYDIR);
                foreach ($mysqlDirs as $mysqlDir) {
                    $mysqldumpPath = $mysqlDir.'\\bin\\mysqldump.exe';
                    if (File::exists($mysqldumpPath)) {
                        return $mysqldumpPath;
                    }
                }
            }

            // Try MariaDB in Laragon
            $mariaBase = 'C:\\laragon\\bin\\mariadb';
            if (is_dir($mariaBase)) {
                $mariaDirs = glob($mariaBase.'\\mariadb-*', GLOB_ONLYDIR);
                foreach ($mariaDirs as $mariaDir) {
                    $mysqldumpPath = $mariaDir.'\\bin\\mysqldump.exe';
                    if (File::exists($mysqldumpPath)) {
                        return $mysqldumpPath;
                    }
                }
            }

            // Try XAMPP paths
            $xamppPaths = [
                'C:\\xampp\\mysql\\bin\\mysqldump.exe',
                'C:\\Program Files\\xampp\\mysql\\bin\\mysqldump.exe',
            ];
            foreach ($xamppPaths as $xamppPath) {
                if (File::exists($xamppPath)) {
                    return $xamppPath;
                }
            }
        }

        return null;
    }

    public function createDatabaseBackup(): void
    {
        try {
            $filename = 'backup-db-'.now()->format('Y-m-d-His').'.sql';
            $backupPath = storage_path('app/backups');

            // Create backups directory if it doesn't exist
            if (! File::exists($backupPath)) {
                File::makeDirectory($backupPath, 0755, true);
            }

            $fullPath = $backupPath.DIRECTORY_SEPARATOR.$filename;

            // Get database credentials
            $dbName = config('database.connections.mysql.database');
            $dbUser = config('database.connections.mysql.username');
            $dbPass = config('database.connections.mysql.password');
            $dbHost = config('database.connections.mysql.host');
            $dbPort = config('database.connections.mysql.port', 3306);

            // Validate database config
            if (empty($dbName) || empty($dbUser) || empty($dbHost)) {
                throw new \Exception('Database configuration is incomplete');
            }

            // Find mysqldump executable
            $mysqldumpPath = $this->findMysqldumpPath();
            if (! $mysqldumpPath) {
                throw new \Exception('mysqldump executable not found. Please ensure MySQL is installed and mysqldump is accessible.');
            }

            // Build mysqldump command arguments
            // Use Process facade for better error handling and cross-platform support
            // Set password via environment variable for security
            $process = Process::env([
                'MYSQL_PWD' => $dbPass,
            ])->run([
                $mysqldumpPath,
                '-h', $dbHost,
                '-P', (string) $dbPort,
                '-u', $dbUser,
                '--single-transaction',
                '--quick',
                '--lock-tables=false',
                $dbName,
            ]);

            if (! $process->successful()) {
                $errorOutput = $process->errorOutput();
                throw new \Exception('mysqldump failed: '.($errorOutput ?: $process->output()));
            }

            // Write output to file
            $output = $process->output();

            if (empty($output)) {
                throw new \Exception('mysqldump produced no output. Check database credentials and permissions.');
            }

            // Write to file
            File::put($fullPath, $output);

            // Verify file was created and has content
            if (! File::exists($fullPath)) {
                throw new \Exception('Backup file was not created');
            }

            $fileSize = File::size($fullPath);
            if ($fileSize === 0) {
                File::delete($fullPath);
                throw new \Exception('Backup file is empty. Check database connection and permissions.');
            }

            Notification::make()
                ->title('Database Backup Created')
                ->success()
                ->body("Backup saved: {$filename} (".number_format($fileSize / 1024, 2).' KB)')
                ->send();

        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();

            // Provide more helpful error messages
            if (str_contains($errorMessage, 'mysqldump executable not found')) {
                $errorMessage = 'mysqldump not found. Please ensure MySQL/MariaDB is installed. For Laragon, make sure MySQL is running.';
            } elseif (str_contains($errorMessage, 'Access denied') || str_contains($errorMessage, '1045')) {
                $errorMessage = 'Database access denied. Check your database credentials in .env file.';
            } elseif (str_contains($errorMessage, 'Unknown database') || str_contains($errorMessage, '1049')) {
                $errorMessage = 'Database not found. Check your database name in .env file.';
            } elseif (str_contains($errorMessage, 'Can\'t connect') || str_contains($errorMessage, '2002')) {
                $errorMessage = 'Cannot connect to MySQL server. Make sure MySQL is running.';
            }

            Notification::make()
                ->title('Backup Failed')
                ->danger()
                ->body($errorMessage)
                ->send();
        }
    }

    public function createFilesBackup(): void
    {
        try {
            $filename = 'backup-files-'.now()->format('Y-m-d-His').'.zip';
            $backupPath = storage_path('app/backups');

            // Create backups directory if it doesn't exist
            if (! File::exists($backupPath)) {
                File::makeDirectory($backupPath, 0755, true);
            }

            $fullPath = $backupPath.'/'.$filename;

            // Create zip archive
            $zip = new \ZipArchive;
            if ($zip->open($fullPath, \ZipArchive::CREATE) !== true) {
                throw new \Exception('Cannot create zip file');
            }

            // Add storage/app/public to backup
            $publicPath = storage_path('app/public');
            if (File::exists($publicPath)) {
                $this->addDirectoryToZip($zip, $publicPath, 'storage/app/public');
            }

            // Add .env file (important!)
            $envPath = base_path('.env');
            if (File::exists($envPath)) {
                $zip->addFile($envPath, '.env');
            }

            $zip->close();

            if (File::exists($fullPath)) {
                Notification::make()
                    ->title('Files Backup Created')
                    ->success()
                    ->body("Backup saved: {$filename}")
                    ->send();
            } else {
                throw new \Exception('Backup file was not created');
            }
        } catch (\Exception $e) {
            Notification::make()
                ->title('Backup Failed')
                ->danger()
                ->body('Error: '.$e->getMessage())
                ->send();
        }
    }

    protected function addDirectoryToZip(\ZipArchive $zip, string $dir, string $zipPath = ''): void
    {
        $files = File::allFiles($dir);

        foreach ($files as $file) {
            $relativePath = $zipPath.'/'.$file->getRelativePathname();
            $zip->addFile($file->getPathname(), $relativePath);
        }
    }

    public function deleteBackup(string $filename): void
    {
        try {
            $backupPath = storage_path('app/backups/'.$filename);

            if (File::exists($backupPath)) {
                File::delete($backupPath);

                Notification::make()
                    ->title('Backup Deleted')
                    ->success()
                    ->body("Backup {$filename} has been deleted.")
                    ->send();
            } else {
                throw new \Exception('Backup file not found');
            }
        } catch (\Exception $e) {
            Notification::make()
                ->title('Delete Failed')
                ->danger()
                ->body('Error: '.$e->getMessage())
                ->send();
        }
    }

    public function downloadBackup(string $filename)
    {
        $backupPath = storage_path('app/backups/'.$filename);

        if (File::exists($backupPath)) {
            return response()->download($backupPath, $filename);
        }

        Notification::make()
            ->title('Download Failed')
            ->danger()
            ->body('Backup file not found.')
            ->send();

        return null;
    }

    public function getBackups(): array
    {
        $backupPath = storage_path('app/backups');

        if (! File::exists($backupPath)) {
            return [];
        }

        $files = File::files($backupPath);
        $backups = [];

        foreach ($files as $file) {
            $backups[] = [
                'filename' => $file->getFilename(),
                'size' => $file->getSize(),
                'created_at' => File::lastModified($file->getPathname()),
                'type' => str_contains($file->getFilename(), 'backup-db-') ? 'database' : 'files',
            ];
        }

        // Sort by created_at descending
        usort($backups, fn ($a, $b) => $b['created_at'] <=> $a['created_at']);

        return $backups;
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('createDatabaseBackup')
                ->label('Create Database Backup')
                ->icon('heroicon-o-circle-stack')
                ->color('primary')
                ->action('createDatabaseBackup')
                ->requiresConfirmation()
                ->modalHeading('Create Database Backup')
                ->modalDescription('This will create a SQL dump of your database. Continue?')
                ->modalSubmitActionLabel('Create Backup'),

            Action::make('createFilesBackup')
                ->label('Create Files Backup')
                ->icon('heroicon-o-folder')
                ->color('success')
                ->action('createFilesBackup')
                ->requiresConfirmation()
                ->modalHeading('Create Files Backup')
                ->modalDescription('This will create a ZIP archive of your files. Continue?')
                ->modalSubmitActionLabel('Create Backup'),
        ];
    }
}
