<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Roles;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Company\CompanyMaster;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\Facades\DataTables;

class AdminManagementController extends Controller
{
    function index(Request $request)
    {

        if ($request->ajax()) {
            $login_id = Auth::user()->id;
            $users = User::whereHas(
                'roles',
                function ($q) {
                    $q->where('name', 'Admin')->orWhere('name', 'User')->orWhere('name', 'Account');
                }
            )->latest()->orderBy('created_at');

            return DataTables::of($users)
                ->addIndexColumn()
                ->addColumn('action', function ($user)  use ($login_id) {
                    $edit = '';
                    if ($login_id == $user->id || $login_id == 1) {
                        $edit = "<a href='password_reset_view/" . $user->id . "' class='btn btn-primary btn-sm mr-2'><i class='fas fa-edit'></i>Change password</a>";
                    }
                    if ($login_id == 1) {
                        $edit .= '<a href="/admin/' . $user->id . '/edit" class="edit btn btn-success btn-sm"> <i class="fas fa-edit"></i> Edit</a>';
                    }
                    return $edit;
                })
                ->addColumn('permission', function ($permission) {
                    return $permission->roles->first()->name;
                })
                ->rawColumns(['action', 'permission'])
                ->make(true);
        }
        return view('admin.adminManagement.index');
    }

    function password_Change_view(Request $request)
    {
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
        $roles = Roles::get('name');
        $companys = CompanyMaster::get();
        return view('admin.adminManagement.add', compact(['roles', 'companys']));
    }

    public function save_user(Request $request)
    {
        $request->validate([
            'password' => 'required|confirmed|min:3|max:18'
        ]);

        // return $request->company;
        // exit;
        $am = User::create([
            'name' => $request->name,
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
        // dd($user->email);
        // dd($user);
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
        $am = User:: where('id', $id)->first();
        
        $am->update([
            'name' => $request->name,
            'email' => $request->email,
            'company_id' => $request->company,
            
        ]);

        $role = $request->Role;
        $am->assignRole($role);
        return redirect()->intended('/admin/user_list')->with('success', 'User ' . $request->name . ' has been updated successfully');
        // return $request;
    }
}
