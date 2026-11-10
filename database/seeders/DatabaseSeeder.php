<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Book;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        User::create(["name" => "Ahmad Kasim", "email" => "admin@example.test", "role" => "admin", "password" => "password"]);
        User::create(["name" => "dalilul Falihin", "email" => "siswa@example.test", "kelas" => "XII RPL 3", "password" => "password"]);

        Book::create(["title" => "Laskar Pelangi", "Author" => "Ariel Noah", "publisher" => "Telkom Schools", "year" => 2009]);
        Book::create(["title" => "Laskar Cinta", "Author" => "Ahmad Dhani", "publisher" => "Telkom Schools", "year" => 2018]);
        Book::create(["title" => "Everything U Are", "Author" => "Baskara", "publisher" => "Telkom Schools", "year" => 2022]);
    }
}
