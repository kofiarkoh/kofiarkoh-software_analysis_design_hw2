<?php

namespace App\Filament\Vendor\Resources;

use App\Filament\Vendor\Resources\VendorAccountPaymentResource\Pages;
use App\Filament\Vendor\Resources\VendorAccountPaymentResource\RelationManagers;
use App\Models\Vendor\ShopPayment;
use App\Utils\BaseResourcePolicy;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ShopPaymentResource extends Resource
{


    protected static ?string $model = ShopPayment::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function canAccess(): bool
    {
        return BaseResourcePolicy::canAccessResource();
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id'),
                Tables\Columns\TextColumn::make('amount'),
                Tables\Columns\TextColumn::make('transaction_status')->badge(),
                Tables\Columns\TextColumn::make('balance'),
                Tables\Columns\TextColumn::make('payment_type')->badge()
                    ->color(fn (string $state): string => match ($state) {
                        ShopPayment::CREDIT_PAYMENT => 'success',
                        ShopPayment::DEBIT_PAYMENT => 'danger',
                    }),
                Tables\Columns\TextColumn::make('orderItem.id'),
                Tables\Columns\TextColumn::make('created_at')->label('Date'),
                Tables\Columns\TextColumn::make('reference')->toggleable(true, true ),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                ]),
            ])
            ->defaultSort('id', 'desc')
            ;
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
            'index' => Pages\ListShopPayments::route('/'),
            'view' => Pages\ViewShopPayment::route('/{record}'),
        ];
    }
}
