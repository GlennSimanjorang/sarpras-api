<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Borrowing;
use App\Models\Category;
use App\Models\Item;
use App\Models\ItemCategory;
use App\Models\Returning;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
//        User::query()->create([
//            "username" => "user1",
//            "password" => Hash::make("helloWorld1"),
//        ]);
//
       Admin::query()->create([
           "username" => "admin1",
           "password" => Hash::make("helloWorld1"),
       ]);
//
//        User::factory(10)->create();
//        Category::factory(5)->create();
//        Item::factory(20)->create();
//        ItemCategory::factory(20)->create();
//        Borrowing::factory(50)->create();

        Returning::query()->delete();

        $borrowings = Borrowing::all()->shuffle();
        foreach ($borrowings as $borrowing) {
            Returning::factory()->create([
                'borrow_id' => $borrowing->id
            ]);
        }
    }
}
