<?php

namespace App\Filament\Vendor\Resources;

use App\Filament\Admin\Resources\Base\BaseOrderItemResource;
use App\Filament\Vendor\Resources\OrderItemResource\RelationManagers;
use App\Models\OrderItem;
use App\Utils\BaseResourcePolicy;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class OrderItemResource extends Resource
{

    protected static ?string $model = OrderItem::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function canAccess(): bool
    {
        return BaseResourcePolicy::canAccessResource();
    }
    public static function form(Form $form): \Filament\Forms\Form
    {
        return BaseOrderItemResource::form($form);
    }


    public static function table(Table $table): \Filament\Tables\Table
    {
        return BaseOrderItemResource::table($table)->filters([

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
        return BaseOrderItemResource::getPages();
    }
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->whereHas('payment.transaction', function ($query) {
            $query->where('status', 'success');
        });
    }
}
