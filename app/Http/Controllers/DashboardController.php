<?php 
namespace App\Http\Controllers;

use App\Models\Item;
use App\Utility\ApiResponse;
use App\Models\Borrowing;
use App\Models\User;
use App\Models\Returning;
use App\Models\Category;

class DashboardController extends Controller
{
    public function index()
    {
        $totalUsers = User::count();
        $totalBorrowings = Borrowing::count();
        $totalItems = Item::count();
        $totalReturnings = Returning::count();
        $totalCategories = Category::count();
        

        return ApiResponse::send(200, "Dashboard summary retrieved", null, [
            'total_items' => $totalItems,
            'total_users' => $totalUsers,
            'total_borrowings' => $totalBorrowings,
            'total_returnings' => $totalReturnings,
            'total_categories' => $totalCategories,
        ]);
    }
}
