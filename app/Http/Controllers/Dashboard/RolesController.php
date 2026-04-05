<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Facades\DataTables;

class RolesController extends Controller
{

    public function __construct()
    {
        $this->middleware('permission:roles',['only'=>['index']]);
        $this->middleware('permission:add-role',['only'=>['create','store']]);
        $this->middleware('permission:edit-role',['only'=>['edit','update']]);
        $this->middleware('permission:delete-role',['only'=>['destroy']]);
    }


    public function index()
    {
        if (\request()->ajax()) {
            try {
                $data = Role::where('id','!=',1)
                    ->where('guard_name', 'admin')
                    ->get()
                    ->map(function ($role) {
                        $role->users_count = DB::table('model_has_roles')
                            ->where('role_id', $role->id)
                            ->where('model_type', 'App\Models\Admin')
                            ->count();
                        return $role;
                    });

                return Datatables::of($data)
                    ->addColumn('created_at', function ($role) {
                        return $role->created_at ? $role->created_at->format('Y-m-d H:i:s') : '';
                    })
                    ->make(true);
            } catch (\Exception $e) {
                Log::error('Roles DataTables Error: ' . $e->getMessage());
                return response()->json([
                    'draw' => request()->input('draw', 0),
                    'recordsTotal' => 0,
                    'recordsFiltered' => 0,
                    'data' => [],
                    'error' => 'An error occurred while loading data.'
                ], 500);
            }
        }

        return view('dashboard.roles.list');
    }

    public function create()
    {
        return view('dashboard.roles.add',['permissions'=>Permission::all()]);
    }


    public function store(Request $request)
    {
        $rules = [
            'name' => ['required','string',Rule::unique('roles','name')],
            'permission.*' => ['numeric','required',Rule::exists('permissions','id')],
            'permission' => ['required']
        ];

        $validator = Validator::make($request->all(),$rules);

        if($validator->fails()){

            return back()->withErrors($validator);
        }

        $role =Role::create(['name'=>$validator->validated()['name']]);
        $role->syncPermissions($validator->validated()['permission']);
        return redirect()->route('admin.roles.index')->with(['success' => __('dashboard.item added successfully')]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \Spatie\Permission\Models\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function show(Role $role)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \Spatie\Permission\Models\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function edit(Role $role)
    {
        return view('dashboard.roles.edit',[
            'role' => $role,
            'permissions' => Permission::all()
        ]);
    }


    public function update(Request $request, Role $role)
    {
//        dd($role);
        $rules = [
            'name' => ['required','string',Rule::unique('roles','name')->ignore($role->id)],
            'permission.*' => ['required',Rule::exists('permissions','id')],
            'permission' => ['required','array']
        ];
        $validator = Validator::make($request->all(),$rules);
        if($validator->fails()){
            return back()->withErrors($validator);
        }
        $request->name == $role->name ?:$role->update(['name' => $validator->validated()['name']]);
        $role->syncPermissions($validator->validated()['permission']);
        return redirect()->route('admin.roles.index')->with(['success' => __('dashboard.item updated successfully')]);
    }


    public function destroy(Role $role)
    {
        $role->delete();

        return back()->with(['success' => __('dashboard.item deleted successfully')]);
    }
}
