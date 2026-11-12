<?php

namespace App\Filament\Resources\Pages;

use App\Filament\Resources\Pages\Pages\EditPage;
use App\Filament\Resources\Pages\Pages\ListPages;
use App\Models\Page;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

/**
 * About / contact / shipping / terms. The four pages are fixed (no create, no delete);
 * the admin edits title, Markdown body, description and whether the page is published.
 */
class PageResource extends Resource
{
    protected static ?string $model = Page::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static ?string $modelLabel = 'صفحه';

    protected static ?string $pluralModelLabel = 'صفحه‌های سایت';

    protected static string|UnitEnum|null $navigationGroup = 'تنظیمات فروشگاه';

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make()
                    ->schema([
                        TextInput::make('title')->label('عنوان')->required()->maxLength(120),
                        MarkdownEditor::make('body')
                            ->label('متن صفحه')
                            ->helperText('جاهایی که داخل [ ] هستند را با اطلاعات واقعی پر کنید. «##» یعنی تیتر و «-» یعنی فهرست.')
                            ->toolbarButtons([['heading', 'bold', 'link'], ['bulletList', 'orderedList', 'blockquote'], ['undo', 'redo']])
                            ->maxLength(20000),
                        Textarea::make('meta_description')
                            ->label('توضیح کوتاه برای گوگل')
                            ->helperText('یک جملهٔ کوتاه (حداکثر ۱۶۰ حرف) که در نتایج جست‌وجو زیر عنوان دیده می‌شود.')
                            ->rows(2)
                            ->maxLength(160),
                        Toggle::make('is_published')
                            ->label('منتشر شود')
                            ->helperText('تا روشن نشود، صفحه در سایت دیده نمی‌شود و لینکش در فوتر نمی‌آید. اول جاهای [ ] را پر کنید.'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->label('عنوان'),
                TextColumn::make('slug')->label('نشانی')
                    ->formatStateUsing(fn (string $state) => '/'.$state),
                IconColumn::make('is_published')->label('منتشرشده')->boolean(),
                TextColumn::make('updated_at')->label('آخرین ویرایش')
                    ->formatStateUsing(fn ($state) => \App\Support\Jalali::dateTime($state)),
            ])
            ->recordActions([
                EditAction::make(),
                Action::make('view')
                    ->label('دیدن در سایت')
                    ->icon(Heroicon::OutlinedEye)
                    ->url(fn (Page $record) => $record->url(), shouldOpenInNewTab: true),
            ])
            ->paginated(false);
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPages::route('/'),
            'edit' => EditPage::route('/{record}/edit'),
        ];
    }
}
