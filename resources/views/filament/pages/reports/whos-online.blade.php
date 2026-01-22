<x-filament-panels::page>
    @push('styles')
        @vite('resources/css/reports.css')
    @endpush

    @php
        $onlineUsers = $this->getOnlineUsers();
        $guestVisitors = $this->getGuestVisitors();
        $counts = $this->getOnlineCounts();
    @endphp

    <div class="space-y-6">
        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-white dark:bg-dark-bg rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Online</p>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $counts['total_online'] }}</p>
                    </div>
                    <div class="p-3 bg-gray-100 dark:bg-gray-700 rounded-full">
                        <svg class="w-6 h-6 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-dark-bg rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Users Online</p>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $counts['users_online'] }}</p>
                    </div>
                    <div class="p-3 bg-gray-100 dark:bg-gray-700 rounded-full">
                        <svg class="w-6 h-6 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-dark-bg rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Guests Online</p>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $counts['guests_online'] }}</p>
                    </div>
                    <div class="p-3 bg-gray-100 dark:bg-gray-700 rounded-full">
                        <svg class="w-6 h-6 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Online Users -->
        <div class="bg-white dark:bg-dark-bg rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Online Users (Last 15 minutes)</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-4 py-3 font-medium text-gray-700 dark:text-gray-300">User</th>
                            <th class="px-4 py-3 font-medium text-gray-700 dark:text-gray-300">Email</th>
                            <th class="px-4 py-3 font-medium text-gray-700 dark:text-gray-300">IP Address</th>
                            <th class="px-4 py-3 font-medium text-gray-700 dark:text-gray-300">Browser</th>
                            <th class="px-4 py-3 font-medium text-gray-700 dark:text-gray-300">Last Activity</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($onlineUsers as $user)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-4 py-3">
                                    <div class="flex items-center">
                                        <div class="w-2 h-2 bg-green-500 rounded-full mr-2"></div>
                                        <span class="font-medium text-gray-900 dark:text-white">{{ $user['name'] }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ $user['email'] }}</td>
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ $user['ip_address'] }}</td>
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ $user['user_agent'] }}</td>
                                <td class="px-4 py-3 text-gray-500 dark:text-gray-400">
                                    <div>
                                        <div>{{ $user['time_ago'] }}</div>
                                        <div class="text-xs text-gray-400 dark:text-gray-500">{{ $user['last_activity']->format('H:i:s') }}</div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No users online</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Guest Visitors -->
        <div class="bg-white dark:bg-dark-bg rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Guest Visitors (Last 15 minutes)</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-4 py-3 font-medium text-gray-700 dark:text-gray-300">IP Address</th>
                            <th class="px-4 py-3 font-medium text-gray-700 dark:text-gray-300">Browser</th>
                            <th class="px-4 py-3 font-medium text-gray-700 dark:text-gray-300">Last Activity</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($guestVisitors as $guest)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-4 py-3">
                                    <div class="flex items-center">
                                        <div class="w-2 h-2 bg-yellow-500 rounded-full mr-2"></div>
                                        <span class="font-medium text-gray-900 dark:text-white">{{ $guest['ip_address'] }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ $guest['user_agent'] }}</td>
                                <td class="px-4 py-3 text-gray-500 dark:text-gray-400">
                                    <div>
                                        <div>{{ $guest['time_ago'] }}</div>
                                        <div class="text-xs text-gray-400 dark:text-gray-500">{{ $guest['last_activity']->format('H:i:s') }}</div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No guest visitors</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Info Note -->
        <div class="bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-700 rounded-lg p-4">
            <div class="flex">
                <svg class="w-6 h-6 text-gray-600 dark:text-gray-400 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                </svg>
                <div class="text-sm text-gray-700 dark:text-gray-300">
                    <p class="font-medium">Note:</p>
                    <p>Users are considered online if they have been active within the last 15 minutes. This page refreshes automatically every 30 seconds.</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Auto-refresh every 30 seconds
        setTimeout(function() {
            window.location.reload();
        }, 30000);
    </script>
</x-filament-panels::page>
