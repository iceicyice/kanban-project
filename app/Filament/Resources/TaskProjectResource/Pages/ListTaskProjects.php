<?php

namespace App\Filament\Resources\TaskProjectResource\Pages;

use App\Filament\Resources\TaskProjectResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTaskProjects extends ListRecords
{
    protected static string $resource = TaskProjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
