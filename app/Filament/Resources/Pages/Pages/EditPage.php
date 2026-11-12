<?php

namespace App\Filament\Resources\Pages\Pages;

use App\Filament\Resources\Pages\PageResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\EditRecord;

class EditPage extends EditRecord
{
    protected static string $resource = PageResource::class;

    protected function getHeaderActions(): array
    {
        // The four pages are fixed: no delete.
        return [
            Action::make('view')
                ->label('دیدن در سایت')
                ->url(fn () => $this->getRecord()->url(), shouldOpenInNewTab: true),
        ];
    }
}
