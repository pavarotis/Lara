@props(['module', 'widthMode' => 'contained', 'style' => []])

@php
    $background = $style['background'] ?? null;
    $backgroundImage = $style['background_image'] ?? null;
    $padding = $style['padding'] ?? null;
    $margin = $style['margin'] ?? null;
    
    // Default padding classes (can be overridden by inline style)
    $paddingClasses = 'py-8 md:py-12';
    if (isset($padding)) {
        $paddingClasses = ''; // Use inline padding if specified
    }
    
    // Default margin classes (can be overridden by inline style)
    $marginClasses = '';
    if (isset($margin)) {
        $marginClasses = ''; // Use inline margin if specified
    }
    
    // Build inline styles
    $inlineStyles = [];
    if ($background && !$backgroundImage) {
        // Only set background-color if no background image
        $inlineStyles[] = "background-color: {$background};";
    }
    if ($backgroundImage) {
        // Background image with overlay if background color is also set
        $inlineStyles[] = "background-image: url('{$backgroundImage}');";
        $inlineStyles[] = "background-size: cover;";
        $inlineStyles[] = "background-position: center;";
        $inlineStyles[] = "background-repeat: no-repeat;";
        $inlineStyles[] = "background-attachment: scroll;";
    }
    if ($padding) {
        $inlineStyles[] = "padding: {$padding};";
    }
    if ($margin) {
        $inlineStyles[] = "margin: {$margin};";
    }
    $styleString = !empty($inlineStyles) ? implode(' ', $inlineStyles) : '';
    
    // Base classes for the row wrapper
    $wrapperClasses = 'module-row w-full relative';
    if ($backgroundImage) {
        $wrapperClasses .= ' relative overflow-hidden';
    }
    if ($paddingClasses && !$padding) {
        $wrapperClasses .= ' ' . $paddingClasses;
    }
@endphp

<div class="{{ $wrapperClasses }}" 
     data-module-type="{{ $module->type }}"
     data-width-mode="{{ $widthMode }}"
     @if($styleString) style="{{ $styleString }}" @endif>
    
    @if($backgroundImage && $background)
        {{-- Background overlay if both image and color are set --}}
        <div class="absolute inset-0 z-0" style="background-color: {{ $background }}; opacity: 0.8;"></div>
    @endif
    
    @if($widthMode === 'full-bg-contained-content')
        {{-- Full background, contained content --}}
        <div class="relative z-10 container mx-auto px-4 sm:px-6 lg:px-8">
            {{ $slot }}
        </div>
    @elseif($widthMode === 'full')
        {{-- Full width, no container --}}
        <div class="relative z-10 w-full">
            {{ $slot }}
        </div>
    @else
        {{-- Contained (default) --}}
        <div class="relative z-10 container mx-auto px-4 sm:px-6 lg:px-8">
            {{ $slot }}
        </div>
    @endif
</div>

