<?php

namespace App\Filament\Resources\Orders;

use App\Enums\OrderStatus;
use App\Filament\Resources\Orders\Pages\EditOrder;
use App\Filament\Resources\Orders\Pages\ListOrders;
use App\Filament\Resources\Orders\RelationManagers\ItemsRelationManager;
use App\Filament\Resources\Orders\RelationManagers\PaymentsRelationManager;
use App\Filament\Resources\Orders\Schemas\OrderForm;
use App\Filament\Resources\Orders\Tables\OrdersTable;
use App\Models\Order;
use App\Support\Digits;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

/**
 * Orders are created only by checkout. In the panel the admin can only change the
 * status (allowed transitions) and enter the postal tracking code.
 */
class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShoppingCart;

    protected static ?string $modelLabel = 'سفارش';

    protected static ?string $pluralModelLabel = 'سفارش‌ها';

    protected static string|UnitEnum|null $navigationGroup = 'فروش';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'number';

    public static function form(Schema $schema): Schema
    {
        return OrderForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return OrdersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            ItemsRelationManager::class,
            PaymentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListOrders::route('/'),
            'edit' => EditOrder::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    /** Badge in the menu: orders that need the admin's attention. */
    public static function getNavigationBadge(): ?string
    {
        $count = Order::where('status', OrderStatus::NeedsAttention)->count();

        return $count > 0 ? Digits::toPersian($count) : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }
}
