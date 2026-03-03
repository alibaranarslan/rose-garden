<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BlogPostResource\Pages;
use App\Models\BlogPost;
use App\Models\Product;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Concerns\Translatable;
use Filament\Resources\Resource;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class BlogPostResource extends Resource
{
    use Translatable;

    protected static ?string $model = BlogPost::class;
    protected static ?string $navigationIcon = 'heroicon-o-pencil-square';
    protected static ?string $navigationGroup = 'Blog';
    protected static ?string $navigationLabel = 'Yazılar';
    protected static ?string $modelLabel = 'Blog Yazısı';
    protected static ?string $pluralModelLabel = 'Blog Yazıları';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Tabs::make('Blog Formu')->tabs([
                Tabs\Tab::make('İçerik')->schema([
                    TextInput::make('title')
                        ->label('Başlık')
                        ->required()
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn (string $operation, $state, \Filament\Forms\Set $set) =>
                            $operation === 'create' ? $set('slug', Str::slug($state)) : null),

                    TextInput::make('slug')
                        ->label('Slug')
                        ->required()
                        ->unique(BlogPost::class, 'slug', ignoreRecord: true),

                    Textarea::make('excerpt')
                        ->label('Özet')
                        ->rows(3)
                        ->columnSpanFull(),

                    RichEditor::make('content')
                        ->label('İçerik')
                        ->required()
                        ->columnSpanFull(),

                    FileUpload::make('featured_image')
                        ->label('Öne Çıkan Görsel')
                        ->image()
                        ->directory('blog')
                        ->maxSize(5120)
                        ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/gif', 'application/pdf'])
                        ->getUploadedFileNameForStorageUsing(fn (\Livewire\Features\SupportFileUploads\TemporaryUploadedFile $file): string => \Illuminate\Support\Str::random(40) . '.' . $file->getClientOriginalExtension())
                        ->columnSpanFull(),

                    Select::make('blog_category_id')
                        ->label('Kategori')
                        ->relationship('category', 'name')
                        ->getOptionLabelFromRecordUsing(fn ($record) => $record->getTranslation('name', 'tr')),

                    Select::make('status')
                        ->label('Durum')
                        ->options([
                            'draft' => 'Taslak',
                            'published' => 'Yayında',
                            'archived' => 'Arşiv',
                        ])
                        ->default('draft')
                        ->required(),
                ])->columns(2),

                Tabs\Tab::make('İlişkili Ürünler')->schema([
                    Select::make('products')
                        ->label('Ürünler')
                        ->relationship('products', 'name')
                        ->multiple()
                        ->searchable()
                        ->preload()
                        ->getOptionLabelFromRecordUsing(fn ($record) => $record->getTranslation('name', 'tr'))
                        ->columnSpanFull(),
                ]),

                Tabs\Tab::make('SEO')->schema([
                    TextInput::make('meta_title')->label('Meta Başlık'),
                    Textarea::make('meta_description')->label('Meta Açıklama')->maxLength(160),
                ])->columns(2),

                Tabs\Tab::make('Yayın')->schema([
                    DateTimePicker::make('published_at')
                        ->label('Yayın Tarihi')
                        ->native(false),
                ]),
            ])->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('featured_image')->label('Görsel')->width(40)->height(40),
                TextColumn::make('title')
                    ->label('Başlık')
                    ->formatStateUsing(fn ($record) => $record->getTranslation('title', 'tr'))
                    ->searchable(),
                TextColumn::make('category.name')
                    ->label('Kategori')
                    ->formatStateUsing(fn ($record) => $record->category?->getTranslation('name', 'tr') ?? '-'),
                BadgeColumn::make('status')
                    ->label('Durum')
                    ->colors(['success' => 'published', 'warning' => 'draft', 'gray' => 'archived'])
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'published' => 'Yayında', 'draft' => 'Taslak', 'archived' => 'Arşiv', default => $state,
                    }),
                TextColumn::make('published_at')->label('Yayın')->dateTime('d.m.Y')->sortable(),
            ])
            ->actions([EditAction::make()])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBlogPosts::route('/'),
            'create' => Pages\CreateBlogPost::route('/create'),
            'edit' => Pages\EditBlogPost::route('/{record}/edit'),
        ];
    }

    public static function getTranslatableLocales(): array
    {
        return ['tr', 'en', 'ku'];
    }
}
