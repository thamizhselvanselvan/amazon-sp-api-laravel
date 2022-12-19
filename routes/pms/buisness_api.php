<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use App\Services\AWS_Business_API\AWS_POC\Orders;
use App\Http\Controllers\PMSPHPUnitTestController;
use App\Http\Controllers\BuisnessAPI\ProductsRequestController;
use App\Services\AWS_Business_API\Details_dump\product_details;

Route::get('product/details', 'BuisnessAPI\SearchProductRequestController@searchproductRequest');
Route::resource('business/search/products', 'BuisnessAPI\SearchProductRequestController');

Route::get('buisness/product/details', 'BuisnessAPI\ProductsRequestController@productRequestasin');
Route::resource('business/products/request', 'BuisnessAPI\ProductsRequestController');

Route::get('buisness/product/offers', 'BuisnessAPI\searchOffersRequestController@searchoffersproduct');
Route::resource('business/offers', 'BuisnessAPI\searchOffersRequestController');

Route::get('business/asin/details', 'BuisnessAPI\GetProductsByAsinsController@searchasinproduct');
Route::resource('business/byasins', 'BuisnessAPI\GetProductsByAsinsController');

Route::resource('business/details', 'BuisnessAPI\ProductDetailsController');
Route::get('buisness/details', 'BuisnessAPI\ProductDetailsController@viewpro');

Route::get('business/orders/details', 'BuisnessAPI\OrdersController@test');
Route::get('business/orders/view', 'BuisnessAPI\OrdersController@getorders');
Route::get('business/orders/pending', 'BuisnessAPI\OrdersController@orderspending');
Route::get('business/offers_view', 'BuisnessAPI\OrdersController@prodoffers');
Route::get('business/order/book', 'BuisnessAPI\OrdersController@orderbooking');

Route::get('business/booked/details', 'BuisnessAPI\OrdersController@booked');
Route::get('business/booked/list', 'BuisnessAPI\OrdersController@booked')->name('business.orders.booked.list');

Route::get('business/orders/confirm','BuisnessAPI\OrdersController@confirmation');
Route::get('business/orders/confirm/list','BuisnessAPI\OrdersController@confirmation')->name('business.orders.confirm.list');
Route::get('business/ship/confirmation','BuisnessAPI\OrdersController@notification');
Route::get('business/orders/shipment/list','BuisnessAPI\OrdersController@notification')->name('business.orders.shipment.list');

Route::resource('business/orders', 'BuisnessAPI\OrdersController');

Route::get('cliqnshop/kyc', 'Cliqnshop\CliqnshopKycController@kyc_index')->name('cliqnshop.kyc');
Route::get('cliqnshop/kyc/details', 'Cliqnshop\CliqnshopKycController@kyc_details')->name('cliqnshop.kyc.view');
Route::get('cliqnshop/kyc/update', 'Cliqnshop\CliqnshopKycController@kyc_status')->name('cliqnshop.kyc.update');

Route::get('cliqnshop/contact', 'Cliqnshop\ContactListController@contactlist')->name('cliqnshop.contacted');
Route::get('cliqnshop/contact/list', 'Cliqnshop\ContactListController@contactlist')->name('cliqnshop.contacted.list');

Route::get('cliqnshop/banner', 'Cliqnshop\ImageBrandController@threebanner')->name('cliqnshop.banner');
Route::post('cliqnshop/banner/image', 'Cliqnshop\ImageBrandController@storeimage')->name('cliqnshop.image.store');

Route::get('cliqnshop/brand', 'Cliqnshop\ImageBrandController@topselling')->name('cliqnshop.brand');
Route::post('cliqnshop/brand/store', 'Cliqnshop\ImageBrandController@storeasin')->name('cliqnshop.brand.store');

Route::get('cliqnshop/two_banners', 'Cliqnshop\ImageBrandController@twobannersection')->name('cliqnshop.twobanners');
Route::post('cliqnshop/2banners/store', 'Cliqnshop\ImageBrandController@two_bannerstore')->name('cliqnshop.two.banner.store');

Route::get('cliqnshop/one_banners', 'Cliqnshop\ImageBrandController@onebanner')->name('cliqnshop.onebanner');
Route::post('cliqnshop/1banners/store', 'Cliqnshop\ImageBrandController@one_bannerstore')->name('cliqnshop.one.banner.store');

Route::get('cliqnshop/trending', 'Cliqnshop\ImageBrandController@trendingbrandssection')->name('cliqnshop.trending');
Route::post('cliqnshop/trending/store', 'Cliqnshop\ImageBrandController@trendingbrands')->name('cliqnshop.trending.store');