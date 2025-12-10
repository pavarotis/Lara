@props(['block', 'index'])

<div class="border border-gray-200 rounded-lg p-4 bg-white" data-block-index="{{ $index }}">
    <div class="flex items-center justify-between mb-3">
        <h4 class="text-sm font-semibold text-gray-700">Hero Block</h4>
        <button type="button" onclick="removeBlock({{ $index }})" class="text-red-600 hover:text-red-800 text-sm">
            Remove
        </button>
    </div>
    
    <div class="space-y-3">
        <input type="hidden" name="blocks[{{ $index }}][type]" value="hero">
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Title *</label>
            <input type="text" name="blocks[{{ $index }}][props][title]" 
                   value="{{ $block['props']['title'] ?? '' }}"
                   class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary"
                   placeholder="Hero title">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Subtitle</label>
            <input type="text" name="blocks[{{ $index }}][props][subtitle]" 
                   value="{{ $block['props']['subtitle'] ?? '' }}"
                   class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary"
                   placeholder="Hero subtitle">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Image URL</label>
            <input type="text" name="blocks[{{ $index }}][props][image]" 
                   value="{{ $block['props']['image'] ?? '' }}"
                   class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary"
                   placeholder="https://example.com/image.jpg">
            <p class="mt-1 text-xs text-gray-500">Enter image URL (media picker coming in Sprint 2)</p>
        </div>

        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">CTA Text</label>
                <input type="text" name="blocks[{{ $index }}][props][cta_text]" 
                       value="{{ $block['props']['cta_text'] ?? '' }}"
                       class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary"
                       placeholder="Button text">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">CTA Link</label>
                <input type="text" name="blocks[{{ $index }}][props][cta_link]" 
                       value="{{ $block['props']['cta_link'] ?? '' }}"
                       class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary"
                       placeholder="/menu">
            </div>
        </div>
    </div>
</div>

