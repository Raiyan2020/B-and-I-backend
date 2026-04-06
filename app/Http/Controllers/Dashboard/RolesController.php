<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\Roles\StoreRoleRequest;
use App\Http\Requests\Dashboard\Roles\UpdateRoleRequest;
use App\Models\Admin;
use App\Models\Role;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Permission;
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


    public function store(StoreRoleRequest $request)
    {
        $validated = $request->validated();
        $slug = Role::generateUniqueSlug($validated['title']['en']);

        $role = Role::create([
            'name' => $slug,
            'guard_name' => 'admin',
        ]);
        $role->setTranslations('title', [
            'ar' => $validated['title']['ar'],
            'en' => $validated['title']['en'],
        ]);
        $role->save();
        $this->syncRolePermissions($role, $validated['permission']);

        return redirect()->route('admin.roles.index')->with(['success' => __('dashboard.item added successfully')]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function show(Role $role)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function edit(Role $role)
    {
        return view('dashboard.roles.edit',[
            'role' => $role,
            'permissions' => Permission::all()
        ]);
    }


    public function update(UpdateRoleRequest $request, Role $role)
    {
        $validated = $request->validated();
        $role->setTranslations('title', [
            'ar' => $validated['title']['ar'],
            'en' => $validated['title']['en'],
        ]);
        $role->save();
        $this->syncRolePermissions($role, $validated['permission']);

        return redirect()->route('admin.roles.index')->with(['success' => __('dashboard.item updated successfully')]);
    }

    /**
     * Sync permissions by ID. Request sends string IDs; Spatie otherwise treats "1" as a permission *name*.
     */
    protected function syncRolePermissions(Role $role, array $permissionIds): void
    {
        $ids = array_values(array_filter(array_map(static fn ($id) => (int) $id, $permissionIds)));
        $permissions = Permission::query()
            ->where('guard_name', $role->guard_name)
            ->whereIn('id', $ids)
            ->get();

        $role->syncPermissions($permissions);
    }

    public function destroy(Role $role): RedirectResponse|JsonResponse
    {
        $json = request()->ajax() || request()->wantsJson();

        if ($role->id === 1 || $role->name === 'super_admin') {
            if ($json) {
                return response()->json([
                    'key' => 'error',
                    'msg' => __('dashboard.cannot_delete_super_admin_role'),
                ], 422);
            }

            return back()->withErrors(['error' => __('dashboard.cannot_delete_super_admin_role')]);
        }

        $assignedCount = DB::table('model_has_roles')
            ->where('role_id', $role->id)
            ->where('model_type', Admin::class)
            ->count();

        if ($assignedCount > 0) {
            if ($json) {
                return response()->json([
                    'key' => 'error',
                    'msg' => __('dashboard.cannot_delete_role_assigned_to_admins'),
                ], 422);
            }

            return back()->withErrors(['error' => __('dashboard.cannot_delete_role_assigned_to_admins')]);
        }

        $role->delete();

        if ($json) {
            return response()->json([
                'key' => 'success',
                'msg' => __('dashboard.item deleted successfully'),
            ]);
        }

        return back()->with(['success' => __('dashboard.item deleted successfully')]);
    }
}
