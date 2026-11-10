<?php

namespace App\Filament\Resources\Books\Tables;

use App\Models\Book;
use App\Models\Loan;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class BooksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->searchable(),
                TextColumn::make('author')
                    ->searchable(),
                TextColumn::make('publisher')
                    ->searchable(),
                TextColumn::make('year')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('stock')
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
                Action::make('pinjam')
                ->icon('heroicon-o-book-open')
                ->requiresConfirmation()
                ->visible(fn () => !auth()->user()->isAdmin())
                ->disabled(fn (Book $book) => $book->stock < 1)
                ->action(function(Book $book) {
                    $total_buku_dipinjam = Loan::where('user_id', auth()->id())->where('status', 'dipinjam')->count();
                    if ($total_buku_dipinjam > 3) {
                        Notification::make()->title("Maksimal pinjam hanya 3 buku")->danger()->send();
                        return;
                    }

                    Loan::create([
                        'user_id' => auth()->id(),
                        'book_id' => $book->id,
                        'loan_date' => now(),
                        'due_date' => now()->addDays(7),
                    ]);

                    $book->decrement('stock');
                    Notification::make()->title("Berhasil meminjam buku")->success()->send();
                }),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
