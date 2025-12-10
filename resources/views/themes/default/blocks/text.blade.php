@php
    $content = $content ?? '';
    $alignment = $alignment ?? 'left';
    
    // Alignment classes
    $alignmentClasses = [
        'left' => 'text-left',
        'center' => 'text-center',
        'right' => 'text-right',
        'justify' => 'text-justify',
    ];
    $alignClass = $alignmentClasses[$alignment] ?? 'text-left';
@endphp

<section class="py-12 px-4 sm:px-6 lg:px-8 bg-white">
    <div class="max-w-4xl mx-auto">
        <div class="prose prose-lg max-w-none {{ $alignClass }}">
            {!! $content !!}
        </div>
    </div>
</section>

<style>
    .prose {
        color: #374151;
        line-height: 1.75;
    }
    .prose h1, .prose h2, .prose h3, .prose h4, .prose h5, .prose h6 {
        font-weight: 700;
        margin-top: 2em;
        margin-bottom: 1em;
        color: #111827;
        line-height: 1.25;
    }
    .prose h1 { font-size: 2.25em; }
    .prose h2 { font-size: 1.875em; }
    .prose h3 { font-size: 1.5em; }
    .prose h4 { font-size: 1.25em; }
    .prose p {
        margin-bottom: 1.25em;
        line-height: 1.75;
    }
    .prose a {
        color: var(--color-primary, #3b82f6);
        text-decoration: underline;
        transition: color 0.2s;
    }
    .prose a:hover {
        color: var(--color-primary, #2563eb);
        text-decoration: none;
    }
    .prose img {
        max-width: 100%;
        height: auto;
        border-radius: 0.5rem;
        margin: 1.5em 0;
    }
    .prose ul, .prose ol {
        margin-bottom: 1.25em;
        padding-left: 1.625em;
    }
    .prose li {
        margin-bottom: 0.5em;
    }
    .prose blockquote {
        border-left: 4px solid var(--color-primary, #3b82f6);
        padding-left: 1em;
        margin: 1.5em 0;
        font-style: italic;
        color: #6b7280;
    }
    .prose code {
        background-color: #f3f4f6;
        padding: 0.125em 0.375em;
        border-radius: 0.25rem;
        font-size: 0.875em;
    }
    .prose pre {
        background-color: #1f2937;
        color: #f9fafb;
        padding: 1em;
        border-radius: 0.5rem;
        overflow-x: auto;
        margin: 1.5em 0;
    }
    .prose pre code {
        background-color: transparent;
        padding: 0;
        color: inherit;
    }
</style>

