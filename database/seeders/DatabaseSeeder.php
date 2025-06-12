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
use Carbon\Carbon;

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
// //
//        Admin::query()->create([
//            "username" => "kenneth",
//            "password" => Hash::make("helloWorld1"),
//        ]);
//
//        User::factory(10)->create();
//        Category::factory(5)->create();
//        Item::factory(20)->create();
//        ItemCategory::factory(20)->create();
        $users = User::all();
        $items = Item::all();

        for ($i = 0; $i < 100; $i++) {
            $createdAt = Carbon::createFromDate(2025, 4, 1)->addDays(rand(0, 90)); // random antara 1 April - 30 Juni
            $dueDate = (clone $createdAt)->addDays(rand(3, 14)); // 3-14 hari dari tanggal dibuat

            $borrowing = Borrowing::create([
                'user_id' => $users->random()->id,
                'item_id' => $items->random()->id,
                'quantity' => rand(1, 5),
                'status' => rand(0, 1) ? 'approved' : 'pending',
                'approved_by' => 1,
                'approved_at' => $createdAt->copy()->addHours(rand(1, 48)),
                'due_date' => $dueDate->format('Y-m-d'),
                'created_at' => $createdAt,
                'updated_at' => $createdAt->copy()->addHours(rand(1, 72)),
            ]);
        }

        Returning::query()->delete();

        $borrowings = Borrowing::all()->shuffle();
        foreach ($borrowings as $borrowing) {
            Returning::factory()->create([
                'borrow_id' => $borrowing->id
            ]);
        }
    }
}
