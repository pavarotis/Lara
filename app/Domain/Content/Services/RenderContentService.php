<?php

declare(strict_types=1);

namespace App\Domain\Content\Services;

use App\Domain\Content\Models\Content;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;

/**
 * RenderContentService
 *
 * Renders content blocks to HTML based on theme.
 */
class RenderContentService
{
    /**
     * Render full content (array of blocks)
     */
    public function render(Content $content): string
    {
        $blocks = $content->body_json ?? [];
        $renderedBlocks = [];

        foreach ($blocks as $block) {
            $renderedBlocks[] = $this->renderBlock($block, $content->business);
        }

        return implode("\n", $renderedBlocks);
    }

    /**
     * Render single block
     */
    public function renderBlock(array $block, $business): string
    {
        $blockType = $block['type'] ?? null;
        $blockProps = $block['props'] ?? [];

        if (! $blockType) {
            return '';
        }

        // Resolve theme from business
        $theme = $this->resolveTheme($business);

        // Try theme-specific block view first
        $viewPath = "themes.{$theme}.blocks.{$blockType}";

        // Fallback to default theme if view doesn't exist
        if (! View::exists($viewPath)) {
            $viewPath = "themes.default.blocks.{$blockType}";
        }

        // If still doesn't exist, return fallback message
        if (! View::exists($viewPath)) {
            return "<!-- Block type '{$blockType}' not found -->";
        }

        // Render block view with props
        try {
            return View::make($viewPath, $blockProps)->render();
        } catch (\Exception $e) {
            // Log error but don't break rendering
            Log::warning("Failed to render block {$blockType}: ".$e->getMessage());

            return "<!-- Error rendering block '{$blockType}' -->";
        }
    }

    /**
     * Resolve theme from business
     */
    private function resolveTheme($business): string
    {
        if (! $business) {
            return 'default';
        }

        // Get theme from business settings
        $theme = $business->getTheme();

        // Validate theme exists (check if theme folder exists)
        $themePath = resource_path("views/themes/{$theme}");
        if (! is_dir($themePath)) {
            return 'default';
        }

        return $theme;
    }

    /**
     * Execute method (alias for render, for backward compatibility)
     */
    public function execute(Content $content): string
    {
        return $this->render($content);
    }
}
