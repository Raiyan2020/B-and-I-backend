<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;
use App\Support\QueryOptions;
use Yajra\DataTables\Facades\DataTables;



class AdminBasicController extends Controller
{

    public function __construct(
        protected        $model,
        protected        $storeRequest,
        protected        $updateRequest,
        protected string $directoryName,
        protected object $serviceName,
        protected string $indexScopes = '',
        protected array  $with = [],
        protected array  $indexConditions = [],
        protected array  $indexCompactVariables = [],
        protected array  $createCompactVariables = [],
        protected array  $editCompactVariables = [],
        protected array  $showCompactVariables = [],
        protected array  $destroyOneConditions = [],
        protected array  $destroyRelationsToCheck = [],
        protected array  $relationsConditions = []
    ) {
        // Ensure service has model set
        if (method_exists($this->serviceName, 'getModel') && !$this->serviceName->getModel()) {
            $this->serviceName->setModel($this->model);
        }
    }

    protected function modelName(): string
    {
        return (string) Str::of($this->model)->afterLast('\\')->lower();
    }

    protected function getClassNameTranslated(): string
    {
        return __('admin.' . $this->modelName());
    }

    protected function pluralModelName(): string
    {
        return Str::plural($this->modelName());
    }


    public function index(): View|JsonResponse
    {
        if (request()->ajax()) {
            $limitOptions = (new QueryOptions())
                ->paginateNum(30)
                ->scopes($this->indexScopes ?? 'search')
                ->conditions($this->indexConditions ?? [])
                ->with($this->with ?? [])
                ->latest(false);
            $rows = $this->serviceName->all($limitOptions);
            return DataTables::of($rows)->make(true);
        }

        return view(
            'dashboard.' . $this->directoryName . '.index', $this->indexCompactVariables ?? []);
    }

    public function create(): View
    {
        return view('dashboard.' . $this->directoryName . '.create', $this->createCompactVariables ?? []);
    }

    public function store(): JsonResponse|RedirectResponse
    {
        $this->storeRequest = app($this->storeRequest);
        $validated = $this->storeRequest->validated();

        // Allow service to preprocess data
        if (method_exists($this->serviceName, 'storeData')) {
            $validated = $this->serviceName->storeData($validated);
        }

        $model = $this->serviceName->create($validated);

        // Allow service to handle post-create actions
        if (method_exists($this->serviceName, 'afterCreate')) {
            $this->serviceName->afterCreate($model,$validated);
        }

        return response()->json(['url' => route($this->currentRouteName() . '.index')]);
    }

    public function update($id): JsonResponse|RedirectResponse
    {
        $this->updateRequest = app($this->updateRequest);
        $validated = $this->updateRequest->validated();

        // Allow service to preprocess data
        if (method_exists($this->serviceName, 'updateData')) {
            $validated = $this->serviceName->updateData($validated, $id);
        }

        $this->serviceName->update(id: $id, data: $validated);

        // Get updated model for afterUpdate hook
        $model = $this->serviceName->find($id);

        // Allow service to handle post-update actions
        if (method_exists($this->serviceName, 'afterUpdate')) {
            $this->serviceName->afterUpdate($model,$validated);
        }

        return response()->json(['url' => route($this->currentRouteName() . '.index'),'msg' => __('dashboard.item updated successfully')]);
    }

    public function edit($id): View
    {
        $row = $this->serviceName->find($id);
        return view('dashboard.' . $this->directoryName . '.edit', array_merge(['row' => $row], $this->editCompactVariables ?? []));
    }

    public function show($id): View|JsonResponse
    {
        $row = $this->serviceName->find(id: $id, with: $this->with ?? []);
        return view('dashboard.' . $this->directoryName . '.show', array_merge(['row' => $row], $this->showCompactVariables ?? []));
    }

    public function destroy($id): JsonResponse
    {
        $result = $this->serviceName->delete(
            id: $id,
            relationsToCheck: $this->destroyRelationsToCheck ?? [],
            conditions: $this->destroyOneConditions ?? [],
            relationConditions: $this->relationsConditions ?? []
        );
        return response()->json(['key' => $result['key'], 'msg' => $result['msg']]);
    }

    public function destroyMultiple(Request $request): JsonResponse
    {
        $result = $this->serviceName->deleteMultiple(
            request: $request,
            relationsToCheck: $this->destroyRelationsToCheck ?? [],
            relationConditions: $this->relationsConditions ?? []
        );
        return response()->json(['key' => $result['key'], 'msg' => $result['msg']]);
    }

    protected function currentRouteName(): string
    {
        $currentRouteName = request()->route()->getName();
        return substr($currentRouteName, 0, strrpos($currentRouteName, '.'));
    }
}
