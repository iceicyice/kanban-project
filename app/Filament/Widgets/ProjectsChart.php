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
        $doing = (clone $query)->whereBetween('progress', [1, 50])->count();
        $almost = (clone $query)->whereBetween('progress', [51, 90])->count();
        $done = (clone $query)->where('progress', 100)->count();

        return [
            'datasets' => [
                [
                    'label' => 'Projects',
                    'data' => [$notStarted, $doing, $almost, $done],
                    'backgroundColor' => ['#f87171', '#ffa852', '#60a5fa', '#34d399'], // red, orange, blue, green
                ],
            ],
            'labels' => ['Not Started (0%)', 'Doing (1-50%)', 'Almost Done (51-90%)', 'Done (100%)'],
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
