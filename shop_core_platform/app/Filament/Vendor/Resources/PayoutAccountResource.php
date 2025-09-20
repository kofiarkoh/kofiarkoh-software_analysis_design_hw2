<?php

namespace App\Filament\Vendor\Resources;

use App\Filament\Vendor\Resources\PayoutAccountResource\Pages;
use App\Filament\Vendor\Resources\PayoutAccountResource\RelationManagers;
use App\Models\Vendor\PayoutAccount;
use App\Utils\BaseResourcePolicy;
use Faker\Provider\Text;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PayoutAccountResource extends Resource
{
    protected static ?string $model = PayoutAccount::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function canAccess(): bool
    {
        return BaseResourcePolicy::canAccessResource();
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('account_name')->required(),
                Forms\Components\TextInput::make('account_number')->required()
                    ->rule([
                        'regex:/^0(24|25|26|27|28|50|54|55|56|57|58|59)[0-9]{7}$/',
                        'min:10',
                        'max:10',
                    ])
                ->validationMessages([
                    '*' => 'Please enter a valid 10-digit phone number (e.g., 0244123456).'
                ]),
                Forms\Components\Select::make('account_type')->required()->options(PayoutAccount::ACCOUNT_OPTIONS)->label('Operator'),
                Forms\Components\Select::make('bank_code')->options(PayoutAccount::MOMO_NETWORK_OPTIONS)->required()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('account_name'),
                TextColumn::make('account_number'),
                TextColumn::make('account_type'),
                TextColumn::make('bank_code')->label('Operator'),
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
            'index' => Pages\ListPayoutAccounts::route('/'),
            'create' => Pages\CreatePayoutAccount::route('/create'),
            'request_payout' => Pages\RequestPayout::route('/request-payout'),
            'view' => Pages\ViewPayoutAccount::route('/{record}'),
        ];
    }
}
