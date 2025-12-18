@php
    // Module views receive $module and $settings
    $title = $settings['title'] ?? 'Testimonials';
    $testimonials = $settings['testimonials'] ?? [];
    $columns = $settings['columns'] ?? 3;
    
    $gridClasses = [
        1 => 'grid-cols-1',
        2 => 'grid-cols-1 md:grid-cols-2',
        3 => 'grid-cols-1 md:grid-cols-2 lg:grid-cols-3',
    ];
    $gridClass = $gridClasses[$columns] ?? 'grid-cols-1 md:grid-cols-2 lg:grid-cols-3';
@endphp

<div class="testimonials-module py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        @if($title)
            <h2 class="text-3xl font-bold mb-8 text-center">{{ $title }}</h2>
        @endif
        
        @if(count($testimonials) > 0)
            <div class="grid {{ $gridClass }} gap-6">
                @foreach($testimonials as $testimonial)
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <div class="flex items-center mb-4">
                            @if(isset($testimonial['rating']))
                                <div class="flex text-yellow-400">
                                    @for($i = 0; $i < 5; $i++)
                                        <svg class="w-5 h-5 {{ $i < ($testimonial['rating'] ?? 5) ? 'fill-current' : 'text-gray-300' }}" viewBox="0 0 20 20">
                                            <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                        </svg>
                                    @endfor
                                </div>
                            @endif
                        </div>
                        
                        <p class="text-gray-700 mb-4 italic">"{{ $testimonial['text'] ?? '' }}"</p>
                        
                        <div class="flex items-center">
                            @if(isset($testimonial['avatar_id']))
                                @php
                                    $avatar = \App\Domain\Media\Models\Media::find($testimonial['avatar_id']);
                                @endphp
                                @if($avatar)
                                    <img src="{{ $avatar->thumbnail_url ?? $avatar->url }}" alt="{{ $testimonial['name'] ?? '' }}" class="w-12 h-12 rounded-full mr-4">
                                @endif
                            @endif
                            <div>
                                <p class="font-semibold">{{ $testimonial['name'] ?? 'Anonymous' }}</p>
                                @if(isset($testimonial['position']))
                                    <p class="text-sm text-gray-500">{{ $testimonial['position'] }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-center text-gray-500">No testimonials available.</p>
        @endif
    </div>
</div>

