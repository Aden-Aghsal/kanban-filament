<?php

namespace App\Filament\User\Resources\TaskResource\Pages;

use App\Filament\User\Resources\TaskResource;
use Filament\Resources\Pages\EditRecord;
use Filament\Actions;

class EditTask extends EditRecord
{
    protected static string $resource = TaskResource::class;

    /**
     * Tombol di header halaman edit task
     */
    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(), // tetap bisa hapus task
        ];
    }

    /**
     * Redirect setelah update task
     */
    protected function getRedirectUrl(): string
    {
        // Redirect ke halaman index TaskResource
        return $this->getResource()::getUrl('index');
    }

    /**
     * Optional: pastikan user login tetap menjadi pemilik task jika tidak ingin berubah
     * Uncomment jika ingin enforce user_id tetap sama
     */
    // protected function mutateFormDataBeforeSave(array $data): array
    // {
    //     $data['user_id'] = $this->record->user_id;
    //     return $data;
    // }
}
