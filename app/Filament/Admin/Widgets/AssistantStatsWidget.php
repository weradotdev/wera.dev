<?php

namespace App\Filament\Admin\Widgets;

use App\Models\AgentConversation;
use App\Models\AssistantActionRequest;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AssistantStatsWidget extends StatsOverviewWidget
{
    protected ?string $pollingInterval = '60s';

    protected function getStats(): array
    {
        $total = AgentConversation::query()->count();
        $mobile = AgentConversation::query()->where('channel', 'mobile')->count();
        $whatsapp = AgentConversation::query()->where('channel', 'whatsapp')->count();
        $telegram = AgentConversation::query()->where('channel', 'telegram')->count();
        $pending = AssistantActionRequest::query()->pending()->count();

        return [
            Stat::make('Total conversations', (string) $total)
                ->icon('heroicon-o-chat-bubble-left-right'),
            Stat::make('Mobile', (string) $mobile)
                ->icon('heroicon-o-device-phone-mobile')
                ->color('primary'),
            Stat::make('WhatsApp', (string) $whatsapp)
                ->icon('heroicon-o-chat-bubble-oval-left-ellipsis')
                ->color('success'),
            Stat::make('Telegram', (string) $telegram)
                ->icon('heroicon-o-paper-airplane')
                ->color('info'),
            Stat::make('Pending actions', (string) $pending)
                ->icon('heroicon-o-clock')
                ->color($pending > 0 ? 'warning' : 'gray')
                ->description($pending > 0 ? 'Awaiting confirmation' : 'No pending actions'),
        ];
    }
}
