<?php

namespace App\Filament\Resources\Loans\Tables;

use App\Models\Loan;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class LoansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('book.title')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('loan_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('due_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('returned_at')
                    ->date()
                    ->sortable(),
                TextColumn::make('status')
                    ->searchable(),
                TextColumn::make('denda')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                Action::make('Kembalikan Buku')
                ->icon('heroicon-o-arrow-uturn-left')
                ->requiresConfirmation()
                ->visible(fn (Loan $loan) => $loan->status === 'dipinjam')
                ->action(function (Loan $loan) {
                    $total_denda_terlambat = (int) $loan->due_date->diffInDays(now()->startOfDay(), false);

                    $loan->update([
                        'returned_at' => now(),
                        'status' => 'dikembalikan',
                        'denda' => max(0, $total_denda_terlambat) * 1000
                    ]);

                    $loan->book->increment('stock');
                    Notification::make()->title('Berhasil mengembalikan buku')->success()->send();
                }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
