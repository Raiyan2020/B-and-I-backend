<div class="ag-btns  mr-2 mb-2 ml-auto ">
    <div class="action-btns">
        <div class="btn-dropdown ">
            <div class="btn-group dropdown actions-dropodown">
                <button type="button" class="btn btn-white px-2 py-75 dropdown-toggle waves-effect waves-light"
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Actions
                </button>
                <div class="dropdown-menu">
                    <a class="dropdown-item" id="{{$tableSlug}}-print" href="{{ request()->fullUrlWithQuery(['print' => 'true']) }}" target="_blank"><i class="feather icon-printer"></i>{{__('dashboard.print')}}</a>
                    <a class="dropdown-item" id="{{ $tableSlug }}-export"
                        href="{{ request()->fullUrlWithQuery(['export' => 'true']) }}"><i
                            class="feather icon-download"></i>
                        {{ __('dashboard.export') }}</a>
                </div>
            </div>
        </div>
    </div>
</div>
