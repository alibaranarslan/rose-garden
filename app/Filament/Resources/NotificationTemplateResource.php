<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NotificationTemplateResource\Pages;
use App\Models\NotificationTemplate;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class NotificationTemplateResource extends Resource
{
    protected static ?string $model = NotificationTemplate::class;
    protected static ?string $navigationIcon = 'heroicon-o-bell-alert';
    protected static ?string $navigationGroup = 'Pazarlama';
    protected static ?string $navigationLabel = 'Bildirim Şablonları';
    protected static ?string $modelLabel = 'Bildirim Şablonu';
    protected static ?string $pluralModelLabel = 'Bildirim Şablonları';
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('key')
                ->label('Anahtar')
                ->required()
                ->unique(NotificationTemplate::class, 'key', ignoreRecord: true)
                ->disabledOn('edit'),

            TextInput::make('name')
                ->label('Ad')
                ->required(),

            Select::make('channel')
                ->label('Kanal')
                ->options([
                    'sms' => 'SMS',
                    'email' => 'E-posta',
                    'both' => 'İkisi de',
                ])
                ->required(),

            Toggle::make('is_active')
                ->label('Aktif')
                ->default(true),

            Textarea::make('sms_body')
                ->label('SMS İçeriği')
                ->helperText('Değişkenler: {müşteri_adı}, {sipariş_no}, {tutar}')
                ->rows(4)
                ->columnSpanFull(),

            TextInput::make('email_subject')
                ->label('E-posta Konusu')
                ->columnSpanFull(),

            RichEditor::make('email_body')
                ->label('E-posta İçeriği')
                ->columnSpanFull(),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('key')->label('Anahtar')->copyable(),
                TextColumn::make('name')->label('Ad'),
                BadgeColumn::make('channel')
                    ->label('Kanal')
                    ->colors(['primary' => 'sms', 'info' => 'email', 'success' => 'both'])
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'sms' => 'SMS', 'email' => 'E-posta', 'both' => 'İkisi', default => $state,
                    }),
                IconColumn::make('is_active')->label('Aktif')->boolean(),
            ])
            ->actions([
                EditAction::make(),
                Action::make('test_send')
                    ->label('Test Gönder')
                    ->icon('heroicon-o-paper-airplane')
                    ->form([
                        TextInput::make('recipient')
                            ->label('Alıcı (Telefon / E-posta)')
                            ->required(),
                    ])
                    ->action(function (NotificationTemplate $record, array $data) {
                        // Test send logic here
                        Notification::make()->success()->title('Test bildirimi gönderildi: ' . $data['recipient'])->send();
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListNotificationTemplates::route('/'),
            'create' => Pages\CreateNotificationTemplate::route('/create'),
            'edit' => Pages\EditNotificationTemplate::route('/{record}/edit'),
        ];
    }
}
