<?php

namespace App\Filament\Resources\StatusResource\Pages;

use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\StatusResource;

class EditStatus extends EditRecord
{
    protected static string $resource = StatusResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->before(function ($record) {
                    if ($record->tasks()->exists()) {
                        Notification::make()
                            ->title('Cannot delete status')
                            ->body('This status has related tasks and cannot be deleted.')
                            ->danger()
                            ->send();

                        $this->halt(); // Stop the delete action
                    }
                }),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
