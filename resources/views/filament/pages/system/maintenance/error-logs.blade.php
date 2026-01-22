<x-filament-panels::page>
    @php
        // Global UI Variables - Customize all elements here
        // Dark Theme Colors (default)
        $cardBorderRadius = '0.5rem';
        $cardPadding = '1.5rem';
        $cardBorderColor = 'rgb(63 63 70)';  // gray-700
        $cardBackground = 'rgb(24 24 27)';   // gray-900 - dark background
        
        // Log File Item Variables
        $fileItemPadding = '1rem 1.25rem';
        $fileItemBorderRadius = '0.5rem';
        $fileItemBorderColor = 'rgb(63 63 70)';  // gray-700
        $fileItemHoverBorderColor = 'rgb(217 119 6)';  // primary-600 (amber)
        $fileItemBackground = 'rgb(24 24 27)';  // gray-900
        $fileItemHoverBackground = 'rgb(63 63 70)';  // gray-700
        $fileNameFontSize = '0.875rem';
        $fileNameFontWeight = '600';
        $fileNameColor = '#ffffff';  // white text
        $fileMetaFontSize = '0.75rem';
        $fileMetaColor = 'rgb(161 161 170)';  // gray-400
        
        // Log Viewer Header Variables
        $headerPadding = '1rem 1.25rem';
        $headerBackground = 'rgb(36 36 39)';  // gray-800 - placeholder background
        $headerBorderColor = 'rgb(82 82 91)';  // gray-600
        $headerFontSize = '1rem';
        $headerFontWeight = '600';
        $headerColor = '#ffffff';  // white text
        $closeButtonSize = '2rem';
        $closeButtonBorderRadius = '0.375rem';
        
        // Textarea Variables
        $height = '500px';
        $padding = '0.5rem 1rem';
        $margin = '1rem 0';
        $fontSize = '0.875rem';
        $lineHeight = '1.5';
        $bgColor = 'rgb(24 24 27)';  // gray-900 - dark background
        $textColor = '#ffffff';  // white text
        $border = '1px solid rgb(63 63 70)';  // gray-700
        $borderRadius = '4px';
        
        $logFiles = $this->getLogFiles();
    @endphp
    
    <div class="error-logs-page">
        <!-- Log Files List -->
        <div class="log-files" style="
            background: {{ $cardBackground }};
            border-radius: {{ $cardBorderRadius }};
            padding: {{ $cardPadding }};
            border: 1px solid {{ $cardBorderColor }};
        ">

            @if (empty($logFiles))
                <div class="log-files-empty">
                    <p>No log files found</p>
                </div>
            @else
                <div class="log-files-list">
                    @foreach ($logFiles as $file)
                        <div class="log-file-item"
                             wire:click="loadLogFile('{{ $file['name'] }}')"
                             style="
                                 padding: {{ $fileItemPadding }};
                                 border-radius: {{ $fileItemBorderRadius }};
                                 border-color: {{ $fileItemBorderColor }};
                                 background: {{ $fileItemBackground }};
                             "
                             onmouseover="this.style.borderColor='{{ $fileItemHoverBorderColor }}'; this.style.background='{{ $fileItemHoverBackground }}';"
                             onmouseout="this.style.borderColor='{{ $fileItemBorderColor }}'; this.style.background='{{ $fileItemBackground }}';"
                        >
                            <p class="log-file-name" style="
                                font-size: {{ $fileNameFontSize }};
                                font-weight: {{ $fileNameFontWeight }};
                                color: {{ $fileNameColor }};
                            ">
                                {{ $file['name'] }}
                            </p>
                            <p class="log-file-meta" style="
                                font-size: {{ $fileMetaFontSize }};
                                color: {{ $fileMetaColor }};
                            ">
                                {{ number_format($file['size'] / 1024, 2) }} KB • 
                                Modified: {{ date('Y-m-d H:i:s', $file['modified']) }}
                            </p>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Log Content Viewer -->
        @if ($this->selectedLogFile && $this->logContent)
            <div class="log-viewer" style="
                border-radius: {{ $cardBorderRadius }};
                border-color: {{ $cardBorderColor }};
            ">
                <div class="log-viewer-header" style="
                    padding: {{ $headerPadding }};
                    background: {{ $headerBackground }};
                    border-bottom-color: {{ $headerBorderColor }};
                ">
                    <span style="
                        font-size: {{ $headerFontSize }};
                        font-weight: {{ $headerFontWeight }};
                        color: {{ $headerColor }};
                    ">{{ $this->selectedLogFile }}</span>
                    <button 
                        wire:click="$set('selectedLogFile', null)"
                        style="
                            width: {{ $closeButtonSize }};
                            height: {{ $closeButtonSize }};
                            border-radius: {{ $closeButtonBorderRadius }};
                        "
                    >
                        ✕
                    </button>
                </div>
                <div class="log-viewer-body">
                    <textarea
                        readonly
                        style="
                            width: 100%;
                            height: {{ $height }};
                            padding: {{ $padding }};
                            margin: {{ $margin }};
                            font-family: monospace;
                            font-size: {{ $fontSize }};
                            line-height: {{ $lineHeight }};
                            white-space: pre-wrap;
                            word-break: break-word;
                            overflow-y: auto;
                            overflow-x: hidden;
                            resize: none;
                            border: {{ $border }};
                            border-radius: {{ $borderRadius }};
                            background: {{ $bgColor }};
                            color: {{ $textColor }};
                        "
                    >{{ $this->logContent }}</textarea>
                </div>
            </div>
        @endif

        @if ($this->selectedLogFile && !$this->logContent)
            <div class="log-viewer-error">
                <p>Unable to load log file content</p>
            </div>
        @endif
    </div>
</x-filament-panels::page>
