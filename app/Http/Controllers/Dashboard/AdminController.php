<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Admin;
use App\Support\QueryOptions;
use Spatie\Permission\Models\Role;
use App\Facades\BaseService as FacadesBaseService;
use App\Http\Requests\Dashboard\Admins\StoreRequest;
use App\Http\Requests\Dashboard\Admins\UpdateRequest;
use Illuminate\Support\Facades\Route;
use App\Services\AllUsers\AdminService;
class AdminController extends AdminBasicController
{
    public function __construct()
    {
        $this->middleware('permission:admins', ['only' => ['index','show']]);
        $this->middleware('permission:add-admin', ['only' => ['create', 'store']]);
        $this->middleware('permission:edit-admin', ['only' => ['edit', 'update']]);
        $this->middleware('permission:delete-admin', ['only' => ['destroy', 'destroyMulti']]);
        $this->middleware('permission:block-admin', ['only' => ['toggleBlock']]);
        // $this->middleware('permission:show-admin', ['only' => ['show']]);

        $this->model = Admin::class;
        $this->serviceName = new AdminService();
        $this->storeRequest = StoreRequest::class;
        $this->updateRequest = UpdateRequest::class;
        $this->directoryName = 'admins';
        $this->with = ['roles'];
        $roles = FacadesBaseService::setModel(Role::class)
            ->all((new QueryOptions())->conditions([['name', '!=', 'super_admin']]));
        if (Route::currentRouteName() == 'admin.admins.index') {
            $this->indexCompactVariables = ['roles' => $roles];
            $this->indexConditions = [['id', '!=', auth()->id()], ['id', '!=', 1]];
        }
        $this->indexScopes = 'search';
        if (Route::currentRouteName() == 'admin.admins.create') {
            $this->createCompactVariables = ['roles' => $roles];
        }
        if (Route::currentRouteName() == 'admin.admins.edit') {
            $this->editCompactVariables = ['roles' => $roles];
        }
    }

    public function toggleBlock($id)
    {
        try {
            $result = $this->serviceName->toggleBlock($id);
            return response()->json([
                'key' => 'success',
                'msg' => $result['msg']
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'key' => 'error',
                'msg' => $e->getMessage()
            ], 500);
        }
    }
}
