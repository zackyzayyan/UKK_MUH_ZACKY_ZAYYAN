<?php

namespace App\Filament\Resources\Loans\Tables;

use App\Models\Loan;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;


class LoansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
    TextColumn::make('user.name')->label('Peminjam')->searchable()->sortable(),
    TextColumn::make('book.title')->label('Buku')->searchable()->limit(30),
    TextColumn::make('loan_date')->label('Tgl Pinjam')->date('d M Y')->sortable(),
    TextColumn::make('due_date')->label('Jatuh Tempo')->date('d M Y')->sortable()
        ->color(fn ($record) => $record->status === 'dipinjam' && $record->due_date->isPast() ? 'danger' : null),
    TextColumn::make('status')->badge()
        ->color(fn (string $state) => match ($state) {
            'dipinjam'     => 'warning',
            'dikembalikan' => 'success',
            default        => 'gray',
        }),
    TextColumn::make('fine')->label('Denda')->money('IDR', locale: 'id')->placeholder('-'),
])
            ->filters([
                //
            ])
            ->recordActions([
                Action::make('kembalikan')
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->requiresConfirmation()
                    ->visible(fn (Loan $record) => $record->status === 'dipinjam')
                    ->action(function (Loan $record) {
                        $telat = (int) $record->due_date->diffInDays(now()->startOfDay(), false);
                        $record->update([
                            'returned_at' => now(),
                            'status' => 'dikembalikan',
                            'fine' => max(0, $telat) * 1000,
                        ]);
                        $record->book->increment('stock');
                        Notification::make()->title('Buku sudah dikembalikan')->success()->send();
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
