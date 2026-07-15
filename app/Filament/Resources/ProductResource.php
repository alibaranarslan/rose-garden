<?php

namespace App\Filament\Resources;

use Closure;
use App\Filament\Resources\ProductResource\Pages;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Tag;
use App\Support\AdminActionLogger;
use App\Support\ProductDuplicator;
use App\Support\StorefrontImage;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Concerns\Translatable;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

class ProductResource extends Resource
{
    use Translatable;

    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?string $navigationGroup = 'Ürünler';

    protected static ?string $navigationLabel = 'Tüm Ürünler';

    protected static ?string $modelLabel = 'Ürün';

    protected static ?string $pluralModelLabel = 'Ürünler';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Tabs::make('Ürün Formu')
                ->tabs([
                    Tabs\Tab::make('Temel Bilgiler')
                        ->schema([
                            TextInput::make('name')
                                ->label('Ad')
                                ->required()
                                ->maxLength(255)
                                ->dehydrateStateUsing(fn ($state): string => trim((string) $state))
                                ->live(onBlur: true)
                                ->afterStateUpdated(fn (string $operation, $state, \Filament\Forms\Set $set) => $operation === 'create' && filled($state)
                                    ? $set('slug', Str::slug((string) $state, '-', 'tr'))
                                    : null),

                            TextInput::make('slug')
                                ->label('Slug')
                                ->required()
                                ->maxLength(255)
                                ->regex('/^[A-Za-z0-9]+(?:-[A-Za-z0-9]+)*$/')
                                ->dehydrateStateUsing(fn ($state): string => Str::slug((string) $state, '-', 'tr'))
                                ->unique(Product::class, 'slug', ignoreRecord: true),

                            TextInput::make('sku')
                                ->label('SKU')
                                ->maxLength(100)
                                ->dehydrateStateUsing(fn ($state): ?string => filled($state) ? strtoupper(trim((string) $state)) : null)
                                ->unique(Product::class, 'sku', ignoreRecord: true)
                                ->placeholder('Opsiyonel'),

                            Select::make('status')
                                ->label('Durum')
                                ->options([
                                    'draft' => 'Taslak',
                                    'active' => 'Aktif',
                                    'inactive' => 'Pasif',
                                ])
                                ->default('active')
                                ->required(),

                            Textarea::make('short_description')
                                ->label('Kısa Açıklama')
                                ->rows(3)
                                ->maxLength(220)
                                ->columnSpanFull(),

                            RichEditor::make('description')
                                ->label('Ürün Hikayesi')
                                ->columnSpanFull(),
                        ])
                        ->columns(2),

                    Tabs\Tab::make('Fiyatlandırma')
                        ->schema([
                            TextInput::make('price')
                                ->label('Fiyat')
                                ->numeric()
                                ->minValue(0.01)
                                ->maxValue(999999)
                                ->prefix('₺')
                                ->required(),

                            TextInput::make('sale_price')
                                ->label('İndirimli Fiyat')
                                ->numeric()
                                ->minValue(0)
                                ->maxValue(999999)
                                ->rule(fn (callable $get): Closure => function (string $attribute, mixed $value, Closure $fail) use ($get): void {
                                    if (blank($value) || blank($get('price'))) {
                                        return;
                                    }

                                    if ((float) $value >= (float) $get('price')) {
                                        $fail('İndirimli fiyat normal fiyattan düşük olmalıdır.');
                                    }
                                })
                                ->prefix('₺'),

                            DateTimePicker::make('sale_start')
                                ->label('İndirim Başlangıcı')
                                ->native(false),

                            DateTimePicker::make('sale_end')
                                ->label('İndirim Bitişi')
                                ->after('sale_start')
                                ->native(false),

                            Placeholder::make('vat_note')
                                ->label('')
                                ->content('Fiyatlar KDV dahildir.')
                                ->columnSpanFull(),
                        ])
                        ->columns(2),

                    Tabs\Tab::make('Galeri ve Bileşenler')
                        ->schema([
                            Repeater::make('images')
                                ->label('Ürün Galerisi')
                                ->relationship()
                                ->mutateRelationshipDataBeforeFillUsing(fn (array $data): array => static::normalizeImageRelationshipDataForFileUpload($data))
                                ->schema([
                                    Placeholder::make('current_image_preview')
                                        ->label('Mevcut gorsel')
                                        ->content(fn (?ProductImage $record): HtmlString => static::adminProductImagePreviewHtml($record))
                                        ->visible(fn (?ProductImage $record): bool => filled($record?->image_path))
                                        ->columnSpanFull(),

                                    FileUpload::make('image_path')
                                        ->label('Görsel')
                                        ->disk('public')
                                        ->directory('products')
                                        ->image()
                                        ->imageEditor()
                                        ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                        ->maxSize(5120)
                                        ->required(fn (?ProductImage $record): bool => blank($record?->image_path))
                                        ->dehydrateStateUsing(
                                            fn ($state, ?ProductImage $record) => static::normalizeUploadedImagePath($state, $record?->image_path)
                                        )
                                        ->getUploadedFileNameForStorageUsing(
                                            fn (\Livewire\Features\SupportFileUploads\TemporaryUploadedFile $file): string => Str::random(40).'.'.($file->guessExtension() ?: $file->getClientOriginalExtension())
                                        )
                                        ->columnSpanFull(),

                                    TextInput::make('alt_text')
                                        ->label('Alt Metin')
                                        ->dehydrateStateUsing(fn ($state): string => trim((string) $state))
                                        ->maxLength(255)
                                        ->columnSpanFull(),

                                    Toggle::make('is_primary')
                                        ->label('Kapak Görseli'),
                                ])
                                ->itemLabel(fn (array $state): ?string => filled($state['alt_text'] ?? null) ? (string) $state['alt_text'] : 'Galeri görseli')
                                ->reorderable('sort_order')
                                ->defaultItems(1)
                                ->collapsible()
                                ->collapsed()
                                ->addActionLabel('Görsel Ekle')
                                ->helperText('Müşteri vitrinde galeri sırasını ve kapak görselini bu ayarlara göre görür.')
                                ->columnSpanFull(),

                            Repeater::make('product_highlights')
                                ->label('Ürün Bileşenleri')
                                ->schema([
                                    Select::make('icon')
                                        ->label('İkon')
                                        ->options(static::highlightIconOptions())
                                        ->default('sparkles')
                                        ->required(),

                                    TextInput::make('title')
                                        ->label('Başlık')
                                        ->required()
                                        ->maxLength(80),

                                    Textarea::make('body')
                                        ->label('Açıklama')
                                        ->rows(3)
                                        ->required()
                                        ->maxLength(220)
                                        ->columnSpanFull(),
                                ])
                                ->default([])
                                ->dehydrateStateUsing(fn (?array $state): array => static::normalizeHighlightState($state ?? []))
                                ->itemLabel(fn (array $state): ?string => filled($state['title'] ?? null) ? (string) $state['title'] : 'Bilgi kartı')
                                ->reorderable()
                                ->collapsible()
                                ->helperText('Ürün detay sayfasındaki bilgi kartlarını yönetir.')
                                ->addActionLabel('Bilgi Kartı Ekle')
                                ->columnSpanFull(),
                        ]),

                    Tabs\Tab::make('Kategoriler & Etiketler')
                        ->schema([
                            Select::make('categories')
                                ->label('Kategoriler')
                                ->relationship('categories', 'slug')
                                ->multiple()
                                ->preload()
                                ->searchable()
                                ->required()
                                ->getOptionLabelFromRecordUsing(fn ($record): string => $record->getTranslation('name', app()->getLocale()) ?: $record->slug),

                            Select::make('specialOccasions')
                                ->label('Özel Günler')
                                ->relationship(
                                    name: 'specialOccasions',
                                    titleAttribute: 'slug',
                                    modifyQueryUsing: fn ($query) => $query->where('is_active', true)->orderBy('date_month')->orderBy('date_day'),
                                )
                                ->multiple()
                                ->preload()
                                ->searchable()
                                ->getOptionLabelFromRecordUsing(
                                    fn (\App\Models\SpecialOccasion $record): string => $record->getTranslation('name', 'tr') ?: $record->slug
                                ),

                            Select::make('tags')
                                ->label('Etiketler')
                                ->relationship('tags', 'slug')
                                ->multiple()
                                ->preload()
                                ->searchable()
                                ->getOptionLabelFromRecordUsing(fn (Tag $record): string => $record->getTranslation('name', app()->getLocale()) ?: $record->slug)
                                ->createOptionForm([
                                    TextInput::make('name_tr')
                                        ->label('Etiket Adı')
                                        ->required()
                                        ->maxLength(80),
                                ])
                                ->createOptionUsing(function (array $data): int {
                                    $label = trim((string) ($data['name_tr'] ?? ''));
                                    $tag = Tag::query()->create([
                                        'slug' => Str::slug($label, '-', 'tr'),
                                        'name' => ['tr' => $label, 'en' => $label, 'ku' => $label],
                                    ]);

                                    return $tag->getKey();
                                }),
                        ])
                        ->columns(2),

                    Tabs\Tab::make('Varyantlar')
                        ->schema([
                            Repeater::make('variants')
                                ->label('Varyantlar')
                                ->relationship()
                                ->schema([
                                    TextInput::make('name')
                                        ->label('Varyant Adı')
                                        ->maxLength(255)
                                        ->dehydrateStateUsing(fn ($state): string => trim((string) $state))
                                        ->required(),

                                    TextInput::make('price')
                                        ->label('Fiyat')
                                        ->numeric()
                                        ->minValue(0.01)
                                        ->maxValue(999999)
                                        ->prefix('₺')
                                        ->required(),

                                    TextInput::make('sale_price')
                                        ->label('İndirimli Fiyat')
                                        ->numeric()
                                        ->minValue(0)
                                        ->maxValue(999999)
                                        ->rule(fn (callable $get): Closure => function (string $attribute, mixed $value, Closure $fail) use ($get): void {
                                            if (blank($value) || blank($get('price'))) {
                                                return;
                                            }

                                            if ((float) $value >= (float) $get('price')) {
                                                $fail('Varyant indirimli fiyatı normal fiyattan düşük olmalıdır.');
                                            }
                                        })
                                        ->prefix('₺'),

                                    Select::make('stock_status')
                                        ->label('Stok')
                                        ->options([
                                            'in_stock' => 'Stokta',
                                            'out_of_stock' => 'Stok Yok',
                                        ])
                                        ->default('in_stock')
                                        ->required(),

                                    Toggle::make('is_active')
                                        ->label('Aktif')
                                        ->default(true),
                                ])
                                ->reorderable('sort_order')
                                ->collapsible()
                                ->addActionLabel('Varyant Ekle')
                                ->columnSpanFull(),
                        ]),

                    Tabs\Tab::make('Stok ve Teslimat')
                        ->schema([
                            Select::make('stock_status')
                                ->label('Stok Durumu')
                                ->options([
                                    'in_stock' => 'Stokta',
                                    'out_of_stock' => 'Stok Yok',
                                ])
                                ->default('in_stock')
                                ->required(),

                            Toggle::make('is_featured')
                                ->label('Öne Çıkan'),

                            Toggle::make('is_new')
                                ->label('Yeni Ürün'),

                            Textarea::make('delivery_note')
                                ->label('Teslimat Notu')
                                ->rows(3)
                                ->maxLength(500)
                                ->dehydrateStateUsing(fn ($state): string => trim((string) $state))
                                ->columnSpanFull(),
                        ])
                        ->columns(2),

                    Tabs\Tab::make('SEO')
                        ->schema([
                            TextInput::make('meta_title')
                                ->label('Meta Başlık')
                                ->maxLength(70)
                                ->dehydrateStateUsing(fn ($state): string => trim((string) $state)),

                            Textarea::make('meta_description')
                                ->label('Meta Açıklama')
                                ->maxLength(160)
                                ->dehydrateStateUsing(fn ($state): string => trim((string) $state)),
                        ])
                        ->columns(2),
                ])
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('primary_image')
                    ->label('Görsel')
                    ->getStateUsing(fn (Product $record): string => static::adminProductImageUrl($record))
                    ->defaultImageUrl(asset('images/product-placeholder.svg'))
                    ->width(56)
                    ->height(56),

