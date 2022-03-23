<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\Facades\DataTables;

class AdminManagementController extends Controller
{
    function index(Request $request){

        if($request->ajax()){
            $login_id = Auth::user()->id;
            $users = User::whereHas(
                'roles', function($q){
                    $q->where('name', 'Admin');
            })->latest()->orderBy('created_at');

            return DataTables::of($users)
                ->addIndexColumn()
                ->addColumn('action', function ($user)  use($login_id) {
                    if($login_id == $user->id || $login_id == 1 )
                    {
                        return "<a href='password_reset_view/".$user->id."' class='btn btn-primary btn-sm'><i class='fas fa-edit'></i>Change password</a>";
                    }                   
                })
                ->rawColumns(['action'])
                ->make(true);

        }
        return view('admin.adminManagement.index');
   }
   
    function password_Change_view(Request $request){
        $user_id = $request->id;
        return view('admin.adminManagement.password_reset', compact('user_id'));
    }
    
    public function password_reset_save(Request $request, $id)
    {
        $request->validate([
            'password' => 'required|confirmed|min:3|max:18'
        ]);

        User::where('id', $id)->update([
            'password' => Hash::make($request->password)
        ]);
        
        return redirect()->intended('/admin/user_list')->with('success', 'Admin password has been changed successfully');
    }
    public function create()
    {
        
        return view('admin.adminManagement.add');
    }

    public function save_user(Request $request)
    {
       $request->validate([
       'password' => 'required|confirmed|min:3|max:18'
       ]);

       $am = User::create([
           'name' => $request->name,
           'email' => $request->email,
           'password' => Hash::make($request->password),

       ]);
       $role = $request->Role;
       $am->assignRole($role);
       
       return redirect()->intended('/admin/user_list')->with('success', 'User '.$request->name.' has been created successfully');

    }
}
