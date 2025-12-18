@php
    // Module views receive $module and $settings
    $title = $settings['title'] ?? 'Opening Hours';
    $hours = $settings['hours'] ?? [];
    
    // Default hours structure
    $defaultHours = [
        'monday' => ['open' => '09:00', 'close' => '17:00', 'closed' => false],
        'tuesday' => ['open' => '09:00', 'close' => '17:00', 'closed' => false],
        'wednesday' => ['open' => '09:00', 'close' => '17:00', 'closed' => false],
        'thursday' => ['open' => '09:00', 'close' => '17:00', 'closed' => false],
        'friday' => ['open' => '09:00', 'close' => '17:00', 'closed' => false],
        'saturday' => ['open' => '10:00', 'close' => '14:00', 'closed' => false],
        'sunday' => ['open' => null, 'close' => null, 'closed' => true],
    ];
    
    $hours = array_merge($defaultHours, $hours);
    $dayNames = [
        'monday' => 'Monday',
        'tuesday' => 'Tuesday',
        'wednesday' => 'Wednesday',
        'thursday' => 'Thursday',
        'friday' => 'Friday',
        'saturday' => 'Saturday',
        'sunday' => 'Sunday',
    ];
@endphp

<div class="opening-hours-module py-12">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
        @if($title)
            <h2 class="text-3xl font-bold mb-6 text-center">{{ $title }}</h2>
        @endif
        
        <div class="bg-white rounded-lg shadow-md p-6">
            <dl class="space-y-3">
                @foreach($dayNames as $key => $dayName)
                    <div class="flex justify-between items-center py-2 border-b last:border-b-0">
                        <dt class="font-semibold">{{ $dayName }}</dt>
                        <dd class="text-gray-600">
                            @if($hours[$key]['closed'] ?? false)
                                <span class="text-red-600">Closed</span>
                            @else
                                {{ $hours[$key]['open'] ?? '09:00' }} - {{ $hours[$key]['close'] ?? '17:00' }}
                            @endif
                        </dd>
                    </div>
                @endforeach
            </dl>
        </div>
    </div>
</div>

