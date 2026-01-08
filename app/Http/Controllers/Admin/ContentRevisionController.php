<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Domain\Content\Models\Content;
use App\Domain\Content\Models\ContentRevision;
use App\Domain\Content\Services\CreateRevisionService;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ContentRevisionController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        private CreateRevisionService $createRevisionService
    ) {}

    /**
     * Display a listing of revisions for content
     */
    public function index(Content $content): View
    {
        $this->authorize('view', $content);

        $revisions = $content->revisions()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.content.revisions.index', [
            'content' => $content,
            'revisions' => $revisions,
        ]);
    }

    /**
     * Display the specified revision
     */
    public function show(Content $content, ContentRevision $revision): View
    {
        $this->authorize('view', $content);

        // Ensure revision belongs to content
        if ($revision->content_id !== $content->id) {
            abort(404, 'Revision not found for this content');
        }

        $revision->load('user');

        return view('admin.content.revisions.show', [
            'content' => $content,
            'revision' => $revision,
        ]);
    }

    /**
     * Restore content to this revision state
     */
    public function restore(Content $content, ContentRevision $revision): RedirectResponse
    {
        $this->authorize('update', $content);

        // Ensure revision belongs to content
        if ($revision->content_id !== $content->id) {
            abort(404, 'Revision not found for this content');
        }

        // Create backup revision before restoring
        $this->createRevisionService->execute($content);

        // Restore revision
        $revision->restore();

        return redirect()
            ->route('admin.content.show', $content)
            ->with('success', 'Content restored to revision from '.$revision->created_at->format('M d, Y H:i'));
    }

    /**
     * Compare two revisions
     */
    public function compare(Content $content, ContentRevision $a, string $b): View
    {
        $this->authorize('view', $content);

        // Handle 'latest' or revision ID
        if ($b === 'latest') {
            $bRevision = $content->revisions()->latest()->first();
            if (! $bRevision) {
                abort(404, 'No revisions found');
            }
        } else {
            $bRevision = ContentRevision::findOrFail($b);
        }

        // Ensure both revisions belong to content
        if ($a->content_id !== $content->id || $bRevision->content_id !== $content->id) {
            abort(404, 'Revisions not found for this content');
        }

        $a->load('user');
        $bRevision->load('user');

        return view('admin.content.revisions.compare', [
            'content' => $content,
            'revisionA' => $a,
            'revisionB' => $bRevision,
        ]);
    }
}
