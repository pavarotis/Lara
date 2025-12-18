@php
    // Module views receive $module and $settings
    $title = $settings['title'] ?? 'Frequently Asked Questions';
    $faqs = $settings['faqs'] ?? [];
@endphp

<div class="faq-module py-12">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        @if($title)
            <h2 class="text-3xl font-bold mb-8 text-center">{{ $title }}</h2>
        @endif
        
        @if(count($faqs) > 0)
            <div class="space-y-4">
                @foreach($faqs as $index => $faq)
                    <div class="bg-white rounded-lg shadow-md overflow-hidden">
                        <button class="w-full px-6 py-4 text-left font-semibold flex justify-between items-center hover:bg-gray-50 transition-colors" onclick="toggleFaq({{ $index }})">
                            <span>{{ $faq['question'] ?? 'Question' }}</span>
                            <svg class="w-5 h-5 transform transition-transform faq-icon-{{ $index }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div class="px-6 pb-4 text-gray-600 faq-content-{{ $index }} hidden">
                            {!! $faq['answer'] ?? 'Answer' !!}
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-center text-gray-500">No FAQs available.</p>
        @endif
    </div>
</div>

<script>
    function toggleFaq(index) {
        const content = document.querySelector('.faq-content-' + index);
        const icon = document.querySelector('.faq-icon-' + index);
        content.classList.toggle('hidden');
        icon.classList.toggle('rotate-180');
    }
</script>

