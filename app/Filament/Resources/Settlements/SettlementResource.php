<?php

namespace App\Filament\Resources\Settlements;

use App\Filament\Resources\Settlements\Pages\CreateSettlement;
use App\Filament\Resources\Settlements\Pages\EditSettlement;
use App\Filament\Resources\Settlements\Pages\ListSettlements;
use App\Filament\Resources\Settlements\Schemas\SettlementForm;
use App\Filament\Resources\Settlements\Tables\SettlementsTable;
use App\Models\Merchant;
use App\Models\Payment;
use App\Models\Settlement;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SettlementResource extends Resource
{
    protected static ?string $model = Settlement::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return SettlementForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('merchant.merchant_name')->label('Merchant'),
                TextColumn::make('gross_amount'),
                TextColumn::make('total_fee'),
                TextColumn::make('settled_amount'),
                BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'pending',
                        'primary' => 'processing',
                        'success' => 'completed',
                        'danger'  => 'failed',
                    ]),
                TextColumn::make('settled_at')->dateTime()
                    ->placeholder('Not Transferd Yet'),
                
            ])->actions([
                 Action::make('Settle')
                ->label('Settle')
                ->requiresConfirmation()
                ->action(function (Settlement $record){
                    $record->update([
                        'status' => 'completed',
                        'settled_at' => now(),
            ]);
        })
        ->visible(fn (Settlement $record) => $record->status !== 'success')
        ->color('success')
            ])
            ->filters([
                Filter::make('settled_at')
                    ->form([
                        DatePicker::make('date')->default(now())
                    ])
                    ->query(function(Builder $query, $data){
                        return $query->when($data['date'], function (Builder $query, $date) {
                            logger('=>> ', [$date]);
                            return $query->whereDate('settled_at', $date);
                        });
                    })
                    ->indicateUsing(function(array $data) {
                        if(!$data['date']) {
                            return '';
                        }

                        return 'Settled At: ' . now()->parse($data['date'])->toFormattedDateString();
                    })
            ])
            
            ->headerActions([
                Action::make('generate_settlements')
                    ->label('Generate Settlements')
                    ->color('success')
                    ->schema([
                    Section::make()
                        ->schema([
                            DatePicker::make('date')
                                ->required()
                                ->maxDate(now()->subDay()),
                ]),
                    ])
                    ->requiresConfirmation()
                    ->action(function ($data) {
                        $date = $data['date'];
                        $payments = Payment::whereDate('created_at', $date)
                            ->where('settled_status', '!=', 'settled')
                            ->get();

                        if($payments->isEmpty()){
                            return;
                        };

                        $settlements = [];

                        foreach ($payments->groupBy('receiver_id') as $merchantId => $items) {

                            $grossAmount = $items->sum('amount');
                            $fee = 2;

                            $settlements[] = [
                                'merchant_id'    => $merchantId,
                                'gross_amount'   => $grossAmount,
                                'total_fee'      => $fee,
                                'settled_amount' => $grossAmount - $fee,
                                'currency'       => 'USD',
                                'status'         => 'pending',
                                'created_at'     => now(),
                                'settled_at'     => now(),
                                'updated_at'     => now(),
                            ];
                        }

                        Settlement::insert($settlements);

                        Payment::whereDate('created_at', $date)
                            ->where('settled_status', '!=', 'settled')
                            ->update([
                                'settled_status' => 'settled'
                            ]);
                    })
      
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
            'index' => ListSettlements::route('/'),
            'create' => CreateSettlement::route('/create'),
            'edit' => EditSettlement::route('/{record}/edit'),
        ];
    }
}
