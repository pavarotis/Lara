@php
    // Module views receive $module and $settings
    $title = $settings['title'] ?? 'Contact Us';
    $phone = $settings['phone'] ?? '';
    $email = $settings['email'] ?? '';
    $address = $settings['address'] ?? '';
    $website = $settings['website'] ?? '';
@endphp

<div class="contact-card-module py-12">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
        @if($title)
            <h2 class="text-3xl font-bold mb-6 text-center">{{ $title }}</h2>
        @endif
        
        <div class="bg-white rounded-lg shadow-md p-8">
            <div class="space-y-4">
                @if($phone)
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-blue-600 mr-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                        </svg>
                        <a href="tel:{{ $phone }}" class="text-lg text-gray-700 hover:text-blue-600">{{ $phone }}</a>
                    </div>
                @endif
                
                @if($email)
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-blue-600 mr-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        <a href="mailto:{{ $email }}" class="text-lg text-gray-700 hover:text-blue-600">{{ $email }}</a>
                    </div>
                @endif
                
                @if($address)
                    <div class="flex items-start">
                        <svg class="w-6 h-6 text-blue-600 mr-4 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <p class="text-lg text-gray-700">{{ $address }}</p>
                    </div>
                @endif
                
                @if($website)
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-blue-600 mr-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" />
                        </svg>
                        <a href="{{ $website }}" target="_blank" rel="noopener" class="text-lg text-gray-700 hover:text-blue-600">{{ $website }}</a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

