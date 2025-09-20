<?php

namespace App\Filament\Vendor\Resources;

use App\Filament\Admin\Resources\ShopResource\RelationManagers\OrderItemsRelationManager;
use App\Filament\Vendor\Resources\ProductResource\Pages;
use App\Filament\Vendor\Resources\ProductResource\RelationManagers;
use App\Jobs\RecomputeSimilarProducts;
use App\Models\Vendor\AttributeValue;
use App\Models\Vendor\Product;
use App\Models\Vendor\Vendor;
use App\Utils\BaseResourcePolicy;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function canAccess(): bool
    {
       return BaseResourcePolicy::canAccessResource();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Forms\Components\Grid::make(2)
                ->schema([
                    Forms\Components\Placeholder::make('shop_name')
                        ->label('Shop')
                        ->content(fn (?Model $record) => $record?->shop?->name ?? '—')
                        ->hiddenOn(['create', 'edit']),
                    TextInput::make('name')
                        ->required()
                        ->maxLength(255)
                        ->disabled(self::isFormFieldDisabled()),
                    TextInput::make('price')
                        ->label('Price')
                        ->numeric()
                        ->step(0.01)
                        ->required()
                        ->disabled(self::isFormFieldDisabled())
                        ->rules(['numeric', 'min:0']),

                ]),

                Forms\Components\Grid::make(1)
                ->schema([
                    Select::make('categories')
                        ->label('Categories')
                        ->multiple()
                        ->relationship('categories', 'name')
                        ->preload()
                        ->searchable()
                        ->disabled(self::isFormFieldDisabled())
                        ->required()

                ]),


                Forms\Components\Grid::make(1)
                    ->schema([
                        Forms\Components\RichEditor::make('description')
                            ->label('Description')
                            ->required()
                            ->disabled(self::isFormFieldDisabled())
                            ->disableToolbarButtons([
                                'attachFiles',
                                'h2',
                                'h3',
                                'link',
                                'blockquote',
                                'codeBlock',
                                'strike',
                                'underline',
                            ]),

                    ]),

                Forms\Components\Grid::make(2)
                    ->schema([
                        TextInput::make('quantity')
                            ->required()
                            ->numeric()
                            ->disabled(self::isFormFieldDisabled())
                            ->rules(['numeric', 'min:0']),

                        TextInput::make('reserved_stock')
                            ->disabled(),

                    ]),



                Forms\Components\Select::make('status')
                    ->label('Product Status')
                    ->options(self::getStatuses())
                    ->required()
                    ->default(Product::STATUS_DRAFT),


                Forms\Components\Grid::make(1)
                    ->schema([
                        Forms\Components\FileUpload::make('photos')
                            ->multiple()
                            ->minFiles(1)
                            ->maxFiles(5)
                            ->maxSize(2024)

                            ->image()
                            ->disabled(self::isFormFieldDisabled())
                            ->
                            saveUploadedFileUsing(function ($file) {
                                $filename = Str::uuid(). '.' . $file->getClientOriginalExtension();
                                $path = 'products_images/' . $filename;

                                // Setup the Intervention Image Manager with GD or Imagick
                                $manager = new ImageManager(new Driver()); // or new \Intervention\Image\Drivers\Imagick\Driver()

                                // Read the uploaded image
                                $image = $manager->read($file);


                                // Save as compressed JPEG/PNG/WebP (adjust as needed)
                                $encoded = $image->toJpeg(quality: 40); // or toPng(), toWebp()

                                // Store to disk
                                Storage::disk('public')->put($path, (string) $encoded);

                                return $path;
                            })
                            ->panelLayout('grid')
                            ->directory('products_images')
                    ]),

                Forms\Components\Grid::make(1)
                    ->schema([
                        Repeater::make('variants')
                            ->relationship()
                            ->defaultItems(0)
                            ->disabled(self::isFormFieldDisabled())
                            //->dehydrated(true)
                            ->schema([
                                TextInput::make('sku')->required(),
                                TextInput::make('price')->numeric()->required(),
                                TextInput::make('stock')->numeric()->required(),
                                TextInput::make('reserved_stock')->numeric()->disabled(),

                                Forms\Components\Select::make('attributeValues')
                                    ->multiple()
                                    ->label('Attributes')
                                    ->relationship('attributeValues', 'value') // this is optional when using options()
                                    ->options(function ($record) {

                                        if (Filament::getCurrentPanel()->getId() == 'vendor'){
                                            $shop = Filament::getTenant();
                                        }

                                        else{
                                            $shop = $record->product->shop;

                                        }

                                        return AttributeValue::with('attribute')
                                            ->whereHas('attribute', fn ($query) => $query->where('shop_id', $shop->id))
                                            ->get()
                                            ->mapWithKeys(fn ($record) => [
                                                $record->id => "{$record->attribute->name} - {$record->value}"
                                            ])
                                            ->toArray();
                                    })
                                    ->columnSpanFull()
                                    ->searchable()
                            ])->columns(2)->grid()
                    ]),


            ]);
    }

    public static function getStatuses(): array
    {
        if (Filament::getCurrentPanel()->getId() == 'vendor'){
            return Product::VENDOR_STATUSES;
        }

        return Product::ADMIN_STATUSES;
    }
    public static function isFormFieldDisabled() : bool
    {
        if (Filament::getCurrentPanel()->getId() == 'vendor'){
            return false;
        }
        return true;
    }
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('stock')->label('Available Stock'),
                TextColumn::make('reserved_stock'),
                TextColumn::make('status'),

                TextColumn::make('price')
                    ->label('Price')
                    ->money('GHC')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
                Tables\Filters\SelectFilter::make('category')
                    ->label('Category')
                    ->relationship('categories', 'name')
                    ->multiple()
                    ->preload()
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            OrderItemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'view' => Pages\ViewProduct::route('/{record}'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
