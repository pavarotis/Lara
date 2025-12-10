@props(['block', 'index'])

<div class="border border-gray-200 rounded-lg p-4 bg-white" data-block-index="{{ $index }}">
    <div class="flex items-center justify-between mb-3">
        <h4 class="text-sm font-semibold text-gray-700">Text Block</h4>
        <button type="button" onclick="removeBlock({{ $index }})" class="text-red-600 hover:text-red-800 text-sm">
            Remove
        </button>
    </div>
    
    <div class="space-y-3">
        <input type="hidden" name="blocks[{{ $index }}][type]" value="text">
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Content *</label>
            <textarea name="blocks[{{ $index }}][props][content]" 
                      rows="6"
                      class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary"
                      placeholder="Enter your text content...">{{ $block['props']['content'] ?? '' }}</textarea>
            <p class="mt-1 text-xs text-gray-500">You can use HTML tags for formatting</p>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Alignment</label>
            <select name="blocks[{{ $index }}][props][alignment]" 
                    class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary">
                <option value="left" {{ ($block['props']['alignment'] ?? 'left') === 'left' ? 'selected' : '' }}>Left</option>
                <option value="center" {{ ($block['props']['alignment'] ?? '') === 'center' ? 'selected' : '' }}>Center</option>
                <option value="right" {{ ($block['props']['alignment'] ?? '') === 'right' ? 'selected' : '' }}>Right</option>
            </select>
        </div>
    </div>
</div>

