<?php

namespace App\Filament\Admin\Resources\ShopResource\RelationManagers;

use App\Filament\Admin\Resources\Base\BaseOrderItemResource;
use App\Filament\Admin\Resources\OrderItemResource;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class OrderItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'orderItems';


    protected static ?string $title = 'Orders';
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('product_id')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('product_id')
            ->columns(BaseOrderItemResource::table($table)->getColumns())
            ->filters([
                BaseOrderItemResource::getTransactionStatusFilter(),
            ])
            ->headerActions([
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->url( fn ($record) =>  OrderItemResource::getUrl('view', ['record' => $record->id])),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                ]),
            ]);
    }
}
