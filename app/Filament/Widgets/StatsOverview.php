<?php

namespace App\Filament\Widgets;

use App\Models\Book;
use App\Models\Loan;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Judul Buku', Book::count())
                ->description('Total koleksi')
                ->descriptionIcon('heroicon-m-book-open')
                ->color('primary'),
            Stat::make('Anggota', User::where('role', 'siswa')->count())
                ->description('Siswa terdaftar')
                ->descriptionIcon('heroicon-m-users')
                ->color('success'),
            Stat::make('Sedang Dipinjam', Loan::where('status', 'dipinjam')->count())
                ->description('Belum dikembalikan')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),
        ];
    }
}