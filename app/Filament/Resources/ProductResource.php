<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Category;
use App\Models\Product;
use App\Models\SpecialOccasion;
use App\Models\Tag;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
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
            Tabs::make('Ürün Formu')->tabs([

                Tabs\Tab::make('Temel Bilgiler')->schema([
                    TextInput::make('name')
                        ->label('Ad')
                        ->required()
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn (string $operation, $state, \Filament\Forms\Set $set) =>
                            $operation === 'create' ? $set('slug', Str::slug($state)) : null),

                    TextInput::make('slug')
                        ->label('Slug')
                        ->required()
                        ->unique(Product::class, 'slug', ignoreRecord: true),

                    Textarea::make('short_description')
                        ->label('Kısa Açıklama')
                        ->maxLength(200)
                        ->rows(2),

                    RichEditor::make('description')
                        ->label('Açıklama')
                        ->columnSpanFull(),

                    TextInput::make('sku')
                        ->label('SKU')
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
                ])->columns(2),

                Tabs\Tab::make('Fiyatlandırma')->schema([
                    TextInput::make('price')
                        ->label('Fiyat')
                        ->numeric()
                        ->prefix('₺')
                        ->required(),

                    TextInput::make('sale_price')
                        ->label('İndirimli Fiyat')
                        ->numeric()
                        ->prefix('₺'),

                    DateTimePicker::make('sale_start')
                        ->label('İndirim Başlangıcı')
                        ->native(false),

                    DateTimePicker::make('sale_end')
                        ->label('İndirim Bitişi')
                        ->native(false),

                    Placeholder::make('kdv_note')
                        ->label('')
                        ->content('Fiyatlar KDV dahildir.')
                        ->columnSpanFull(),
                ])->columns(2),

                Tabs\Tab::make('Görseller')->schema([
                    FileUpload::make('product_images')
                        ->label('Ürün Görselleri')
                        ->image()
                        ->multiple()
                        ->reorderable()
                        ->maxFiles(10)
                        ->directory('products')
                        ->maxSize(5120)
                        ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/gif', 'application/pdf'])
                        ->getUploadedFileNameForStorageUsing(fn (\Livewire\Features\SupportFileUploads\TemporaryUploadedFile $file): string => \Illuminate\Support\Str::random(40) . '.' . $file->getClientOriginalExtension())
                        ->columnSpanFull(),
                ]),

                Tabs\Tab::make('Kategoriler & Etiketler')->schema([
                    Select::make('categories')
                        ->label('Kategoriler')
                        ->relationship('categories', 'name')
                        ->multiple()
                        ->searchable()
                        ->preload()
                        ->getOptionLabelFromRecordUsing(fn ($record) => $record->getTranslation('name', app()->getLocale())),

                    Select::make('special_occasions')
                        ->label('Özel Günler')
                        ->options(
                            SpecialOccasion::active()->get()
                                ->mapWithKeys(fn ($o) => [$o->id => $o->getTranslation('name', 'tr')])
                        )
                        ->multiple(),

                    TagsInput::make('tags_list')
                        ->label('Etiketler')
                        ->placeholder('Etiket ekle...')
                        ->suggestions(Tag::all()->map(fn ($t) => $t->getTranslation('name', 'tr'))->toArray()),
                ]),

                Tabs\Tab::make('Varyantlar')->schema([
                    Repeater::make('variants')
                        ->label('Varyantlar')
                        ->relationship()
                        ->schema([
                            TextInput::make('name')
                                ->label('Varyant Adı (TR)')
                                ->required(),

                            TextInput::make('price')
                                ->label('Fiyat')
                                ->numeric()
                                ->prefix('₺')
                                ->required(),

                            TextInput::make('sale_price')
                                ->label('İndirimli Fiyat')
                                ->numeric()
                                ->prefix('₺'),

                            Select::make('stock_status')
                                ->label('Stok')
                                ->options([
                                    'in_stock' => 'Stokta',
                                    'out_of_stock' => 'Stok Yok',
                                ])
                                ->default('in_stock'),

                            Toggle::make('is_active')
                                ->label('Aktif')
                                ->default(true),
                        ])
                        ->reorderable('sort_order')
                        ->columns(5)
                        ->columnSpanFull(),
                ]),

                Tabs\Tab::make('Stok & Teslimat')->schema([
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
                        ->columnSpanFull(),
                ])->columns(2),

                Tabs\Tab::make('SEO')->schema([
                    TextInput::make('meta_title')
                        ->label('Meta Başlık'),

                    Textarea::make('meta_description')
                        ->label('Meta Açıklama')
                        ->maxLength(160),
                ])->columns(2),

            ])->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('primaryImage')
                    ->label('Görsel')
                    ->width(50)
                    ->height(50)
                    ->defaultImageUrl(asset('images/placeholder.png')),

                TextColumn::make('name')
                    ->label('Ad')
                    ->formatStateUsing(fn ($record) => $record->getTranslation('name', 'tr'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('sku')
                    ->label('SKU')
                    ->searchable(),

                TextColumn::make('price')
                    ->label('Fiyat')
                    ->money('TRY')
                    ->sortable(),

                TextColumn::make('sale_price')
                    ->label('İndirimli')
                    ->money('TRY')
                    ->color('danger')
                    ->sortable(),

                BadgeColumn::make('stock_status')
                    ->label('Stok')
                    ->sortable()
                    ->colors([
                        'success' => 'in_stock',
                        'danger' => 'out_of_stock',
                    ])
                    ->formatStateUsing(fn ($state) => $state === 'in_stock' ? 'Stokta' : 'Tükendi'),

                BadgeColumn::make('status')
                    ->label('Durum')
                    ->sortable()
                    ->colors([
                        'success' => 'active',
                        'warning' => 'draft',
                        'gray' => 'inactive',
                    ])
                    ->formatStateUsing(fn ($state) => match ($state) {
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
                    ->url(fn () => static::getUrl('create')),
            ])
            ->actions([
                EditAction::make(),
                Action::make('duplicate')
                    ->label('Kopyala')
                    ->icon('heroicon-o-document-duplicate')
                    ->action(function (Product $record) {
                        $clone = $record->replicate();
                        $clone->slug = $record->slug . '-kopya-' . time();
                        $clone->status = 'draft';
                        $clone->save();

                        foreach ($record->categories as $category) {
                            $clone->categories()->attach($category);
                        }
                    }),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    BulkAction::make('activate')
                        ->label('Aktifleştir')
                        ->action(fn (Collection $records) => $records->each->update(['status' => 'active'])),

                    BulkAction::make('deactivate')
                        ->label('Pasifleştir')
                        ->action(fn (Collection $records) => $records->each->update(['status' => 'inactive'])),

                    BulkAction::make('set_featured')
                        ->label('Öne Çıkar')
                        ->action(fn (Collection $records) => $records->each->update(['is_featured' => true])),

                    BulkAction::make('unset_featured')
                        ->label('Öne Çıkarmayı Kaldır')
                        ->action(fn (Collection $records) => $records->each->update(['is_featured' => false])),

                    BulkAction::make('price_increase')
                        ->label('Fiyat Artır (%)')
                        ->form([
                            TextInput::make('percentage')
                                ->label('Yüzde')
                                ->numeric()
                                ->required(),
                        ])
                        ->action(function (Collection $records, array $data) {
                            $records->each(function ($record) use ($data) {
                                $record->update(['price' => $record->price * (1 + $data['percentage'] / 100)]);
                            });
                        }),

                    DeleteBulkAction::make()->label('Sil'),
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
}
