<?php

namespace App\Filament\Resources\Products\Schemas;

use App\Filament\Forms\IntegerInput;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('اطلاعات اصلی')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label('نام محصول')
                            ->placeholder('مثلاً عسل طبیعی کوهستان')
                            ->required()
                            ->maxLength(255),
                        Select::make('category_id')
                            ->label('دسته')
                            ->relationship('category', 'name')
                            ->preload()
                            ->required(),
                        TextInput::make('slug')
                            ->label('نامک (آدرس)')
                            ->helperText('خالی بگذارید تا خودکار از نام ساخته شود.')
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        Toggle::make('is_active')
                            ->label('نمایش در فروشگاه')
                            ->helperText('فقط محصول روشن در سایت دیده می‌شود.')
                            ->default(false)
                            ->inline(false),
                        Textarea::make('short_description')
                            ->label('توضیح کوتاه')
                            ->helperText('یک یا دو جمله، برای کارت محصول.')
                            ->maxLength(500)
                            ->rows(2)
                            ->columnSpanFull(),
                        Textarea::make('description')
                            ->label('توضیحات')
                            ->rows(6)
                            ->columnSpanFull(),
                        Textarea::make('story')
                            ->label('داستان محصول (از کجا می‌آید)')
                            ->helperText('منطقه، ایل و روش برداشت. فقط چیزهایی که واقعی و قابل اثبات‌اند.')
                            ->rows(4)
                            ->columnSpanFull(),
                        Textarea::make('storage_instructions')
                            ->label('شرایط نگهداری')
                            ->placeholder('مثلاً دور از نور مستقیم آفتاب و در دمای اتاق نگهداری شود.')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),

                Section::make('وزن‌ها، قیمت و موجودی')
                    ->description('هر وزن جداگانه قیمت و موجودی دارد. وزنی که قیمت ندارد قابل خرید نیست.')
                    ->schema([
                        Repeater::make('variants')
                            ->hiddenLabel()
                            ->relationship()
                            ->orderColumn('sort')
                            ->columns(3)
                            ->itemLabel(fn (array $state): ?string => $state['title'] ?? null)
                            ->addActionLabel('افزودن وزن')
                            ->defaultItems(1)
                            ->schema([
                                TextInput::make('title')
                                    ->label('عنوان')
                                    ->placeholder('مثلاً ۱ کیلوگرم')
                                    ->required()
                                    ->maxLength(100),
                                IntegerInput::make('weight_grams')
                                    ->label('وزن')
                                    ->suffix('گرم')
                                    ->helperText('برای محاسبهٔ هزینهٔ ارسال.')
                                    ->required(),
                                IntegerInput::make('price')
                                    ->label('قیمت')
                                    ->suffix('تومان'),
                                IntegerInput::make('stock')
                                    ->label('موجودی')
                                    ->suffix('عدد')
                                    ->default(0)
                                    ->required(),
                                TextInput::make('sku')
                                    ->label('کد کالا (اختیاری)')
                                    ->maxLength(100)
                                    ->distinct()
                                    ->unique(table: 'product_variants', column: 'sku', ignoreRecord: true),
                                Toggle::make('is_active')
                                    ->label('فعال')
                                    ->default(true)
                                    ->inline(false),
                            ]),
                    ]),

                Section::make('عکس‌ها')
                    ->description('فقط عکس واقعی از محصول خودمان. اولین عکس، عکس اصلی است.')
                    ->schema([
                        Repeater::make('images')
                            ->hiddenLabel()
                            ->relationship()
                            ->orderColumn('sort')
                            ->columns(2)
                            ->addActionLabel('افزودن عکس')
                            ->defaultItems(0)
                            ->schema([
                                FileUpload::make('path')
                                    ->label('عکس')
                                    ->image()
                                    ->disk('public')
                                    ->directory('products')
                                    ->visibility('public')
                                    ->maxSize(4096)
                                    ->required(),
                                TextInput::make('alt')
                                    ->label('توضیح عکس (برای نابینایان و گوگل)')
                                    ->placeholder('مثلاً شیشهٔ عسل ۱ کیلویی ایلنیا')
                                    ->helperText('خالی بماند، نام محصول استفاده می‌شود.')
                                    ->maxLength(255),
                            ]),
                    ]),
            ]);
    }
}
