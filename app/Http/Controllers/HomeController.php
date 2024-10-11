<?php

namespace App\Http\Controllers;

use App\Complaint;
use App\Product;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        /** @var User $user */
        $user = auth()->user();
        $p = $user->getAllPermissions();
        $user = $user->toArray();
        $user['permissions'] = $p;
        return view('home' , [
            'user' => $user
        ]);
    }

    public function dashboard()
    {
        return null;

    }

    public function dashboardStats(){
        $totalUsers = User::query()->count();
        $totalProducts = Product::query()->count();
        $totalSales = 5;

        return response()->json([
            'totalUsers' => $totalUsers,
            'totalProducts' => $totalProducts,
            'totalSales' => $totalSales,
        ]);
    }


}
