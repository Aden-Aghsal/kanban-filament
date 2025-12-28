<?php

namespace App\Filament\User\Resources\TaskResource\Pages;

use App\Filament\User\Resources\TaskResource;
use Filament\Resources\Pages\CreateRecord;
use App\Enums\TaskStatus;

class CreateTask extends CreateRecord
{
    protected static string $resource = TaskResource::class;

    /**
     * Modifikasi data sebelum membuat task
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Assign user login sebagai pemilik task
        $data['user_id'] = auth()->id();

        // Optional: set status default jika belum ada
        if (!isset($data['status'])) {
            $data['status'] = TaskStatus::TODO;
        }

        return $data;
    }

    /**
     * Redirect setelah create task
     */
    protected function getRedirectUrl(): string
    {
        // Redirect ke halaman index TaskResource
        return $this->getResource()::getUrl('index');
    }
}
