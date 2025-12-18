{{-- Landing Layout View --}}
{{-- This layout is optimized for landing pages with minimal structure --}}
@extends('layouts.public')

@section('content')
    {{-- Main Content (full width, no sidebars, minimal regions) --}}
    <main class="layout-main-content w-full">
        {{-- Content Top Region --}}
        @if(isset($regions['content_top']))
            <div class="layout-region content-top">
                {!! $regions['content_top'] !!}
            </div>
        @endif
        
        {{-- Main Content --}}
        {!! $regions['main_content'] ?? '' !!}
        
        {{-- Content Bottom Region --}}
        @if(isset($regions['content_bottom']))
            <div class="layout-region content-bottom">
                {!! $regions['content_bottom'] !!}
            </div>
        @endif
    </main>
@endsection

