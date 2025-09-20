<?php

namespace App\Filament\Admin\Resources\Base;

use App\Filament\Vendor\Resources\OrderItemResource\Pages\EditOrderItem;
use App\Filament\Vendor\Resources\OrderItemResource\Pages\ListOrderItems;
use App\Filament\Vendor\Resources\OrderItemResource\Pages\ViewOrderItem;
use App\Models\OrderItem;
use App\States\Transaction\FailedTransaction;
use App\States\Transaction\PendingTransaction;
use App\States\Transaction\SuccessTransaction;
use App\Utils\OrderItemStatus;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class BaseOrderItemResource extends Resource
{

    protected static ?string $model = OrderItem::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([


                Section::make('Product Details')
                    ->schema([
                        Grid::make(2)->schema([
                            Placeholder::make('order_id')
                                ->label('Order #')
                                ->content(fn($record) => $record->order?->id)
                                ->visibleOn('view'),

                            Placeholder::make('order_date')
                                ->label('Order Date')
                                ->content(fn($record) => $record->order?->created_at?->format('F j, Y'))
                                ->visibleOn('view'),

                            Placeholder::make('product_name')
                                ->label('Product Name')
                                ->content(fn($record) => $record->product?->name)
                                ->visibleOn('view'),

                            Placeholder::make('variant_sku')
                                ->label('Variant SKU')
                                ->content(fn($record) => $record->variant?->sku)
                                ->visibleOn('view'),

                            Placeholder::make('quantity')
                                ->label('Quantity')
                                ->content(fn($record) => $record->quantity)
                                ->visibleOn('view'),
                        ]),
                    ])
                    ->visibleOn('view'),


                Section::make('Customer & Delivery Info')
                    ->schema([
                        Grid::make(2)->schema([
                            Placeholder::make('customer_name')
                                ->label('Customer Name')
                                ->content(fn($record) => $record->order?->user?->first_name . ' ' . $record->order?->user?->last_name)
                                ->visibleOn('view'),

                            Placeholder::make('customer_phone')
                                ->label('Phone Number')
                                ->content(fn($record) => $record->order?->user?->phone)
                                ->visibleOn('view'),

                            Placeholder::make('delivery_instructions')
                                ->label('Delivery Instructions')
                                ->content(fn($record) => $record->order?->delivery_instructions ?? "-- N/A -- ")
                                ->visibleOn('view'),

                            Placeholder::make('nearby_city')
                                ->label('Nearby City')
                                ->content(fn($record) => $record->order?->nearby_city ?? "-- N/A -- ")
                                ->visibleOn('view'),
                        ]),
                    ])
                    ->visibleOn('view'),

                Section::make('Vendor Info')
                    ->schema([
                        Grid::make(2)->schema([
                            Placeholder::make('shop_name')
                                ->label('Shop Name')
                                ->content(fn($record) => $record->product?->shop?->name)
                                ->visibleOn('view'),
                            Placeholder::make('vendor_name')
                                ->label('Vendor Name')
                                ->content(fn($record) => $record->product?->shop?->owner?->getFilamentName())
                                ->visibleOn('view'),
                            Placeholder::make('vendor_email')
                                ->label('Email')
                                ->content(fn($record) => $record->product?->shop?->owner?->email)
                                ->visibleOn('view'),
                            Placeholder::make('vendor_name')
                                ->label('Phone Number')
                                ->content(fn($record) => $record->product?->shop?->owner?->phone)
                                ->visibleOn('view'),

                        ]),
                    ])
                    ->visibleOn('view'),

                Select::make('status')
                    ->options(collect(OrderItemStatus::cases())->mapWithKeys(fn($case) => [
                        $case->value => ucfirst($case->name),
                    ]))
                    ->label('Status')
                    ->required(),


            ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->groups([
                Tables\Grouping\Group::make('order_id')
                    ->label('Order')
                    ->getTitleFromRecordUsing(function ($record) {
                        return '# ' . $record->order->order_number;
                    }),
                'status'
            ])
            ->columns([
                TextColumn::make('order.order_number')->label('Order #')->searchable(),
                TextColumn::make('product.name'),

                TextColumn::make('transaction_status')
                    ->label('Payment Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        PendingTransaction::$name => 'gray',
                        SuccessTransaction::$name => 'success',
                        FailedTransaction::$name => 'danger',
                    }),

                TextColumn::make('status')->badge(),

                TextColumn::make('variant.sku'),
                TextColumn::make('order.created_at')->label('Order Date'),
                TextColumn::make('quantity')->label('Quantity'),

            ])
            ->filters([

            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                ]),
            ]);
    }

    public static function getTransactionStatusFilter(): Tables\Filters\SelectFilter
    {
       return Tables\Filters\SelectFilter::make('transaction_status')
            ->label('Transaction Status')
            ->options([
                'pending' => 'Pending',
                'success' => 'Success',
                'failed' => 'Failed',
            ])
            ->default('success')
            ->query(function ($query, $data) {
                $query->whereHas('payment.transaction', function ($q) use ($data) {
                    $q->where('status', $data['value']);
                });
            });
    }
    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListOrderItems::route('/'),
            'view' => ViewOrderItem::route('/{record}'),
            'edit' => EditOrderItem::route('/{record}/edit'),
        ];
    }
}
