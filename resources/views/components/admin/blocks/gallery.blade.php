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
            <label class="block text-sm font-medium text-gray-700 mb-1">Image URLs *</label>
            <textarea name="blocks[{{ $index }}][props][images]" 
                      rows="4"
                      class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary"
                      placeholder="https://example.com/image1.jpg&#10;https://example.com/image2.jpg&#10;https://example.com/image3.jpg">{{ is_array($block['props']['images'] ?? []) ? implode("\n", $block['props']['images']) : ($block['props']['images'] ?? '') }}</textarea>
            <p class="mt-1 text-xs text-gray-500">Enter one image URL per line (media picker coming in Sprint 2)</p>
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

