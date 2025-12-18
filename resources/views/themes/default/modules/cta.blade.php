@php
    // Module views receive $module and $settings
    $title = $settings['title'] ?? '';
    $text = $settings['text'] ?? '';
    $buttonText = $settings['button_text'] ?? 'Get Started';
    $buttonLink = $settings['button_link'] ?? '#';
    $buttonStyle = $settings['button_style'] ?? 'primary'; // primary, secondary, outline
    $alignment = $settings['alignment'] ?? 'center';
    
    $alignmentClasses = [
        'left' => 'text-left',
        'center' => 'text-center',
        'right' => 'text-right',
    ];
    $alignClass = $alignmentClasses[$alignment] ?? 'text-center';
    
    $buttonClasses = [
        'primary' => 'bg-blue-600 text-white hover:bg-blue-700',
        'secondary' => 'bg-gray-600 text-white hover:bg-gray-700',
        'outline' => 'border-2 border-blue-600 text-blue-600 hover:bg-blue-600 hover:text-white',
    ];
    $buttonClass = $buttonClasses[$buttonStyle] ?? $buttonClasses['primary'];
@endphp

<div class="cta-module py-16 {{ $alignClass }}">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        @if($title)
            <h2 class="text-3xl md:text-4xl font-bold mb-4">{{ $title }}</h2>
        @endif
        
        @if($text)
            <p class="text-lg mb-8 text-gray-600">{{ $text }}</p>
        @endif
        
        <a href="{{ $buttonLink }}" class="inline-block px-8 py-4 {{ $buttonClass }} font-semibold rounded-lg transition-colors">
            {{ $buttonText }}
        </a>
    </div>
</div>

