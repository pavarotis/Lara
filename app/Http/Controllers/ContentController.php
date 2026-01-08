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
     * Show business home page (canonical routing)
     */
    public function showBusinessHome(Business $business): View|Response
    {
        // Get home page (slug: '/')
        // For admin users, also check unpublished content
        $user = Auth::user();
        $isAdmin = $user && (method_exists($user, 'hasRole') ? $user->hasRole('admin') : ($user->isAdmin ?? false));

        if ($isAdmin) {
            // Admin can see unpublished content
            $content = Content::where('business_id', $business->id)
                ->where('slug', '/')
                ->with(['business', 'layout.business'])
                ->first();
        } else {
            // Regular users only see published content
            $content = $this->getContentService->bySlug($business->id, '/');
        }

        if (! $content) {
            $message = $isAdmin
                ? 'Home page not found. Please create a home page with slug "/" in the CMS (Content → New → Page).'
                : 'Home page not found.';
            abort(404, $message);
        }

        // Only show published content to non-admin users
        if (! $content->isPublished() && ! $isAdmin) {
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
     * Show public content by slug (canonical routing with business)
     */
    public function show(Business $business, string $page): View|Response
    {
        $content = $this->getContentService->bySlug($business->id, $page);

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
     * Legacy show method (fallback for non-canonical routes)
     */
    public function showLegacy(Request $request, string $slug): View|Response
    {
        $business = $request->attributes->get('business') ?? Business::active()->first();

        if (! $business) {
            abort(404, 'Business not found');
        }

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
