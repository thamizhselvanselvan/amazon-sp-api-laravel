<?php

namespace App\Http\Controllers\Seller;

use RedBeanPHP\R;
use League\Csv\Writer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Services\SP_API\API\Catalog;
use Illuminate\Support\Facades\Auth;
use App\Models\seller\AsinMasterSeller;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;

class SellerCatalogController extends Controller
{
  private $writer;

  public function index(Request $request)
  {

    return view('seller.Catalog.index');
  }

  public function ImportCatalogDetails()
  {
    $login_user = Auth::user();
    $seller_id = $login_user->bb_seller_id;
    if ($seller_id == "") {
      $seller_id = $login_user->id;
    }

    if (App::environment(['Production', 'Staging', 'production', 'staging'])) {

      $base_path = base_path();
      $command = "cd $base_path && php artisan pms:seller-catalog-import $seller_id > /dev/null &";
      exec($command);
    } else {

      Log::info($seller_id);
      Artisan::call('pms:seller-catalog-import ' . $seller_id);
    }
    Log::alert("working on click");
  }

  public function catalogExport()
  {
    $user = Auth::user();
    $id = $user->bb_seller_id;
    if ($id == NULL) {
      $id = $user->id;
    }
    $id = 20;
    $user_name = $user->email;

    if (App::environment(['Production', 'Staging', 'production', 'staging'])) {

      $base_path = base_path();
      $command = "cd $base_path && php artisan pms:seller-catalog-csv-export $user_name $id > /dev/null &";
      exec($command);
    } else {
      // Log::info($seller_id);
      Artisan::call('pms:seller-catalog-csv-export ' . $user_name . ' ' . $id);
    }
  }
}
