@props(['block', 'index'])

<div class="border border-gray-200 rounded-lg p-4 bg-white" data-block-index="{{ $index }}">
    <div class="flex items-center justify-between mb-3">
        <h4 class="text-sm font-semibold text-gray-700">Gallery Block</h4>
        <button type="button" onclick="removeBlock({{ $index }})" class="text-red-600 hover:text-red-800 text-sm">
            Remove
        </button>
    </div>
    
    <div class="space-y-3">
        <input type="hidden" name="blocks[{{ $index }}][type]" value="gallery">
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Gallery Images</label>
            <!-- Media Picker -->
            <x-admin.media-picker 
                :name="'blocks[' . $index . '][props][images]'"
                mode="multiple"
                type="image"
                :selected="isset($block['props']['image_ids']) && is_array($block['props']['image_ids']) ? $block['props']['image_ids'] : []" />
            
            <!-- Hidden fields will be managed by media-picker component -->
            
            <!-- Gallery Preview -->
            @if(isset($block['props']['images']) && is_array($block['props']['images']))
                <div class="mt-2 grid grid-cols-3 gap-2">
                    @foreach($block['props']['images'] as $image)
                        @if(is_array($image) && isset($image['url']))
                            <div class="relative aspect-square rounded-lg overflow-hidden border border-gray-300">
                                <img src="{{ $image['thumbnail_url'] ?? $image['url'] }}" 
                                     alt="Gallery preview" 
                                     class="w-full h-full object-cover">
                            </div>
                        @endif
                    @endforeach
                </div>
            @endif
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Columns</label>
            <select name="blocks[{{ $index }}][props][columns]" 
                    class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary">
                <option value="1" {{ ($block['props']['columns'] ?? 3) == 1 ? 'selected' : '' }}>1 Column</option>
                <option value="2" {{ ($block['props']['columns'] ?? 3) == 2 ? 'selected' : '' }}>2 Columns</option>
                <option value="3" {{ ($block['props']['columns'] ?? 3) == 3 ? 'selected' : '' }}>3 Columns</option>
                <option value="4" {{ ($block['props']['columns'] ?? 3) == 4 ? 'selected' : '' }}>4 Columns</option>
            </select>
        </div>
    </div>
</div>

