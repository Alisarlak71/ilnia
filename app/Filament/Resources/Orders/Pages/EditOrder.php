<?php

namespace App\Filament\Resources\Orders\Pages;

use App\Enums\OrderStatus;
use App\Filament\Resources\Orders\OrderResource;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Validation\ValidationException;

class EditOrder extends EditRecord
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return []; // Orders are never deleted.
    }

    /**
     * Only `status` (allowed transition) and `tracking_code` may change here.
     * Everything else in the form is display-only.
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $current = $this->getRecord()->status;
        $requested = isset($data['status']) ? OrderStatus::tryFrom((string) $data['status']) : $current;

        if ($requested === null || ($requested !== $current && ! in_array($requested, $current->adminTransitions(), true))) {
            throw ValidationException::withMessages(['data.status' => 'این تغییر وضعیت مجاز نیست.']);
        }

        return [
            'status' => $requested,
            'tracking_code' => filled($data['tracking_code'] ?? null) ? trim((string) $data['tracking_code']) : null,
        ];
    }
}
