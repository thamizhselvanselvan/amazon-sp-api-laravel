<?php

namespace App\Http\Controllers\Inventory;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class InventoryStockController extends Controller
{
    public function StockIndex()
    {
        return view('Inventory.Stock.Index');
    }
}