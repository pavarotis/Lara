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
            <label class="block text-sm font-medium text-gray-700 mb-1">Hero Image</label>
            <!-- Media Picker -->
            <x-admin.media-picker 
                :name="'blocks[' . $index . '][props][image]'"
                mode="single"
                type="image"
                :selected="isset($block['props']['image_id']) ? [$block['props']['image_id']] : []" />
            
            <!-- Hidden fields will be managed by media-picker component -->
            
            <!-- Image Preview -->
            @if(isset($block['props']['image_url']) && !empty($block['props']['image_url']))
                <div class="mt-2">
                    <img src="{{ $block['props']['image_url'] }}" 
                         alt="Hero preview" 
                         class="w-full h-48 object-cover rounded-lg border border-gray-300">
                </div>
            @endif
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

