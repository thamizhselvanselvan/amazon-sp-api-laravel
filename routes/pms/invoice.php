<?php

use Illuminate\Support\Facades\Route;

Route::get('invoice/manage', 'invoice\InvoiceManagementController@Index');
Route::get('invoice/upload', 'invoice\InvoiceManagementController@Upload');
Route::post('invoice/upload/excel', 'invoice\InvoiceManagementController@UploadExcel');
Route::get('invoice/template', 'invoice\InvoiceManagementController@showpdf');
Route::get('invoice/convert-pdf/{id}', 'invoice\InvoiceManagementController@showTemplate');
Route::post('invoice/export-pdf','invoice\InvoiceManagementController@ExportPdf');