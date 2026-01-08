<x-admin-layout>
    <x-slot name="title">Testing Dashboard</x-slot>

    <div class="space-y-6">
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h1 class="text-2xl font-bold text-gray-900 mb-4">Testing Dashboard</h1>
            <p class="text-gray-600">
                Overview of test suite status and results.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach($testSuites as $name => $suite)
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">{{ $name }}</h3>
                        @if($suite['status'] === 'passing')
                            <span class="bg-green-100 text-green-800 text-xs font-semibold px-3 py-1 rounded-full">
                                Passing
                            </span>
                        @else
                            <span class="bg-red-100 text-red-800 text-xs font-semibold px-3 py-1 rounded-full">
                                Failing
                            </span>
                        @endif
                    </div>

                    <div class="space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Total Tests:</span>
                            <span class="font-medium text-gray-900">{{ $suite['count'] }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Passed:</span>
                            <span class="font-medium text-green-600">{{ $suite['passed'] }}</span>
                        </div>
                        @if($suite['failed'] > 0)
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Failed:</span>
                                <span class="font-medium text-red-600">{{ $suite['failed'] }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Last Run:</span>
                            <span class="font-medium text-gray-900">{{ $suite['last_run']->diffForHumans() }}</span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</x-admin-layout>

