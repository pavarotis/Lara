<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Domain\Content\Models\ContentType;
use Illuminate\Database\Seeder;

class ContentTypeSeeder extends Seeder
{
    /**
     * Seed default content types
     */
    public function run(): void
    {
        $types = [
            [
                'name' => 'Page',
                'slug' => 'page',
                'field_definitions' => null,
            ],
            [
                'name' => 'Article',
                'slug' => 'article',
                'field_definitions' => null,
            ],
            [
                'name' => 'Block',
                'slug' => 'block',
                'field_definitions' => null,
            ],
        ];

        foreach ($types as $type) {
            ContentType::firstOrCreate(
                ['slug' => $type['slug']],
                $type
            );
        }
    }
}
