<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers\ItemsRelationManager;
use App\Filament\Resources\OrderResource\RelationManagers\StatusHistoryRelationManager;
use App\Models\Order;
use App\Support\AdminActionLogger;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;
    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    protected static ?string $navigationGroup = 'Siparişler';
    protected static ?string $navigationLabel = 'Tüm Siparişler';
    protected static ?string $modelLabel = 'Sipariş';
    protected static ?string $pluralModelLabel = 'Siparişler';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Sipariş Bilgileri')->schema([
                \Filament\Forms\Components\TextInput::make('order_number')
                    ->label('Sipariş No')
                    ->disabled(),

                \Filament\Forms\Components\TextInput::make('created_at')
                    ->label('Tarih')
                    ->disabled(),

                Select::make('status')
                    ->label('Durum')
                    ->options([
                        'pending' => 'Bekliyor',
                        'awaiting_payment' => 'Ödeme Bekleniyor',
                        'paid' => 'Ödendi',
                        'preparing' => 'Hazırlanıyor',
                        'on_the_way' => 'Yolda',
                        'delivered' => 'Teslim Edildi',
                        'cancelled' => 'İptal',
                        'refunded' => 'İade',
                    ])
                    ->required(),

                Textarea::make('admin_note')
                    ->label('Admin Notu')
                    ->rows(3),
            ])->columns(2),

            Section::make('Gönderici Bilgileri')->schema([
                \Filament\Forms\Components\TextInput::make('sender_name')
                    ->label('Ad Soyad')
                    ->disabled(),

                \Filament\Forms\Components\TextInput::make('sender_phone')
                    ->label('Telefon')
                    ->disabled(),

                \Filament\Forms\Components\TextInput::make('sender_email')
                    ->label('E-posta')
                    ->disabled(),
            ])->columns(3),

            Section::make('Alıcı ve Teslimat')->schema([
                \Filament\Forms\Components\TextInput::make('recipient_name')
                    ->label('Alıcı Adı')
                    ->disabled(),

                \Filament\Forms\Components\TextInput::make('recipient_phone')
                    ->label('Alıcı Telefonu')
                    ->disabled(),

                Textarea::make('recipient_address')
                    ->label('Adres')
                    ->disabled()
                    ->columnSpanFull(),

                \Filament\Forms\Components\TextInput::make('delivery_date')
                    ->label('Teslimat Tarihi')
                    ->disabled(),

                Textarea::make('delivery_note')
                    ->label('Teslimat Notu')
                    ->disabled(),
            ])->columns(2),

            Section::make('Sipariş Özeti')->schema([
                \Filament\Forms\Components\TextInput::make('subtotal')
                    ->label('Ara Toplam')
                    ->prefix('₺')
                    ->disabled(),

                \Filament\Forms\Components\TextInput::make('delivery_fee')
                    ->label('Teslimat Ücreti')
                    ->prefix('₺')
                    ->disabled(),

                \Filament\Forms\Components\TextInput::make('discount_amount')
                    ->label('İndirim')
                    ->prefix('₺')
                    ->disabled(),

                \Filament\Forms\Components\TextInput::make('loyalty_points_used')
                    ->label('Kullanılan Puan')
                    ->prefix('₺')
                    ->disabled(),

                \Filament\Forms\Components\TextInput::make('total')
                    ->label('Genel Toplam')
                    ->prefix('₺')
                    ->disabled(),
            ])->columns(5),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('order_number')
                    ->label('Sipariş No')
                    ->searchable()
                    ->copyable(),

                TextColumn::make('sender_name')
                    ->label('Müşteri')
                    ->searchable(),

                TextColumn::make('recipient_name')
                    ->label('Alıcı')
                    ->searchable(),

                TextColumn::make('total')
                    ->label('Toplam')
                    ->money('TRY')
                    ->sortable(),

                BadgeColumn::make('payment_method')
                    ->label('Ödeme')
                    ->sortable()
                    ->colors([
                        'info' => 'credit_card',
                        'warning' => 'bank_transfer',
                    ])
                    ->formatStateUsing(fn ($state) => $state === 'credit_card' ? 'Kredi Kartı' : 'Havale'),

                BadgeColumn::make('status')
                    ->label('Durum')
                    ->sortable()
                    ->colors([
                        'gray' => 'pending',
                        'warning' => 'awaiting_payment',
                        'info' => 'paid',
                        'primary' => 'preparing',
                        'success' => fn ($state) => in_array($state, ['on_the_way', 'delivered']),
                        'danger' => fn ($state) => in_array($state, ['cancelled', 'refunded']),
                    ])
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'pending' => 'Bekliyor',
                        'awaiting_payment' => 'Ödeme Bekleniyor',
                        'paid' => 'Ödendi',
                        'preparing' => 'Hazırlanıyor',
                        'on_the_way' => 'Yolda',
                        'delivered' => 'Teslim Edildi',
                        'cancelled' => 'İptal',
                        'refunded' => 'İade',
                        default => $state,
                    }),

                TextColumn::make('delivery_date')
                    ->label('Teslimat')
                    ->date('d.m.Y')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Tarih')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Durum')
                    ->options([
                        'pending' => 'Bekliyor',
                        'awaiting_payment' => 'Ödeme Bekleniyor',
                        'paid' => 'Ödendi',
                        'preparing' => 'Hazırlanıyor',
                        'on_the_way' => 'Yolda',
                        'delivered' => 'Teslim Edildi',
                        'cancelled' => 'İptal',
                        'refunded' => 'İade',
                    ]),

                SelectFilter::make('payment_method')
                    ->label('Ödeme Yöntemi')
                    ->options([
                        'credit_card' => 'Kredi Kartı',
                        'bank_transfer' => 'Havale',
                    ]),

                Filter::make('awaiting_bank_transfer')
                    ->label('Havale Onayı Bekleyen')
                    ->query(fn (Builder $q) => $q->awaitingBankTransfer()),

                Filter::make('delivery_date')
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('date')->label('Teslimat Tarihi'),
                    ])
                    ->query(fn (Builder $q, array $data) =>
                        $q->when($data['date'], fn ($query) => $query->whereDate('delivery_date', $data['date']))),
            ])
            ->actions([
                ViewAction::make()->label('Görüntüle'),
                EditAction::make()->label('Düzenle'),

                Action::make('approve_payment')
                    ->label('Havaleyi Onayla')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (Order $record) => $record->status === 'awaiting_payment' && $record->payment_method === 'bank_transfer')
                    ->requiresConfirmation()
                    ->modalHeading('Havale ödemesini onayla')
                    ->modalDescription('Bu işlem siparişi ödendi durumuna alır ve ödeme kaydını tamamlandı olarak işaretler. Banka hareketini kontrol etmeden onaylamayın.')
                    ->modalSubmitActionLabel('Havaleyi onayla')
                    ->action(function (Order $record) {
                        $payment = $record->payment;

                        if (! $payment || $payment->status !== 'pending') {
                            Notification::make()
                                ->danger()
                                ->title('Havale onaylanamadı')
                                ->body('Bu sipariş için bekleyen ödeme kaydı bulunamadı.')
                                ->send();

                            return;
                        }

                        DB::transaction(function () use ($record) {
                            $record->payment->update(['status' => 'completed', 'confirmed_by' => auth()->id(), 'confirmed_at' => now()]);
                            $record->update(['status' => 'paid']);
                        });

                        AdminActionLogger::record('order.approve_bank_transfer', $record, [
                            'payment_id' => $record->payment?->getKey(),
                            'order_number' => $record->order_number,
                        ]);

                        Notification::make()->success()->title('Havale onaylandı')->send();
                    }),

                Action::make('print')
                    ->label('Yazdır')
                    ->icon('heroicon-o-printer')
                    ->url(fn (Order $record) => route('orders.print', $record))
                    ->openUrlInNewTab(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            ItemsRelationManager::class,
            StatusHistoryRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'view' => Pages\ViewOrder::route('/{record}'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
