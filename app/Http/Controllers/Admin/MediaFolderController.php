<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Domain\Businesses\Models\Business;
use App\Domain\Media\Models\MediaFolder;
use App\Http\Controllers\Controller;
use App\Http\Requests\Media\CreateFolderRequest;
use App\Http\Requests\Media\UpdateFolderRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class MediaFolderController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', MediaFolder::class);

        $business = Business::active()->firstOrFail();

        // Get root folders (no parent) with children recursively
        $folders = MediaFolder::where('business_id', $business->id)
            ->whereNull('parent_id')
            ->with('children')
            ->get();

        return view('admin.media.folders', [
            'folders' => $folders,
            'business' => $business,
        ]);
    }

    public function store(CreateFolderRequest $request): RedirectResponse
    {
        $this->authorize('create', MediaFolder::class);

        MediaFolder::create([
            'business_id' => $request->business_id,
            'parent_id' => $request->parent_id,
            'name' => $request->name,
            'path' => $this->generatePath($request->name, $request->parent_id),
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('admin.media.folders.index')
            ->with('success', 'Folder created successfully!');
    }

    public function update(UpdateFolderRequest $request, MediaFolder $folder): RedirectResponse
    {
        $this->authorize('update', $folder);

        $folder->update([
            'name' => $request->name,
            'path' => $this->generatePath($request->name, $folder->parent_id),
        ]);

        return redirect()->route('admin.media.folders.index')
            ->with('success', 'Folder updated successfully!');
    }

    public function destroy(MediaFolder $folder): RedirectResponse
    {
        $this->authorize('delete', $folder);

        // Check if folder has children or files
        if ($folder->children()->count() > 0 || $folder->media()->count() > 0) {
            return redirect()->route('admin.media.folders.index')
                ->with('error', 'Cannot delete folder with children or files!');
        }

        $folder->delete();

        return redirect()->route('admin.media.folders.index')
            ->with('success', 'Folder deleted successfully!');
    }

    private function generatePath(string $name, ?int $parentId): string
    {
        if ($parentId) {
            $parent = MediaFolder::find($parentId);
            if ($parent) {
                return $parent->path.'/'.$name;
            }
        }

        return $name;
    }
}
