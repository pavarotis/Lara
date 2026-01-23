<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class TestGoogleFontsApi extends Command
{
    protected $signature = 'google-fonts:test {--key= : Override API key for testing}';

    protected $description = 'Test Google Fonts API connection and configuration

Examples:
  php artisan google-fonts:test
  php artisan google-fonts:test --key=YOUR_API_KEY_HERE

Manual testing with curl:
  # Without API key:
  curl "https://www.googleapis.com/webfonts/v1/webfonts?sort=popularity"
  
  # With API key:
  curl "https://www.googleapis.com/webfonts/v1/webfonts?sort=popularity&key=YOUR_API_KEY"';

    public function handle(): int
    {
        $this->info('🔍 Testing Google Fonts API Connection...');
        $this->newLine();

        // Check API key configuration
        $apiKey = $this->option('key') ?: config('services.google_fonts.api_key');

        $this->line('📋 Configuration:');
        $this->table(['Setting', 'Value'], [
            ['API Key', $apiKey ? '✓ Configured' : '✗ Not configured'],
            ['API Key Length', $apiKey ? strlen($apiKey).' characters' : 'N/A'],
        ]);
        $this->newLine();

        // Build API URL
        $url = 'https://www.googleapis.com/webfonts/v1/webfonts';
        $params = ['sort' => 'popularity'];

        if ($apiKey) {
            $params['key'] = $apiKey;
            $this->info('🔑 Using API key from config');
        } else {
            $this->warn('⚠️  No API key configured (may have rate limits)');
        }
        $this->newLine();

        // Test connection
        $this->info('🌐 Testing API connection...');

        try {
            $startTime = microtime(true);
            $response = Http::timeout(10)->get($url, $params);
            $duration = round((microtime(true) - $startTime) * 1000, 2);

            $this->line("⏱️  Response time: {$duration}ms");
            $this->newLine();

            if ($response->successful()) {
                $data = $response->json();

                if (isset($data['items']) && is_array($data['items'])) {
                    $fontCount = count($data['items']);
                    $this->info('✅ Connection successful!');
                    $this->line("📊 Total fonts available: {$fontCount}");
                    $this->newLine();

                    // Filter fonts with latin or greek
                    $latinFonts = 0;
                    $greekFonts = 0;
                    foreach ($data['items'] as $font) {
                        if (isset($font['subsets']) && is_array($font['subsets'])) {
                            if (in_array('latin', $font['subsets'])) {
                                $latinFonts++;
                            }
                            if (in_array('greek', $font['subsets'])) {
                                $greekFonts++;
                            }
                        }
                    }

                    $this->line('📈 Fonts by subset:');
                    $this->table(['Subset', 'Count'], [
                        ['Latin', $latinFonts],
                        ['Greek', $greekFonts],
                    ]);
                    $this->newLine();

                    // Show first 5 fonts as sample
                    $this->line('📝 Sample fonts (first 5):');
                    $sampleFonts = array_slice($data['items'], 0, 5);
                    $fontList = [];
                    foreach ($sampleFonts as $font) {
                        $fontList[] = [
                            $font['family'] ?? 'N/A',
                            isset($font['subsets']) && is_array($font['subsets']) ? implode(', ', array_slice($font['subsets'], 0, 3)) : 'N/A',
                        ];
                    }
                    $this->table(['Font Family', 'Subsets'], $fontList);
                    $this->newLine();

                    // Cache test
                    $this->info('💾 Testing cache...');
                    Cache::put('google_fonts_test', $data, 3600);
                    $cached = Cache::get('google_fonts_test');
                    if ($cached) {
                        $this->info('✅ Cache working correctly');
                    } else {
                        $this->warn('⚠️  Cache may not be working');
                    }
                    $this->newLine();

                    $this->info('🎉 All tests passed!');

                    return Command::SUCCESS;
                } else {
                    $this->error('❌ API returned invalid data structure');
                    $this->line('Response: '.json_encode($data, JSON_PRETTY_PRINT));

                    return Command::FAILURE;
                }
            } else {
                $statusCode = $response->status();
                $errorBody = $response->json();

                $this->error("❌ API request failed (Status: {$statusCode})");
                $this->newLine();

                if ($statusCode === 403) {
                    $this->error('🔒 Access Forbidden (403)');
                    $this->line('This usually means:');
                    $this->line('  • API key is invalid');
                    $this->line('  • API is not enabled in Google Cloud Console');
                    $this->line('  • API key has restrictions that block this request');
                } elseif ($statusCode === 400) {
                    $this->error('❌ Bad Request (400)');
                    $this->line('Check your API key configuration.');
                } elseif ($statusCode === 429) {
                    $this->error('⏱️  Rate Limit Exceeded (429)');
                    $this->line('Too many requests. Please wait a few minutes.');
                } else {
                    $errorMessage = $errorBody['error']['message'] ?? 'Unknown error';
                    $this->error("Error: {$errorMessage}");
                }

                if ($errorBody) {
                    $this->newLine();
                    $this->line('Full error response:');
                    $this->line(json_encode($errorBody, JSON_PRETTY_PRINT));
                }

                return Command::FAILURE;
            }
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            $this->error('❌ Connection failed');
            $this->line('Error: '.$e->getMessage());
            $this->newLine();
            $this->line('Possible causes:');
            $this->line('  • No internet connection');
            $this->line('  • Firewall blocking the request');
            $this->line('  • DNS issues');

            return Command::FAILURE;
        } catch (\Exception $e) {
            $this->error('❌ Unexpected error');
            $this->line('Error: '.$e->getMessage());

            return Command::FAILURE;
        }
    }
}
