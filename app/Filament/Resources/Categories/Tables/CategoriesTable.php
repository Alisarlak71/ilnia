<?php

namespace App\Filament\Resources\Categories\Tables;

use App\Models\Category;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CategoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->withCount('products'))
            ->defaultSort('sort')
            ->columns([
                TextColumn::make('name')
                    ->label('نام')
                    ->searchable(),
                TextColumn::make('slug')
                    ->label('نامک'),
                TextColumn::make('products_count')
                    ->label('تعداد محصول'),
                TextColumn::make('sort')
                    ->label('ترتیب')
                    ->sortable(),
            ])
            ->recordActions([
                EditAction::make(),
                // A category that still has products cannot be deleted (the database also blocks it).
                DeleteAction::make()
                    ->hidden(fn (Category $record) => $record->products_count > 0),
            ]);
    }
}
