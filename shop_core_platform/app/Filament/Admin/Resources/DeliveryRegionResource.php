<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\DeliveryRegionResource\Pages;
use App\Filament\Admin\Resources\DeliveryRegionResource\RelationManagers;
use App\Models\Admin\DeliveryRegion;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DeliveryRegionResource extends Resource
{
    protected static ?string $model = DeliveryRegion::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name'),

        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\CitiesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDeliveryRegions::route('/'),
            'create' => Pages\CreateDeliveryRegion::route('/create'),
            'view' => Pages\ViewDeliveryRegion::route('/{record}'),
            'edit' => Pages\EditDeliveryRegion::route('/{record}/edit'),
        ];
    }
}
