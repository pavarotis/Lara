<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Domain\Businesses\Models\Business;
use App\Domain\Content\Models\Content;
use App\Domain\Content\Services\GetContentService;
use App\Domain\Content\Services\RenderContentService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ContentController extends Controller
{
    public function __construct(
        private GetContentService $getContentService,
        private RenderContentService $renderContentService
    ) {}

    /**
     * Show public content by slug
     */
    public function show(string $slug): View|Response
    {
        $business = Business::active()->firstOrFail();

        $content = $this->getContentService->bySlug($business->id, $slug);

        if (! $content) {
            abort(404, 'Content not found');
        }

        // Only show published content to non-admin users
        if (! $content->isPublished() && (! Auth::check() || ! Auth::user()->hasRole('admin'))) {
            abort(404, 'Content not found');
        }

        $renderedContent = $this->renderContentService->execute($content);

        return view('themes.default.layouts.page', [
            'content' => $content,
            'renderedContent' => $renderedContent,
            'isPreview' => false,
        ]);
    }

    /**
     * Preview draft content (admin only)
     */
    public function preview(Request $request, int $contentId): View|Response
    {
        // Only allow authenticated admins
        if (! Auth::check() || (! Auth::user()->hasRole('admin') && ! Auth::user()->isAdmin())) {
            abort(403, 'Access denied. Admin privileges required for preview.');
        }

        $content = Content::findOrFail($contentId);
        $business = $content->business;

        if (! $business) {
            abort(404, 'Business not found for this content');
        }

        // Render content (including drafts)
        $renderedContent = $this->renderContentService->execute($content);

        return view('themes.default.layouts.page', [
            'content' => $content,
            'renderedContent' => $renderedContent,
            'isPreview' => true,
        ]);
    }
}