                TextColumn::make('name')
                    ->label('Ad')
                    ->formatStateUsing(fn (Product $record): string => $record->getTranslation('name', 'tr') ?: $record->slug)
                    ->searchable()
                    ->sortable(),

                TextColumn::make('sku')
                    ->label('SKU')
                    ->searchable(),

                TextColumn::make('price')
                    ->label('Fiyat')
                    ->money('TRY')
                    ->sortable(),

                BadgeColumn::make('stock_status')
                    ->label('Stok')
                    ->colors([
                        'success' => 'in_stock',
                        'danger' => 'out_of_stock',
                    ])
                    ->formatStateUsing(fn (string $state): string => $state === 'in_stock' ? 'Stokta' : 'Tükendi'),

                BadgeColumn::make('status')
                    ->label('Durum')
                    ->colors([
                        'success' => 'active',
                        'warning' => 'draft',
                        'gray' => 'inactive',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'active' => 'Aktif',
                        'draft' => 'Taslak',
                        'inactive' => 'Pasif',
                        default => $state,
                    }),

                IconColumn::make('is_featured')
                    ->label('Öne Çıkan')
                    ->boolean()
                    ->sortable(),

                TextColumn::make('view_count')
                    ->label('Görüntülenme')
                    ->numeric()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Durum')
                    ->options([
                        'draft' => 'Taslak',
                        'active' => 'Aktif',
                        'inactive' => 'Pasif',
                    ]),

