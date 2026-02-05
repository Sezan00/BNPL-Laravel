<?php

namespace App\Filament\Resources\InstallmentPackegs;

use App\Filament\Resources\InstallmentPackegs\Pages\CreateInstallmentPackeg;
use App\Filament\Resources\InstallmentPackegs\Pages\EditInstallmentPackeg;
use App\Filament\Resources\InstallmentPackegs\Pages\ListInstallmentPackegs;
use App\Filament\Resources\InstallmentPackegs\Schemas\InstallmentPackegForm;
use App\Filament\Resources\InstallmentPackegs\Tables\InstallmentPackegsTable;
use App\Models\InstallmentPackeg;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class InstallmentPackegResource extends Resource
{
    protected static ?string $model = InstallmentPackeg::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'InstallmentPackeg';

    public static function form(Schema $schema): Schema
    {
     return $schema->components([
        TextInput::make('name')
            ->required()
            ->maxLength(255),
        Select::make('term')
               ->label('Payment Term')
               ->options([
                 'weekly' => 'Weekly',
                 'bi_weekly' => 'Bi-Weekly',
                 'monthly' => 'Monthly',
               ]),

        TextInput::make('installment_count')
            ->numeric()
            ->required()
            ->minValue(1),

        TextInput::make('fixed_profit')
            ->numeric()
            ->prefix('$')
            ->default(0),
        
        TextInput::make('interest_percent')
        ->numeric()
        ->suffix('%')
        ->default(0),


        Toggle::make('is_active')
            ->default(true),
    ]);
    }

    public static function table(Table $table): Table
    {
        return $table
                ->columns([
                    TextColumn::make('name')->label('Name'),
                   TextColumn::make('term')
                        ->label('Term')
                        ->formatStateUsing(fn ($state) => match ($state) {
                            'weekly' => 'Weekly',
                            'bi_weekly' => 'Bi-Weekly',
                            'monthly' => 'Monthly',
                            default => $state,
                        }),
                    TextColumn::make('installment_count')->label('Installment Count'),
                    TextColumn::make('fixed_profit')->label('Fixed Profit')->money('usd', true),
                    TextColumn::make('interest_percent')->label('Interest Percent')->suffix('%'),
                ])
                ->recordActions([
                    EditAction::make(),
                    DeleteAction::make(),
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
            'index' => ListInstallmentPackegs::route('/'),
            'create' => CreateInstallmentPackeg::route('/create'),
            'edit' => EditInstallmentPackeg::route('/{record}/edit'),
        ];
    }
}
