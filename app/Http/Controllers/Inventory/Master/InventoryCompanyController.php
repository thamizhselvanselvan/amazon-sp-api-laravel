<?php

namespace App\Http\Controllers\Inventory\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class InventoryCompanyController extends Controller
{
    public function companyview()
    {
        return view('Inventory.Master.Company.Index');
    }
    public function companyadd()
    {
        return view('Inventory.Master.Company.Add');
    }
}
