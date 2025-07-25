<?php

namespace App\Filament\Resources\TaskProjectResource\Pages;

use App\Filament\Resources\TaskProjectResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTaskProject extends EditRecord
{
    protected static string $resource = TaskProjectResource::class;

    

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        $this->redirect(request()->header('Referer'));
    }

}
