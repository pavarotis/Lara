<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Domain\Businesses\Models\Business;
use App\Domain\Content\Models\Content;
use App\Domain\Content\Services\CreateContentService;
use App\Domain\Content\Services\DeleteContentService;
use App\Domain\Content\Services\GetContentService;
use App\Domain\Content\Services\PublishContentService;
use App\Domain\Content\Services\UpdateContentService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Content\StoreContentRequest;
use App\Http\Requests\Content\UpdateContentRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContentController extends Controller
{
    public function __construct(
        private CreateContentService $createContentService,
        private UpdateContentService $updateContentService,
        private DeleteContentService $deleteContentService,
        private GetContentService $getContentService,
        private PublishContentService $publishContentService
    ) {}

    /**
     * Display a listing of content
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Content::class);

        $business = Business::active()->first();
        $query = Content::where('business_id', $business->id)
            ->with('creator')
            ->orderBy('updated_at', 'desc');

        // Filters
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%'.$request->search.'%')
                    ->orWhere('slug', 'like', '%'.$request->search.'%');
            });
        }

        $contents = $query->paginate(15);

        return view('admin.content.index', [
            'contents' => $contents,
            'business' => $business,
        ]);
    }

    /**
     * Show the form for creating a new content
     */
    public function create(): View
    {
        $this->authorize('create', Content::class);

        $business = Business::active()->first();
        $contentTypes = \App\Domain\Content\Models\ContentType::all();

        return view('admin.content.create', [
            'business' => $business,
            'contentTypes' => $contentTypes,
        ]);
    }

    /**
     * Store a newly created content
     */
    public function store(StoreContentRequest $request): RedirectResponse
    {
        $this->authorize('create', Content::class);

        $validated = $request->validated();

        // Convert blocks array to body_json if blocks provided
        if ($request->has('blocks') && ! empty($request->input('blocks'))) {
            $blocks = [];
            foreach ($request->input('blocks', []) as $block) {
                if (! empty($block['type'])) {
                    $props = $block['props'] ?? [];

                    // Handle hero block - media picker provides image_id, image_url, image_thumbnail_url
                    if ($block['type'] === 'hero') {
                        if (isset($props['image_id'])) {
                            $props['image_id'] = $props['image_id'];
                            $props['image_url'] = $props['image_url'] ?? null;
                            $props['image_thumbnail_url'] = $props['image_thumbnail_url'] ?? null;
                        }
                        // Legacy support: if image (URL) exists but no image_id, keep it
                        if (isset($props['image']) && ! isset($props['image_id'])) {
                            // Keep old format for backward compatibility
                        }
                    }

                    // Handle gallery block - media picker provides images array with id, url, thumbnail_url
                    if ($block['type'] === 'gallery') {
                        if (isset($props['images']) && is_array($props['images'])) {
                            // Media picker format: images array with objects containing id, url, thumbnail_url
                            $props['images'] = array_values($props['images']); // Ensure indexed array
                        } elseif (isset($props['images']) && is_string($props['images'])) {
                            // Legacy support: newline-separated URLs
                            $props['images'] = array_filter(array_map('trim', explode("\n", $props['images'])));
                        }
                    }

                    $blocks[] = [
                        'type' => $block['type'],
                        'props' => $props,
                    ];
                }
            }
            $validated['body_json'] = $blocks;
        } elseif (isset($validated['body_json']) && is_string($validated['body_json'])) {
            // Convert body_json from JSON string to array if needed
            $validated['body_json'] = json_decode($validated['body_json'], true) ?? [];
        }

        $validated['created_by'] = auth()->id();

        $content = $this->createContentService->execute($validated);

        return redirect()
            ->route('admin.content.show', $content)
            ->with('success', 'Content created successfully.');
    }

    /**
     * Display the specified content
     */
    public function show(Content $content): View
    {
        $this->authorize('view', $content);

        $content->load('revisions.user', 'creator');

        return view('admin.content.show', [
            'content' => $content,
        ]);
    }

    /**
     * Show the form for editing the specified content
     */
    public function edit(Content $content): View
    {
        $this->authorize('update', $content);

        $content->load('revisions.user');
        $contentTypes = \App\Domain\Content\Models\ContentType::all();

        return view('admin.content.edit', [
            'content' => $content,
            'contentTypes' => $contentTypes,
        ]);
    }

    /**
     * Update the specified content
     */
    public function update(UpdateContentRequest $request, Content $content): RedirectResponse
    {
        $this->authorize('update', $content);

        $validated = $request->validated();

        // Convert blocks array to body_json if blocks provided
        if ($request->has('blocks') && ! empty($request->input('blocks'))) {
            $blocks = [];
            foreach ($request->input('blocks', []) as $block) {
                if (! empty($block['type'])) {
                    $props = $block['props'] ?? [];

                    // Handle hero block - media picker provides image_id, image_url, image_thumbnail_url
                    if ($block['type'] === 'hero') {
                        if (isset($props['image_id'])) {
                            $props['image_id'] = $props['image_id'];
                            $props['image_url'] = $props['image_url'] ?? null;
                            $props['image_thumbnail_url'] = $props['image_thumbnail_url'] ?? null;
                        }
                        // Legacy support: if image (URL) exists but no image_id, keep it
                        if (isset($props['image']) && ! isset($props['image_id'])) {
                            // Keep old format for backward compatibility
                        }
                    }

                    // Handle gallery block - media picker provides images array with id, url, thumbnail_url
                    if ($block['type'] === 'gallery') {
                        if (isset($props['images']) && is_array($props['images'])) {
                            // Media picker format: images array with objects containing id, url, thumbnail_url
                            $props['images'] = array_values($props['images']); // Ensure indexed array
                        } elseif (isset($props['images']) && is_string($props['images'])) {
                            // Legacy support: newline-separated URLs
                            $props['images'] = array_filter(array_map('trim', explode("\n", $props['images'])));
                        }
                    }

                    $blocks[] = [
                        'type' => $block['type'],
                        'props' => $props,
                    ];
                }
            }
            $validated['body_json'] = $blocks;
        } elseif (isset($validated['body_json']) && is_string($validated['body_json'])) {
            // Convert body_json from JSON string to array if needed
            $validated['body_json'] = json_decode($validated['body_json'], true) ?? [];
        }

        $this->updateContentService->execute($content, $validated);

        return redirect()
            ->route('admin.content.show', $content)
            ->with('success', 'Content updated successfully.');
    }

    /**
     * Remove the specified content
     */
    public function destroy(Content $content): RedirectResponse
    {
        $this->authorize('delete', $content);

        $this->deleteContentService->execute($content);

        return redirect()
            ->route('admin.content.index')
            ->with('success', 'Content deleted successfully.');
    }

    /**
     * Publish the specified content
     */
    public function publish(Content $content): RedirectResponse
    {
        $this->authorize('update', $content);

        $this->publishContentService->execute($content);

        return redirect()
            ->route('admin.content.show', $content)
            ->with('success', 'Content published successfully.');
    }

    /**
     * Preview content (optional)
     */
    public function preview(Content $content): View
    {
        $this->authorize('view', $content);

        return view('admin.content.preview', [
            'content' => $content,
        ]);
    }
}
