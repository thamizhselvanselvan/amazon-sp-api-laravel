<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class CatalogManagementController extends Controller
{
    function index(Request $request){

        if($request->ajax()){
            $users = User::whereHas(
                'roles', function($q){
                    $q->where('name', 'Catalog Manager ');
            })->latest()->orderBy('created_at');

            return DataTables::of($users)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                   
                    $actionBtn = '<a href="/admin/catalog/'.$row->id.'/edit" class="edit btn btn-success btn-sm"> <i class="fas fa-edit"></i> Edit</a>';
                    $actionBtn .= '<a href="/admin/catalog/'.$row->id.'/password_reset" class="password_reset btn btn-primary ml-2 btn-sm"> <i class="fas fa-key"></i> Reset Password</a>';
               
                    return $actionBtn;
                })
                ->rawColumns(['action'])
                ->make(true);

        }
        return view('admin.catalogManagement.index');
    }

    function password_reset_view(Request $request){
        $user_id = $request->id;
        return view('admin.catalogManagement.password_reset', compact('user_id'));
    }

    public function password_reset_save(Request $request, $id)
    {
        $request->validate([
            'password' => 'required|confirmed|min:3|max:18'
        ]);

        User::where('id', $id)->update([
            'password' => Hash::make($request->password)
        ]);
        
        return redirect()->intended('/admin/catalog_user')->with('success', 'Catalog password has been changed successfully');
    }
}