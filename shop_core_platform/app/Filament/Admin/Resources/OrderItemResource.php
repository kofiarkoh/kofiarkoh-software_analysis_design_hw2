<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\Base\BaseOrderItemResource;
use App\Filament\Admin\Resources\OrderItemResource\Pages;
use App\Filament\Admin\Resources\OrderItemResource\RelationManagers;
use App\Models\OrderItem;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OrderItemResource extends Resource
{
    protected static ?string $model = OrderItem::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return BaseOrderItemResource::form($form);
    }

    public static function table(Table $table): Table
    {
       return  BaseOrderItemResource::table($table)->filters([
           BaseOrderItemResource::getTransactionStatusFilter(),
       ]);
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
            'index' => Pages\ListOrderItems::route('/'),
            'view' => Pages\ViewOrderItem::route('/{record}'),
            'edit' => Pages\EditOrderItem::route('/{record}/edit'),
        ];
    }
}
