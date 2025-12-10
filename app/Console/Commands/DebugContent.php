<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Domain\Businesses\Models\Business;
use App\Domain\Content\Models\Content;
use App\Domain\Content\Services\GetContentService;
use Illuminate\Console\Command;

class DebugContent extends Command
{
    protected $signature = 'debug:content {slug?}';

    protected $description = 'Debug content by slug';

    public function handle(): int
    {
        $slug = $this->argument('slug') ?? '/';
        $business = Business::active()->first();

        if (! $business) {
            $this->error('No active business found');

            return Command::FAILURE;
        }

        $this->info("Active Business ID: {$business->id}");

        // Check content directly
        $content = Content::where('business_id', $business->id)
            ->where('slug', $slug)
            ->first();

        if (! $content) {
            $this->error("No content found with slug: {$slug}");

            return Command::FAILURE;
        }

        $this->info('Content found:');
        $this->line("  ID: {$content->id}");
        $this->line("  Title: {$content->title}");
        $this->line("  Slug: {$content->slug}");
        $this->line("  Status: {$content->status}");
        $this->line('  Published At: '.($content->published_at ? $content->published_at->toDateTimeString() : 'NULL'));
        $this->line("  Business ID: {$content->business_id}");

        // Check if published
        $isPublished = $content->status === 'published' && $content->published_at !== null;
        $this->line('  Is Published: '.($isPublished ? 'YES' : 'NO'));

        // Check via service
        $service = new GetContentService;
        $serviceContent = $service->bySlug($business->id, $slug);

        if ($serviceContent) {
            $this->info("Service found content: {$serviceContent->id}");
        } else {
            $this->error('Service did NOT find content (published() scope issue)');
        }

        return Command::SUCCESS;
    }
}
