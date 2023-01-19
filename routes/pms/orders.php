<?php

use App\Http\Controllers\PMSPHPUnitTestController;
use App\Models\Order\ZohoMissing;
use Illuminate\Support\Facades\Route;

Route::get('orders/dashboard', 'Orders\OrdersDashboardController@Dashboard');
Route::get('orders/list', 'Orders\OrdersListController@index');
Route::get('orders/getlist', 'Orders\OrdersListController@GetOrdersList')->name('getOrder.list');
Route::get('orders/itemsdetails', 'Orders\OrdersListController@GetOrderitems')->name('getOrderitem.list');

Route::get('orders/details', 'Orders\OrdersListController@OrderDetails');
Route::get('orders/item-details', 'Orders\OrdersListController@OrderItemDetails');
Route::get('orders/getdetails/', 'Orders\OrdersListController@GetOrderDetails')->name('getOrder.details');

Route::get('orders/item/dashboard', 'Orders\OrdersDashboardController@OrderItemDashboard');
Route::get('orders/aws/dashboard', 'Orders\OrdersDashboardController@AwsOrderDashboard')->name('order.aws.dashboard');

Route::get('orders/csv/import', 'Orders\OrdersDashboardController@OrderCsvImport')->name('orders.csv.import');
Route::POST('orders/import/file', 'Orders\OrdersDashboardController@ImportOrdersFile')->name('import.orders.file');
Route::get('orders/csv/download', 'Orders\OrdersDashboardController@OrderCsvDownload')->name('download.order.csv.template');

Route::get('orders/details/list', 'Orders\OrderDetailsController@index')->name('orders.search.index');
// Route::post('orders/search/details', 'Orders\OrderDetailsController@search')->name('orders.search');
Route::post('orders/details/update', 'Orders\OrderDetailsController@update')->name('orders.searched.update');
Route::post('orders/bulk/search', 'Orders\OrderDetailsController@bulksearch')->name('orders.search.bulk');
Route::get('orders/bulk/edit/{id}', 'Orders\OrderDetailsController@bulkedit');

Route::get('orders/statistics', 'Orders\OrderDetailsController@orderStatistics')->name('orders.statistics');
Route::get('orders/statistics/{store_id}', 'Orders\OrderDetailsController@orderStatistics')->name('orders.statistics.store_id');

Route::get('orders/file/management/monitor', 'Orders\OrdersDashboardController@OrderFileManagementMonitor')->name('orders.file.management.monitor');

Route::get('orders/missing/price', 'Orders\OrderMissingDetailsController@index')->name('orders.missing');
Route::post('orders/missing/update', 'Orders\OrderMissingDetailsController@updateview')->name('orders.missing.price.update');
