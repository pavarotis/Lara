{{-- Default Layout View --}}
{{-- This layout extends the public layout and renders regions --}}
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
        <div class="layout-container max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Content Top Region --}}
            @if(isset($regions['content_top']))
                <div class="layout-region content-top py-8">
                    {!! $regions['content_top'] !!}
                </div>
            @endif
            
            <div class="layout-content-wrapper flex flex-col lg:flex-row gap-8">
                {{-- Column Left (optional) --}}
                @if(isset($regions['column_left']))
                    <aside class="layout-sidebar layout-sidebar-left lg:w-1/4">
                        <div class="sticky top-4">
                            {!! $regions['column_left'] !!}
                        </div>
                    </aside>
                @endif
                
                {{-- Main Content --}}
                <main class="layout-main-content flex-1">
                    {!! $regions['main_content'] ?? '' !!}
                </main>
                
                {{-- Column Right (optional) --}}
                @if(isset($regions['column_right']))
                    <aside class="layout-sidebar layout-sidebar-right lg:w-1/4">
                        <div class="sticky top-4">
                            {!! $regions['column_right'] !!}
                        </div>
                    </aside>
                @endif
            </div>
            
            {{-- Content Bottom Region --}}
            @if(isset($regions['content_bottom']))
                <div class="layout-region content-bottom py-8">
                    {!! $regions['content_bottom'] !!}
                </div>
            @endif
        </div>
    </div>
    
    {{-- Footer Top Region --}}
    @if(isset($regions['footer_top']))
        <div class="layout-region footer-top">
            {!! $regions['footer_top'] !!}
        </div>
    @endif
@endsection

