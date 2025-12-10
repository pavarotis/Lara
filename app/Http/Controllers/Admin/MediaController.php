<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Domain\Businesses\Models\Business;
use App\Domain\Media\Models\Media;
use App\Domain\Media\Models\MediaFolder;
use App\Domain\Media\Services\DeleteMediaService;
use App\Domain\Media\Services\GetMediaService;
use App\Domain\Media\Services\UploadMediaService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Media\UpdateMediaRequest;
use App\Http\Requests\Media\UploadMediaRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MediaController extends Controller
{
    public function __construct(
        private UploadMediaService $uploadMediaService,
        private DeleteMediaService $deleteMediaService,
        private GetMediaService $getMediaService
    ) {}

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Media::class);

        $business = Business::active()->firstOrFail();

        $query = Media::where('business_id', $business->id)
            ->with(['folder', 'creator'])
            ->orderBy('created_at', 'desc');

        // Filters
        if ($request->filled('folder_id')) {
            $query->where('folder_id', $request->folder_id);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%'.$request->search.'%')
                    ->orWhere('path', 'like', '%'.$request->search.'%');
            });
        }

        $media = $query->paginate(24);

        // Get folders for sidebar
        $folders = MediaFolder::where('business_id', $business->id)
            ->orderBy('name', 'asc')
            ->get();

        return view('admin.media.index', [
            'media' => $media,
            'folders' => $folders,
            'business' => $business,
        ]);
    }

    public function store(UploadMediaRequest $request): RedirectResponse
    {
        $this->authorize('create', Media::class);

        $business = Business::active()->firstOrFail();

        // Handle multiple files or single file
        $files = $request->hasFile('files')
            ? $request->file('files')
            : ($request->hasFile('file') ? [$request->file('file')] : []);

        if (empty($files)) {
            return redirect()->route('admin.media.index')
                ->with('error', 'No files selected.');
        }

        foreach ($files as $file) {
            $this->uploadMediaService->execute($business, $file, $request->folder_id);
        }

        return redirect()->route('admin.media.index')
            ->with('success', count($files).' file(s) uploaded successfully!');
    }

    public function update(UpdateMediaRequest $request, Media $media): RedirectResponse
    {
        $this->authorize('update', $media);

        $media->update($request->validated());

        return redirect()->route('admin.media.index')
            ->with('success', 'Media updated successfully!');
    }

    public function destroy(Media $media): RedirectResponse
    {
        $this->authorize('delete', $media);

        $this->deleteMediaService->execute($media);

        return redirect()->route('admin.media.index')
            ->with('success', 'Media deleted successfully!');
    }
}
