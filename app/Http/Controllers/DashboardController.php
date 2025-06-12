<?php 
namespace App\Http\Controllers;

use App\Models\Item;
use App\Utility\ApiResponse;
use App\Models\Borrowing;
use App\Models\User;
use App\Models\Returning;
use App\Models\Category;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $totalUsers = User::count();
        $totalBorrowings = Borrowing::count();
        $totalItems = Item::count();
        $totalReturnings = Returning::count();
        $totalCategories = Category::count();

        $dueDates = Borrowing::select('due_date', DB::raw('SUM(quantity) as items_due'))
            ->groupBy('due_date')
            ->orderBy('due_date')
            ->get();
        

        return ApiResponse::send(200, "Dashboard summary retrieved", null, [
            'total_items' => $totalItems,
            'total_users' => $totalUsers,
            'total_borrowings' => $totalBorrowings,
            'total_returnings' => $totalReturnings,
            'total_categories' => $totalCategories,
            'due_date_summary' => $dueDates,
        ]);
    }
}
