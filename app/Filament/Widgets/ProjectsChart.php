<?php

namespace App\Filament\Widgets;

use App\Models\TaskProject;
use Filament\Widgets\ChartWidget;

class ProjectsChart extends ChartWidget
{
    protected static ?string $heading = 'Projects Progress Overview';

    protected function getData(): array
    {
        $query = TaskProject::query()
            ->whereHas('users', fn ($q) => 
                $q->where('users.id', auth()->id())
            );

        $notStarted = (clone $query)->where('progress', 0)->count();
        $doing = (clone $query)->whereBetween('progress', [1, 99])->count();
        $done = (clone $query)->where('progress', 100)->count();

        return [
            'datasets' => [
                [
                    'label' => 'Projects',
                    'data' => [$notStarted, $doing, $done],
                    'backgroundColor' => ['#f87171', '#60a5fa', '#34d399'], // red, blue, green
                ],
            ],
            'labels' => ['Not Started', 'Doing', 'Done'],
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'position' => 'bottom',
                ],
            ],
            'scales' => [
                'x' => ['display' => false],
                'y' => ['display' => false],
            ],
        ];
    }
}
