<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Domain\Content\Models\Content;
use Illuminate\Console\Command;

class FixPublishedAt extends Command
{
    protected $signature = 'fix:published-at';

    protected $description = 'Fix published_at for published content';

    public function handle(): int
    {
        $contents = Content::where('status', 'published')
            ->whereNull('published_at')
            ->get();

        if ($contents->isEmpty()) {
            $this->info('No content needs fixing');

            return Command::SUCCESS;
        }

        $this->info("Found {$contents->count()} content(s) to fix");

        foreach ($contents as $content) {
            $content->published_at = now();
            $content->save();
            $this->line("  Fixed: {$content->title} (ID: {$content->id})");
        }

        $this->info('Done!');

        return Command::SUCCESS;
    }
}
