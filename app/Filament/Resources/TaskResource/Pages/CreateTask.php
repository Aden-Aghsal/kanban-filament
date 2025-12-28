<?php

namespace App\Filament\Resources\TaskResource\Pages;

use App\Filament\Resources\TaskResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateTask extends CreateRecord
{
    protected static string $resource = TaskResource::class;
    protected function mutateFormDataBeforeCreate(array $data): array
{
    if (! auth()->user()->hasRole('admin')) {
        $data['user_id'] = auth()->id();
    }

    return $data;
}

 protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }


}


