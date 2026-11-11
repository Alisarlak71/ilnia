<?php

namespace App\Filament\Resources\Categories\Pages;

use App\Filament\Resources\Categories\CategoryResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCategory extends EditRecord
{
    protected static string $resource = CategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // A category that still has products cannot be deleted.
            DeleteAction::make()
                ->hidden(fn () => $this->getRecord()->products()->exists()),
        ];
    }
}
