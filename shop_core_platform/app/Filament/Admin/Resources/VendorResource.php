<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\VendorResource\Pages;
use App\Filament\Admin\Resources\VendorResource\RelationManagers;
use App\Models\Vendor\Vendor;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\DateTimePicker;

class VendorResource extends Resource
{
    protected static ?string $model = Vendor::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('first_name')
                    ->required()
                    ->disabled()
                    ->maxLength(255),

                TextInput::make('last_name')
                    ->required()
                    ->disabled()
                    ->maxLength(255),

                TextInput::make('phone_number')
                    ->required()
                    ->disabled()
                    ->maxLength(255),

                TextInput::make('email')
                    ->email()
                    ->unique(ignoreRecord: true)
                    ->required()
                    ->disabled()
                    ->maxLength(255),


                DateTimePicker::make('email_verified_at')
                    ->label('Email Verified At')
                    ->nullable()->disabled(),

                Forms\Components\Select::make('status')->options(Vendor::VENDOR_STATUSES)->required(),


            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('first_name')->label('First Name')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('last_name')->label('Last Name')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('status') ->badge()
                    ->colors([
                        'primary' => Vendor::STATUS_UNDER_REVIEW,
                        'success' => Vendor::STATUS_APPROVED,
                        'danger'  =>  Vendor::STATUS_SUSPENDED,
                    ]),
                Tables\Columns\TextColumn::make('shops_count')->counts('shops')->label('Shops'),
            ])
            ->filters([
                //
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

    public static function getRelations(): array
    {
        return [
            RelationManagers\ShopsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVendors::route('/'),
            'view' => Pages\ViewVendor::route('/{record}'),
        ];
    }
}
