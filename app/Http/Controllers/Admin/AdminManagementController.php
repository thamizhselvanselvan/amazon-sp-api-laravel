<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Roles;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Admin\BB\BB_User;
use App\Models\Company\CompanyMaster;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\Facades\DataTables;

class AdminManagementController extends Controller
{
    function index(Request $request)
    {
        $user = Auth::user();
        $login_id = $user->id;
        $role = $user->roles->first()->name;
        $users = User::latest()->orderBy('created_at')->get();
        // dd($users[0]->roles);
        if ($request->ajax()) {

            return DataTables::of($users)
                ->addIndexColumn()
                ->addColumn('action', function ($user)  use ($login_id, $role) {
                    $edit = '';
                    if ($login_id == $user->id || $role =='Admin' && $user->id != 1 ) {
                        $edit = "<a href='password_reset_view/" . $user->id . "' class='btn btn-primary btn-sm mr-2'><i class='fas fa-edit'></i>Change password</a>";
                    }
                    if ($login_id == $user->id || $role =='Admin' && $user->id != 1 ) {
                        $edit .= '<a href="/admin/' . $user->id . '/edit" class="edit btn btn-success btn-sm"> <i class="fas fa-edit"></i> Edit</a>';
                    }
                    return $edit;
                })
                ->addColumn('permission', function ($permission) {
                    $roles = $permission->roles;
                    $roles = json_decode($roles);
                    $multiple_roles = '';
                    foreach($roles as $key => $role){
                        $multiple_roles .= $role->name.', ';
                    }

                    return rtrim($multiple_roles,', ');

                })
                ->rawColumns(['action', 'permission'])
                ->make(true);
        }
        return view('admin.adminManagement.index');
    }

    function password_Change_view(Request $request, $id)
    {

        $user_exists = User::where('id', $id)->exists();

        if(!$user_exists) {
            return redirect()->intended('/admin/user_list')->with("error", "User does not exists");
        }

        $user = Auth::user();
        $login_id = $user->id;
        $role = $user->roles->first()->name;

        $user_id = $request->id;

        if ($login_id == $user_id || $role == 'Admin' && $user_id != 1 ) {
            return view('admin.adminManagement.password_reset', compact('user_id'));
        }
        
        return redirect()->intended('/admin/user_list')->with("error", "You don't have permission to change the password");
    }

    public function password_reset_save(Request $request, $id)
    {
        $request->validate([
            'password' => 'required|confirmed|min:3|max:18'
        ]);

        $user_exists = User::where('id', $id)->exists();

        if(!$user_exists) {
            return redirect()->intended('/admin/user_list')->with("error", "User does not exists");
        }

        User::where('id', $id)->update([
            'password' => Hash::make($request->password)
        ]);

        return redirect()->intended('/admin/user_list')->with('success', 'Admin password has been changed successfully');
    }

    public function create()
    {
        $roles = Roles::get('name');
        $companys = CompanyMaster::get();
        return view('admin.adminManagement.add', compact(['roles', 'companys']));
    }

    public function save_user(Request $request)
    {
        $request->validate([
            'password' => 'required|confirmed|min:3|max:18'
        ]);
        $seller_id = NULL;
        foreach($request->Role as $role)
        {
           if($role == 'Seller')
           {
               BB_User::create([
                    'internal_seller' => 2,
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                    'status' => 1
               ]);

               $user_details = BB_User::where('email', $request->email)->get('id');
               $seller_id = $user_details->first()->id;
           }
        }

        $am = User::create([
            'name' => $request->name,
            'bb_seller_id' => $seller_id,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'company_id' => $request->company,

        ]);
        $role = $request->Role;
        $am->assignRole($role);

        return redirect()->intended('/admin/user_list')->with('success', 'User ' . $request->name . ' has been created successfully');
    }

    public function edit(Request $request)
    {
        $user = User::where('id', $request->id)->first();
        $user_id = $request->id;
   
        $user_email = $user->email;
        $user_name = $user->name;
        $selected_roles = $user->roles->first()->name;
        $selected_company = $user->company_id;

        $roles = Roles::get('name');
        $companys = CompanyMaster::get();
        return view('admin.adminManagement.edit', compact(['roles', 'companys', 'user_name', 'user_email', 'selected_roles', 'selected_company','user_id']));
    }
    
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required'
        ]);
        $user = User:: where('id', $id)->first();
        
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'company_id' => $request->company,
        ]);

        $role = $request->Role;
        $user->roles()->detach();
        $user->assignRole($role);
        return redirect()->intended('/admin/user_list')->with('success', 'User ' . $request->name . ' has been updated successfully');
        // return $request;
    }
}
