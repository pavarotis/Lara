<?php

declare(strict_types=1);

namespace App\Domain\Layouts\Services;

use App\Domain\Layouts\Models\Layout;
use Illuminate\Support\Facades\DB;

class CreateLayoutService
{
    public function execute(array $data): Layout
    {
        return DB::transaction(function () use ($data) {
            // Set defaults
            $data['type'] = $data['type'] ?? 'default';
            $data['regions'] = $data['regions'] ?? [];
            $data['is_default'] = $data['is_default'] ?? false;

            // If this is set as default, unset other defaults for this business
            if ($data['is_default']) {
                Layout::forBusiness($data['business_id'])
                    ->update(['is_default' => false]);
            }

            return Layout::create($data);
        });
    }
}
