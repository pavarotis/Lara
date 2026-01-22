<?php

namespace App\Filament\Pages\Reports;

use App\Models\User;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;

class WhosOnline extends Page
{
    protected static string|\UnitEnum|null $navigationGroup = 'Reports';

    protected static ?int $navigationSort = 2;

    protected string $view = 'filament.pages.reports.whos-online';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-user-circle';

    protected static ?string $navigationLabel = 'Who\'s Online';

    public function getTitle(): string
    {
        return 'Who\'s Online';
    }

    public function getOnlineUsers(): array
    {
        $onlineThreshold = now()->subMinutes(15)->timestamp;

        return DB::table('sessions')
            ->where('last_activity', '>=', $onlineThreshold)
            ->whereNotNull('user_id')
            ->join('users', 'sessions.user_id', '=', 'users.id')
            ->select(
                'users.id',
                'users.name',
                'users.email',
                'sessions.ip_address',
                'sessions.user_agent',
                'sessions.last_activity'
            )
            ->orderBy('sessions.last_activity', 'desc')
            ->get()
            ->map(function ($session) {
                return [
                    'id' => $session->id,
                    'name' => $session->name,
                    'email' => $session->email,
                    'ip_address' => $session->ip_address,
                    'user_agent' => $this->parseUserAgent($session->user_agent),
                    'last_activity' => now()->setTimestamp($session->last_activity),
                    'time_ago' => now()->setTimestamp($session->last_activity)->diffForHumans(),
                ];
            })
            ->toArray();
    }

    public function getGuestVisitors(): array
    {
        $onlineThreshold = now()->subMinutes(15)->timestamp;

        return DB::table('sessions')
            ->where('last_activity', '>=', $onlineThreshold)
            ->whereNull('user_id')
            ->select(
                'id',
                'ip_address',
                'user_agent',
                'last_activity'
            )
            ->orderBy('last_activity', 'desc')
            ->get()
            ->map(function ($session) {
                return [
                    'ip_address' => $session->ip_address,
                    'user_agent' => $this->parseUserAgent($session->user_agent),
                    'last_activity' => now()->setTimestamp($session->last_activity),
                    'time_ago' => now()->setTimestamp($session->last_activity)->diffForHumans(),
                ];
            })
            ->toArray();
    }

    public function getOnlineCounts(): array
    {
        $onlineThreshold = now()->subMinutes(15)->timestamp;

        return [
            'total_online' => DB::table('sessions')
                ->where('last_activity', '>=', $onlineThreshold)
                ->count(),
            'users_online' => DB::table('sessions')
                ->where('last_activity', '>=', $onlineThreshold)
                ->whereNotNull('user_id')
                ->count(),
            'guests_online' => DB::table('sessions')
                ->where('last_activity', '>=', $onlineThreshold)
                ->whereNull('user_id')
                ->count(),
        ];
    }

    protected function parseUserAgent(?string $userAgent): string
    {
        if (! $userAgent) {
            return 'Unknown';
        }

        // Simple user agent parsing
        if (str_contains($userAgent, 'Chrome')) {
            return 'Chrome';
        } elseif (str_contains($userAgent, 'Firefox')) {
            return 'Firefox';
        } elseif (str_contains($userAgent, 'Safari')) {
            return 'Safari';
        } elseif (str_contains($userAgent, 'Edge')) {
            return 'Edge';
        } elseif (str_contains($userAgent, 'Opera')) {
            return 'Opera';
        }

        return 'Other';
    }
}
