<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NotificationTemplateResource\Pages;
use App\Models\NotificationTemplate;
use App\Services\SmsService;
use App\Support\DynamicMailConfig;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Concerns\Translatable;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class NotificationTemplateResource extends Resource
{
    use Translatable;

    protected static ?string $model = NotificationTemplate::class;

    protected static ?string $navigationIcon = 'heroicon-o-bell-alert';

    protected static ?string $navigationGroup = 'İletişim';

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
                ->maxLength(100)
                ->regex('/^[a-z0-9_.-]+$/')
                ->helperText('Teknik anahtar: küçük harf, rakam, tire, nokta veya alt çizgi kullanın.')
                ->unique(NotificationTemplate::class, 'key', ignoreRecord: true)
                ->disabledOn('edit'),

            TextInput::make('name')
                ->label('Ad')
                ->maxLength(255)
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
                ->helperText('Değişkenler: {musteri_adi}, {siparis_no}, {toplam}')
                ->rows(4)
                ->required(fn (callable $get): bool => in_array($get('channel'), ['sms', 'both'], true))
                ->columnSpanFull(),

            TextInput::make('email_subject')
                ->label('E-posta Konusu')
                ->maxLength(255)
                ->required(fn (callable $get): bool => in_array($get('channel'), ['email', 'both'], true))
                ->columnSpanFull(),

            RichEditor::make('email_body')
                ->label('E-posta İçeriği')
                ->required(fn (callable $get): bool => in_array($get('channel'), ['email', 'both'], true))
                ->columnSpanFull(),

            TagsInput::make('variables')
                ->label('Kullanılan Değişkenler')
                ->helperText('Boş bırakılırsa metinlerdeki {degisken} alanları otomatik kaydedilir.')
                ->suggestions(array_keys(static::buildTestVariables()))
                ->columnSpanFull(),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('key')->label('Anahtar')->copyable(),
                TextColumn::make('name')
                    ->label('Ad')
                    ->formatStateUsing(fn ($record) => $record->getTranslation('name', app()->getLocale())),
                BadgeColumn::make('channel')
                    ->label('Kanal')
                    ->colors(['primary' => 'sms', 'info' => 'email', 'success' => 'both'])
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'sms' => 'SMS',
                        'email' => 'E-posta',
                        'both' => 'İkisi',
                        default => $state,
                    }),
                IconColumn::make('is_active')->label('Aktif')->boolean(),
            ])
            ->actions([
                EditAction::make(),
                Action::make('test_send')
                    ->label('Test Gönder')
                    ->icon('heroicon-o-paper-airplane')
                    ->form([
                        TextInput::make('email')
                            ->label('Test E-posta')
                            ->email()
                            ->default(auth()->user()?->email),
                        TextInput::make('phone')
                            ->label('Test Telefonu')
                            ->default(auth()->user()?->phone),
                        Select::make('locale')
                            ->label('Dil')
                            ->options([
                                'tr' => 'TR',
                                'en' => 'EN',
                                'ku' => 'KU',
                            ])
                            ->default('tr')
                            ->required(),
                    ])
                    ->action(function (NotificationTemplate $record, array $data): void {
                        $variables = static::buildTestVariables();
                        $locale = $data['locale'] ?? 'tr';
                        $emailSent = false;
                        $smsSent = false;

                        if (in_array($record->channel, ['email', 'both'], true) && filled($data['email'] ?? null)) {
                            DynamicMailConfig::apply();
                            $subject = $record->renderEmailSubject($variables, $locale, 'Test Bildirimi');
                            $body = $record->renderEmailBody($variables, $locale);

                            Mail::html($body, function ($message) use ($data, $subject): void {
                                $message->to($data['email'])->subject($subject);
                            });

                            $emailSent = true;
                        }

                        if (in_array($record->channel, ['sms', 'both'], true) && filled($data['phone'] ?? null)) {
                            $sms = app(SmsService::class);

                            if ($sms->canSend()) {
                                $smsSent = $sms->send(
                                    $data['phone'],
                                    $record->renderSms($variables, $locale)
                                );
                            }
                        }

                        if (! $emailSent && ! $smsSent) {
                            Notification::make()
                                ->danger()
                                ->title('Test bildirimi gönderilemedi')
                                ->body('Şablon kanalına uygun alıcı bilgisi girin ve ilgili servisi etkinleştirin.')
                                ->send();

                            return;
                        }

                        Notification::make()
                            ->success()
                            ->title('Test bildirimi gönderildi')
                            ->body(trim(collect([
                                $emailSent ? 'E-posta gönderildi.' : null,
                                $smsSent ? 'SMS gönderildi.' : null,
                            ])->filter()->implode(' ')))
                            ->send();
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

    public static function getTranslatableLocales(): array
    {
        return ['tr', 'en', 'ku'];
    }

    private static function buildTestVariables(): array
    {
        return [
            'musteri_adi' => 'Test Kullanıcı',
            'siparis_no' => 'RG-TEST-0001',
            'siparis_tarihi' => now()->format('d.m.Y H:i'),
            'siparis_tutari' => '1.250,00 TL',
            'odeme_yontemi' => 'Kredi Kartı',
            'durum' => 'Hazırlanıyor',
            'takip_linki' => url('/siparis-takip'),
            'alici_adi' => 'Test Alıcı',
            'toplam' => '1.250,00 TL',
            'son_tarih' => now()->addDay()->format('d.m.Y H:i'),
            'banka_adi' => 'Test Bankası',
            'iban' => 'TR000000000000000000000000',
            'hesap_sahibi' => 'Rose Garden',
            'aciklama' => 'RG-TEST-0001',
            'sepet_tutari' => '890,00 TL',
            'urun_sayisi' => '3',
            'sepet_linki' => url('/sepet'),
            'site_url' => config('app.url'),
            'olay_adi' => 'Dogum Gunu',
            'gun_kaldi' => '5',
            'tarih' => now()->addDays(5)->format('d.m.Y'),
        ];
    }

    public static function prepareDataForSave(array $data): array
    {
        $data['key'] = trim((string) ($data['key'] ?? ''));
        $data['name'] = static::normalizeLocalizedText($data['name'] ?? '');
        $data['sms_body'] = static::normalizeLocalizedText($data['sms_body'] ?? null);
        $data['email_subject'] = static::normalizeLocalizedText($data['email_subject'] ?? null);
        $data['email_body'] = static::normalizeLocalizedText($data['email_body'] ?? null);

        $usedVariables = static::extractVariablesFromPayload([
            $data['sms_body'],
            $data['email_subject'],
            $data['email_body'],
        ]);
        $declaredVariables = static::normalizeVariables($data['variables'] ?? []);
        $data['variables'] = $declaredVariables !== [] ? $declaredVariables : $usedVariables;

        static::validatePreparedData($data, $usedVariables);

        return $data;
    }

    private static function normalizeLocalizedText(mixed $value): mixed
    {
        if (is_array($value)) {
            return collect($value)
                ->mapWithKeys(fn (mixed $text, string $locale): array => [$locale => trim((string) $text)])
                ->all();
        }

        return is_null($value) ? null : trim((string) $value);
    }

    private static function normalizeVariables(mixed $variables): array
    {
        if (! is_array($variables)) {
            return [];
        }

        return collect($variables)
            ->map(fn (mixed $variable): string => trim((string) $variable))
            ->filter(fn (string $variable): bool => $variable !== '')
            ->unique()
            ->values()
            ->all();
    }

    private static function validatePreparedData(array $data, array $usedVariables): void
    {
        $errors = [];
        $channel = (string) ($data['channel'] ?? '');

        if (in_array($channel, ['sms', 'both'], true) && ! static::hasLocalizedContent($data['sms_body'] ?? null)) {
            $errors['data.sms_body'] = 'SMS kanalı için SMS metni zorunludur.';
        }

        if (in_array($channel, ['email', 'both'], true)) {
            if (! static::hasLocalizedContent($data['email_subject'] ?? null)) {
                $errors['data.email_subject'] = 'E-posta kanalı için konu zorunludur.';
            }

            if (! static::hasLocalizedContent($data['email_body'] ?? null)) {
                $errors['data.email_body'] = 'E-posta kanalı için gövde zorunludur.';
            }
        }

        $invalidDeclared = collect($data['variables'] ?? [])
            ->reject(fn (string $variable): bool => preg_match('/^[a-zA-Z0-9_]+$/', $variable) === 1)
            ->values()
            ->all();

        if ($invalidDeclared !== []) {
            $errors['data.variables'] = 'Değişken adları sadece harf, rakam ve alt çizgi içerebilir.';
        }

        $missingDeclared = array_values(array_diff($usedVariables, $data['variables'] ?? []));

        if ($missingDeclared !== []) {
            $errors['data.variables'] = 'Metinde kullanılan değişkenler listede yok: '.implode(', ', $missingDeclared);
        }

        if ($errors !== []) {
            throw ValidationException::withMessages($errors);
        }
    }

    private static function hasLocalizedContent(mixed $value): bool
    {
        if (is_array($value)) {
            return collect($value)->contains(fn (mixed $text): bool => trim(strip_tags((string) $text)) !== '');
        }

        return trim(strip_tags((string) $value)) !== '';
    }

    private static function extractVariablesFromPayload(array $payload): array
    {
        $variables = [];

        foreach ($payload as $value) {
            foreach (static::flattenText($value) as $text) {
                preg_match_all('/\{([a-zA-Z0-9_]+)\}/', $text, $matches);
                $variables = array_merge($variables, $matches[1] ?? []);
            }
        }

        return collect($variables)->unique()->values()->all();
    }

    private static function flattenText(mixed $value): array
    {
        if (is_array($value)) {
            return collect($value)
                ->flatMap(fn (mixed $item): array => static::flattenText($item))
                ->all();
        }

        return [(string) $value];
    }
}
