<?php

declare(strict_types=1);

namespace App\Domain\Performance\Models;

use Illuminate\Database\Eloquent\Model;

class LighthouseAudit extends Model
{
    protected $fillable = [
        'url',
        'device',
        'performance',
        'accessibility',
        'best_practices',
        'seo',
        'pwa',
        'report_path',
        'audited_at',
    ];

    protected $casts = [
        'performance' => 'float',
        'accessibility' => 'float',
        'best_practices' => 'float',
        'seo' => 'float',
        'pwa' => 'float',
        'audited_at' => 'datetime',
    ];
}
