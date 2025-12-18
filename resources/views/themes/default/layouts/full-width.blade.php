{{-- Full Width Layout View --}}
{{-- This layout extends the public layout and renders regions without sidebars --}}
@extends('layouts.public')

@section('content')
    {{-- Header Top Region --}}
    @if(isset($regions['header_top']))
        <div class="layout-region header-top">
            {!! $regions['header_top'] !!}
        </div>
    @endif
    
    {{-- Header Bottom Region --}}
    @if(isset($regions['header_bottom']))
        <div class="layout-region header-bottom">
            {!! $regions['header_bottom'] !!}
        </div>
    @endif
    
    <div class="layout-main">
        {{-- Content Top Region --}}
        @if(isset($regions['content_top']))
            <div class="layout-region content-top">
                {!! $regions['content_top'] !!}
            </div>
        @endif
        
        {{-- Main Content (full width) --}}
        <main class="layout-main-content w-full">
            {!! $regions['main_content'] ?? '' !!}
        </main>
        
        {{-- Content Bottom Region --}}
        @if(isset($regions['content_bottom']))
            <div class="layout-region content-bottom">
                {!! $regions['content_bottom'] !!}
            </div>
        @endif
    </div>
    
    {{-- Footer Top Region --}}
    @if(isset($regions['footer_top']))
        <div class="layout-region footer-top">
            {!! $regions['footer_top'] !!}
        </div>
    @endif
@endsection

