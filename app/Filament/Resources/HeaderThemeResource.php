<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HeaderThemeResource\Pages;
use App\Models\HeaderTheme;
use App\Models\SpecialOccasion;
use App\Support\AdminPrivileges;
use App\Support\HeaderThemeResolver;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Concerns\Translatable;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class HeaderThemeResource extends Resource
{
    use Translatable;

    protected static ?string $model = HeaderTheme::class;
    protected static ?string $navigationIcon = 'heroicon-o-swatch';
    protected static ?string $navigationGroup = 'Görünüm';
    protected static ?string $navigationLabel = 'Header Temaları';
    protected static ?string $modelLabel = 'Header teması';
    protected static ?string $pluralModelLabel = 'Header temaları';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Temel bilgiler')
                ->description('Tema takvim mantığını, görünürlüğünü ve çok dilli kampanya metinlerini buradan yönetin.')
                ->schema([
                    TextInput::make('name')
                        ->label('Tema adı')
                        ->required()
                        ->maxLength(120),
                    TextInput::make('slug')
                        ->label('Sistem anahtarı')
                        ->required()
                        ->maxLength(120)
                        ->unique(HeaderTheme::class, 'slug', ignoreRecord: true)
                        ->helperText('CSS sınıfı, özel gün eşleşmesi ve sistem fallback mantığı bu anahtara göre çalışır.'),
                    Select::make('mode')
                        ->label('Çalışma modu')
                        ->options(HeaderTheme::modeOptions())
                        ->required()
                        ->default(HeaderTheme::MODE_AUTOMATIC),
                    Toggle::make('is_enabled')
                        ->label('Tema kullanılabilir')
                        ->default(true),
                    TextInput::make('priority')
                        ->label('Öncelik')
                        ->numeric()
                        ->default(100)
                        ->helperText('Aynı gün birden fazla tema eşleşirse yüksek öncelik kazanır.'),
                    Textarea::make('banner_message')
                        ->label('Kısa duyuru metni')
                        ->rows(2)
                        ->maxLength(220)
                        ->helperText('Kampanya kartının küçük üst satırında görünür. EN ve KU boş kalırsa Türkçe içerik fallback olur.'),
                ])->columns(2),

            Section::make('Kampanya header içeriği')
                ->description('Özel gün header kartında görünen başlık, açıklama, CTA ve görsel akışı.')
                ->schema([
                    TextInput::make('headline')
                        ->label('Ana başlık')
                        ->maxLength(140)
                        ->helperText('Örn: Sevgililer Günü için butik çiçek ve çikolata seçkisi.'),
                    Textarea::make('subline')
                        ->label('Kısa açıklama')
                        ->rows(2)
                        ->maxLength(260),
                    TextInput::make('cta_label')
                        ->label('CTA metni')
                        ->maxLength(80)
                        ->helperText('Örn: Koleksiyonu keşfet.'),
                    Select::make('special_occasion_slug')
                        ->label('Bağlı özel gün sayfası')
                        ->options(fn () => SpecialOccasion::query()
                            ->active()
                            ->orderBy('date_month')
                            ->orderBy('date_day')
                            ->get()
                            ->mapWithKeys(fn (SpecialOccasion $occasion) => [
                                $occasion->slug => $occasion->getTranslation('name', app()->getLocale(), false)
                                    ?: $occasion->getTranslation('name', 'tr', false)
                                    ?: $occasion->slug,
                            ])
                            ->all())
                        ->searchable()
                        ->helperText('Boş kalırsa sistem tema anahtarını özel gün slug’ı olarak dener.'),
                    TextInput::make('cta_url')
                        ->label('Özel CTA URL')
                        ->maxLength(255)
                        ->helperText('Boş bırakılırsa bağlı özel gün sayfasına, kayıt yoksa özel günler listesine gider.'),
                    FileUpload::make('campaign_image')
                        ->label('Kampanya görseli')
                        ->disk('public')
                        ->directory('header-themes')
                        ->image()
                        ->maxSize(5120)
                        ->imageEditor()
                        ->getUploadedFileNameForStorageUsing(
                            fn (TemporaryUploadedFile $file): string => Str::random(40).'.'.($file->guessExtension() ?: $file->getClientOriginalExtension())
                        )
                        ->helperText('Opsiyonel. Girilmezse ilgili özel gün ürünlerinden otomatik kolaj üretilir.')
                        ->columnSpanFull(),
                ])->columns(2),

            Section::make('Takvim kuralı')
                ->schema([
                    Select::make('theme_type')
                        ->label('Tarih kuralı')
                        ->options(HeaderTheme::typeOptions())
                        ->required()
                        ->live(),
                    Select::make('month')
                        ->label('Ay')
                        ->options([
                            1 => 'Ocak', 2 => 'Şubat', 3 => 'Mart', 4 => 'Nisan', 5 => 'Mayıs', 6 => 'Haziran',
                            7 => 'Temmuz', 8 => 'Ağustos', 9 => 'Eylül', 10 => 'Ekim', 11 => 'Kasım', 12 => 'Aralık',
                        ])
                        ->visible(fn (Get $get) => in_array($get('theme_type'), [HeaderTheme::TYPE_FIXED, HeaderTheme::TYPE_NTH_WEEKDAY], true)),
                    TextInput::make('day')
                        ->label('Gün')
                        ->numeric()
                        ->minValue(1)
                        ->maxValue(31)
                        ->visible(fn (Get $get) => $get('theme_type') === HeaderTheme::TYPE_FIXED),
                    Select::make('weekday')
                        ->label('Haftanın günü')
                        ->options([
                            0 => 'Pazar',
                            1 => 'Pazartesi',
                            2 => 'Salı',
                            3 => 'Çarşamba',
                            4 => 'Perşembe',
                            5 => 'Cuma',
                            6 => 'Cumartesi',
                        ])
                        ->visible(fn (Get $get) => $get('theme_type') === HeaderTheme::TYPE_NTH_WEEKDAY),
                    Select::make('nth_week')
                        ->label('Hafta sırası')
                        ->options([
                            1 => 'İlk',
                            2 => 'İkinci',
                            3 => 'Üçüncü',
                            4 => 'Dördüncü',
                            -1 => 'Son',
                        ])
                        ->visible(fn (Get $get) => $get('theme_type') === HeaderTheme::TYPE_NTH_WEEKDAY),
                    DatePicker::make('starts_at')
                        ->label('Başlangıç tarihi')
                        ->visible(fn (Get $get) => $get('theme_type') === HeaderTheme::TYPE_RANGE),
                    DatePicker::make('ends_at')
                        ->label('Bitiş tarihi')
                        ->visible(fn (Get $get) => $get('theme_type') === HeaderTheme::TYPE_RANGE),
                ])->columns(3),

            Section::make('Görsel tavır')
                ->schema([
                    Select::make('style_variant')
                        ->label('Stil')
                        ->options(HeaderTheme::styleVariantOptions())
                        ->default('tribute'),
                    Select::make('illustration_mode')
                        ->label('Yedek motif tipi')
                        ->options(HeaderTheme::illustrationModeOptions())
                        ->default('inline_svg')
                        ->live(),
                    TextInput::make('illustration_asset')
                        ->label('Yedek görsel anahtarı / yolu')
                        ->helperText('Ürün kolajı üretilemezse kullanılabilecek motif veya dosya yolu.')
                        ->visible(fn (Get $get) => $get('illustration_mode') !== 'none'),
                    Select::make('decor_intensity')
                        ->label('Yoğunluk')
                        ->options(HeaderTheme::decorIntensityOptions())
                        ->default('medium'),
                    Toggle::make('show_flag')
                        ->label('Bayrak öğesi göster')
                        ->disabled()
                        ->dehydrated(false),
                    Toggle::make('show_ataturk')
                        ->label('Atatürk öğesi göster')
                        ->disabled()
                        ->dehydrated(false),
                    Textarea::make('notes')
                        ->label('Notlar')
                        ->rows(2)
                        ->columnSpanFull(),
                    Placeholder::make('preview_hint')
                        ->label('Önizleme')
                        ->content('Kayıt listesindeki “Önizle” aksiyonu locale ve tarih seçerek signed preview açar.')
                        ->columnSpanFull(),
                ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(HeaderTheme::query()->forSite()->orderByDesc('priority'))
            ->columns([
                TextColumn::make('name')
                    ->label('Tema')
                    ->formatStateUsing(fn (HeaderTheme $record) => $record->translatedName('tr'))
                    ->searchable(),
                TextColumn::make('slug')
                    ->label('Anahtar')
                    ->badge(),
                TextColumn::make('schedule')
                    ->label('Takvim')
                    ->state(fn (HeaderTheme $record) => $record->scheduleLabel())
                    ->wrap(),
                TextColumn::make('mode')
                    ->label('Mod')
                    ->formatStateUsing(fn (string $state) => HeaderTheme::modeOptions()[$state] ?? $state),
                TextColumn::make('special_occasion_slug')
                    ->label('Özel gün')
                    ->placeholder('Otomatik'),
                IconColumn::make('is_enabled')
                    ->label('Aktif')
                    ->boolean(),
                TextColumn::make('priority')
                    ->label('Öncelik')
                    ->sortable(),
            ])
            ->actions([
                Action::make('preview')
                    ->label('Önizle')
                    ->icon('heroicon-o-eye')
                    ->form([
                        Select::make('locale')
                            ->label('Dil')
                            ->options(['tr' => 'Türkçe', 'en' => 'English', 'ku' => 'Kurdî'])
                            ->default('tr')
                            ->required(),
                        DatePicker::make('preview_date')
                            ->label('Simüle edilecek tarih')
                            ->default(fn (HeaderTheme $record) => $record->previewDate()->toDateString())
                            ->required(),
                    ])
                    ->action(function (HeaderTheme $record, array $data) {
                        return redirect(app(HeaderThemeResolver::class)->getPreviewUrl(
                            $record,
                            $data['locale'] ?? 'tr',
                            $data['preview_date'] ?? null,
                        ));
                    }),
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListHeaderThemes::route('/'),
            'create' => Pages\CreateHeaderTheme::route('/create'),
            'edit' => Pages\EditHeaderTheme::route('/{record}/edit'),
        ];
    }

    public static function getTranslatableLocales(): array
    {
        return ['tr', 'en', 'ku'];
    }

    public static function canViewAny(): bool
    {
        return AdminPrivileges::canPublishConfiguration(auth()->user());
    }

    public static function canCreate(): bool
    {
        return AdminPrivileges::canPublishConfiguration(auth()->user());
    }

    public static function canEdit($record): bool
    {
        return AdminPrivileges::canPublishConfiguration(auth()->user());
    }

    public static function canDelete($record): bool
    {
        return AdminPrivileges::canPublishConfiguration(auth()->user());
    }
}