                SelectFilter::make('stock_status')
                    ->label('Stok')
                    ->options([
                        'in_stock' => 'Stokta',
                        'out_of_stock' => 'Tükendi',
                    ]),

                TernaryFilter::make('is_featured')
                    ->label('Öne Çıkan'),

                TernaryFilter::make('is_new')
                    ->label('Yeni Ürün'),
            ])
            ->headerActions([
                Action::make('create')
                    ->label('Yeni Ürün')
                    ->url(fn (): string => static::getUrl('create')),
            ])
            ->actions([
                EditAction::make(),

                Action::make('duplicate')
                    ->label('Kopyala')
                    ->icon('heroicon-o-document-duplicate')
                    ->requiresConfirmation()
                    ->modalHeading('Ürünü kopyala')
                    ->modalDescription('Bu işlem ürünün yeni bir kopyasını oluşturur. Kopya ürün yayına alınmadan önce fiyat, stok, görsel ve kategori bilgilerini kontrol edin.')
                    ->modalSubmitActionLabel('Kopyayı oluştur')
                    ->action(function (Product $record): void {
                        $copy = ProductDuplicator::duplicate($record);

                        AdminActionLogger::record('product.duplicate', $record, [
                            'copy_id' => $copy->getKey(),
                        ]);

                        Notification::make()
                            ->success()
                            ->title('Ürün kopyası oluşturuldu.')
                            ->send();
                    }),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    BulkAction::make('activate')
                        ->label('Aktifleştir')
                        ->requiresConfirmation()
                        ->modalHeading('Seçili ürünleri aktifleştir')
                        ->modalDescription('Seçili ürünler storefront içinde görünür hale gelebilir. Görsel, fiyat ve stok bilgilerini kontrol ettiğinizden emin olun.')
                        ->modalSubmitActionLabel('Aktifleştir')
                        ->action(function (Collection $records): void {
                            $records->each->update(['status' => 'active']);
                            AdminActionLogger::record('product.bulk_activate', null, ['count' => $records->count()]);
                        }),

                    BulkAction::make('deactivate')
                        ->label('Pasifleştir')
                        ->requiresConfirmation()
                        ->modalHeading('Seçili ürünleri pasifleştir')
                        ->modalDescription('Seçili ürünler storefront vitrinlerinden kalkabilir. Aktif kampanya veya özel gün bağlantılarını kontrol edin.')
                        ->modalSubmitActionLabel('Pasifleştir')
                        ->action(function (Collection $records): void {
                            $records->each->update(['status' => 'inactive']);
                            AdminActionLogger::record('product.bulk_deactivate', null, ['count' => $records->count()]);
                        }),

                    BulkAction::make('set_featured')
                        ->label('Öne Çıkar')
                        ->requiresConfirmation()
                        ->modalHeading('Seçili ürünleri öne çıkar')
                        ->modalDescription('Bu işlem ana vitrin ve öneri alanlarının ticari sıralamasını etkileyebilir.')
                        ->modalSubmitActionLabel('Öne çıkar')
                        ->action(function (Collection $records): void {
                            $records->each->update(['is_featured' => true]);
                            AdminActionLogger::record('product.bulk_set_featured', null, ['count' => $records->count()]);
                        }),

                    BulkAction::make('unset_featured')
                        ->label('Öne Çıkarmayı Kaldır')
                        ->requiresConfirmation()
                        ->modalHeading('Öne çıkarma işaretini kaldır')
                        ->modalDescription('Seçili ürünler vitrin önceliğini kaybedebilir. Bu değişiklik merchandising görünümünü etkiler.')
                        ->modalSubmitActionLabel('Kaldır')
                        ->action(function (Collection $records): void {
                            $records->each->update(['is_featured' => false]);
                            AdminActionLogger::record('product.bulk_unset_featured', null, ['count' => $records->count()]);
                        }),

                    DeleteBulkAction::make()
                        ->label('Sil')
                        ->modalHeading('Seçili ürünleri sil')
                        ->modalDescription('Silme işlemi ürünlerin storefront ve sipariş geçmişiyle ilişkisini etkileyebilir. Emin değilseniz pasifleştirmeyi tercih edin.'),
                ]),
            ])
            ->defaultSort('sort_order');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }

    public static function getTranslatableLocales(): array
    {
        return ['tr', 'en', 'ku'];
    }

    /**
     * @return array<string, string>
     */
    public static function highlightIconOptions(): array
    {
        return [
            'sparkles' => 'Butik Hazırlık',
            'truck' => 'Teslimat',
            'gift' => 'Hediye Etkisi',
            'sun' => 'Bakım Notu',
            'chat-bubble-left-right' => 'Destek',
            'shield-check' => 'Güven',
        ];
    }

    /**
     * @param  array<int, array<string, mixed>>  $state
     * @return array<int, array{icon:string,title:string,body:string,sort_order:int}>
     */
    public static function normalizeHighlightState(array $state): array
    {
        return collect($state)
            ->filter(fn ($item) => is_array($item) && filled($item['title'] ?? null) && filled($item['body'] ?? null))
            ->values()
            ->map(fn (array $item, int $index): array => [
                'icon' => (string) ($item['icon'] ?? 'sparkles'),
                'title' => trim((string) $item['title']),
                'body' => trim((string) $item['body']),
                'sort_order' => $index + 1,
            ])
            ->all();
    }

    public static function normalizeUploadedImagePath(mixed $state, ?string $existingPath = null): ?string
    {
        if (blank($state)) {
            return filled($existingPath) ? static::stripStoragePrefix((string) $existingPath) : null;
        }

        if (is_string($state)) {
            return static::stripStoragePrefix($state);
        }

        if (is_array($state)) {
            $path = collect($state)
                ->flatten()
                ->filter(fn ($value): bool => is_string($value) && filled($value))
                ->first();

            return $path
                ? static::stripStoragePrefix($path)
                : (filled($existingPath) ? static::stripStoragePrefix((string) $existingPath) : null);
        }

        return static::stripStoragePrefix((string) $state);
    }

    public static function normalizeImagePathForFileUpload(mixed $state): mixed
    {
        if (is_string($state)) {
            return static::stripStoragePrefix($state);
        }

        if (is_array($state)) {
            return collect($state)
                ->map(fn ($value) => is_string($value) ? static::stripStoragePrefix($value) : $value)
                ->all();
        }

        return $state;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public static function normalizeImageRelationshipDataForFileUpload(array $data): array
    {
        if (array_key_exists('image_path', $data)) {
            $data['image_path'] = static::normalizeImagePathForFileUpload($data['image_path']);
        }

        return $data;
    }

    public static function adminProductImageUrl(Product $record): string
    {
        $record->loadMissing(['images' => fn ($query) => $query->orderBy('sort_order')]);

        $resolved = StorefrontImage::publicImgSrc(
            StorefrontImage::resolveProduct($record->primaryImage, $record->slug, $record->name)
        );

        if (Str::startsWith($resolved, ['http://', 'https://', 'data:'])) {
            return $resolved;
        }

        return url('/'.ltrim($resolved, '/'));
    }

    public static function adminProductImagePreviewHtml(?ProductImage $record): HtmlString
    {
        if (! $record || blank($record->image_path)) {
            return new HtmlString('<span class="text-sm text-gray-500">Bu galeri satirinda henuz gorsel yok.</span>');
        }

        $product = $record->product;
        $product?->loadMissing(['images' => fn ($query) => $query->orderBy('sort_order')]);

        $resolved = StorefrontImage::publicImgSrc(
            StorefrontImage::resolveProduct(
                $record->image_path,
                $product?->slug,
                $product?->name,
            )
        );

        if (! Str::startsWith($resolved, ['http://', 'https://', 'data:'])) {
            $resolved = url('/'.ltrim($resolved, '/'));
        }

        $alt = e($record->alt_text ?: ($product?->name ?? 'Urun gorseli'));
        $src = e($resolved);

        return new HtmlString(<<<HTML
<div class="flex items-center gap-4 rounded-xl border border-gray-200 bg-white/70 p-3 shadow-sm dark:border-gray-700 dark:bg-gray-900/60">
    <img src="{$src}" alt="{$alt}" class="h-24 w-24 rounded-lg object-cover ring-1 ring-gray-200 dark:ring-gray-700" loading="lazy">
    <div class="min-w-0 text-sm text-gray-600 dark:text-gray-300">
        <div class="font-medium text-gray-900 dark:text-gray-100">Secili urun gorseli</div>
        <div class="mt-1 truncate">{$alt}</div>
        <div class="mt-2 text-xs text-gray-500 dark:text-gray-400">Yeni dosya yuklerseniz bu gorsel degisir; mevcut gorsel kaydetme sirasinda korunur.</div>
    </div>
</div>
HTML);
    }

    private static function stripStoragePrefix(string $path): string
    {
        $normalized = ltrim(str_replace('\\', '/', $path), '/');

        return Str::startsWith($normalized, 'storage/')
            ? substr($normalized, strlen('storage/'))
            : $normalized;
    }
}
