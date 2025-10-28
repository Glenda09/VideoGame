<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Enums\Role;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $stats = [
            'products' => Product::count(),
            'categories' => Category::count(),
            'orders' => Order::count(),
            'customers' => User::where('role', '!=', Role::SuperAdmin->value)->count(),
        ];

        $latestProducts = Product::query()
            ->latest()
            ->limit(5)
            ->with('category')
            ->get();

        return view('admin.dashboard', [
            'stats' => $stats,
            'latestProducts' => $latestProducts,
        ]);
    }
}
