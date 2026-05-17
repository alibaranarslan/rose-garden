<?php

namespace App\Filament\Resources;

use Closure;
use App\Filament\Resources\CustomerEventResource\Pages;
use App\Models\CustomerEvent;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CustomerEventResource extends Resource
{
    protected static ?string $model = CustomerEvent::class;
    protected static ?string $navigationIcon = 'heroicon-o-bell';
    protected static ?string $navigationGroup = 'Pazarlama';
    protected static ?string $navigationLabel = 'Müşteri Olayları';
    protected static ?string $modelLabel = 'Müşteri Olayı';
    protected static ?string $pluralModelLabel = 'Müşteri Olayları';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Select::make('user_id')
                ->label('Müşteri')
                ->relationship(
                    name: 'user',
                    titleAttribute: 'name',
                    modifyQueryUsing: fn (Builder $query): Builder => $query->where('is_admin', false)->where('is_active', true),
                )
                ->searchable()
                ->required(),

            Select::make('event_type')
                ->label('Olay Türü')
                ->options([
                    'birthday' => 'Doğum Günü',
                    'anniversary' => 'Yıldönümü',
                    'valentines' => 'Sevgililer Günü',
                    'mothers_day' => 'Anneler Günü',
                    'custom' => 'Özel',
                ])
                ->required(),

            TextInput::make('event_label')
                ->label('Etiket')
                ->placeholder('Ayşe\'nin doğum günü')
                ->maxLength(255)
                ->dehydrateStateUsing(fn ($state): string => trim((string) $state)),

            TextInput::make('recipient_name')
                ->label('Alıcı Adı')
                ->maxLength(255)
                ->dehydrateStateUsing(fn ($state): string => trim((string) $state)),

            Textarea::make('recipient_address')
                ->label('Teslimat Adresi')
                ->rows(2)
                ->maxLength(1000)
                ->dehydrateStateUsing(fn ($state): string => trim((string) $state))
                ->columnSpanFull(),

            Select::make('event_month')
                ->label('Ay')
                ->options(array_combine(range(1, 12), ['Ocak', 'Şubat', 'Mart', 'Nisan', 'Mayıs', 'Haziran', 'Temmuz', 'Ağustos', 'Eylül', 'Ekim', 'Kasım', 'Aralık']))
                ->required(),

            Select::make('event_day')
                ->label('Gün')
                ->options(array_combine(range(1, 31), range(1, 31)))
                ->rule(fn (callable $get): Closure => function (string $attribute, mixed $value, Closure $fail) use ($get): void {
                    if (blank($value) || blank($get('event_month'))) {
                        return;
                    }

                    if (! checkdate((int) $get('event_month'), (int) $value, 2024)) {
                        $fail('Seçilen ay ve gün birlikte geçerli olmalıdır.');
                    }
                })
                ->required(),

            Select::make('detected_from')
                ->label('Tespit Kaynağı')
                ->options([
                    'card_message' => 'Kart Mesajı',
                    'order_date' => 'Sipariş Tarihi',
                    'manual' => 'Manuel',
                ])
                ->required(),

            TextInput::make('reminder_days_before')
                ->label('Kaç Gün Önce Hatırlat')
                ->numeric()
                ->minValue(0)
                ->maxValue(60)
                ->default(5),

            Toggle::make('is_active')
                ->label('Aktif')
                ->default(true),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Müşteri')
                    ->searchable(),

                BadgeColumn::make('event_type')
                    ->label('Olay')
                    ->colors(['primary'])
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'birthday' => 'Doğum Günü',
                        'anniversary' => 'Yıldönümü',
                        'valentines' => 'Sevgililer Günü',
                        'mothers_day' => 'Anneler Günü',
                        'custom' => 'Özel',
                        default => $state,
                    }),

                TextColumn::make('recipient_name')
                    ->label('Alıcı'),

                TextColumn::make('event_date')
                    ->label('Tarih')
                    ->getStateUsing(fn ($record) => "{$record->event_day}/{$record->event_month}"),

                BadgeColumn::make('detected_from')
                    ->label('Kaynak')
                    ->colors(['info'])
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'card_message' => 'Kart Mesajı',
                        'order_date' => 'Sipariş',
                        'manual' => 'Manuel',
                        default => $state,
                    }),

                TextColumn::make('reminder_days_before')
                    ->label('Hatırlatma'),

                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCustomerEvents::route('/'),
            'create' => Pages\CreateCustomerEvent::route('/create'),
            'edit' => Pages\EditCustomerEvent::route('/{record}/edit'),
        ];
    }
}
