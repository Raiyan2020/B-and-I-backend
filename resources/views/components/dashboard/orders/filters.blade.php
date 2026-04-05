<div class="card">
    <div class="card-header">
        <h4 class="card-title">{{ __('dashboard.filter') }}</h4>
        <a class="heading-elements-toggle"><svg class="svg-inline--fa fa-ellipsis-vertical font-medium-3"
                aria-hidden="true" focusable="false" data-prefix="fas" data-icon="ellipsis-vertical" role="img"
                xmlns="http://www.w3.org/2000/svg" viewBox="0 0 128 512" data-fa-i2svg="">
                <path fill="currentColor"
                    d="M64 360c30.9 0 56 25.1 56 56s-25.1 56-56 56s-56-25.1-56-56s25.1-56 56-56zm0-160c30.9 0 56 25.1 56 56s-25.1 56-56 56s-56-25.1-56-56s25.1-56 56-56zM120 96c0 30.9-25.1 56-56 56S8 126.9 8 96S33.1 40 64 40s56 25.1 56 56z">
                </path>
            </svg><!-- <i class="fa fa-ellipsis-v font-medium-3"></i> Font Awesome fontawesome.com --></a>
        <div class="heading-elements">
            <ul class="list-inline mb-0">
                <li><a data-action="collapse"><i class="feather icon-chevron-down"></i></a></li>
                <li><a data-action="close"><i class="feather icon-x"></i></a></li>
            </ul>
        </div>
    </div>
    <div class="card-content collapse show">
        <div class="card-body">
            <div class="users-list-filter">
                <div class="row">
                    <div class="col-12 col-sm-6 col-lg-3">
                        <label for="event-id">{{ __('dashboard.events') }}</label>
                        <fieldset class="form-group">
                            <select class="form-control" id="orders-event-id" name="event_id">
                                <option selected value="">All</option>
                                @foreach ($events as $event)
                                    <option value="{{ $event->id }}">{{ $event->name }}</option>
                                @endforeach
                            </select>
                        </fieldset>
                    </div>
                    <div class="col-12 col-sm-6 col-lg-3">
                        <label for="ticket-ategory">{{ __('dashboard.tickets_cat') }}</label>
                        <fieldset class="form-group">
                            <select class="form-control" id="orders-ticket-category" name="ticket_category">
                                <option selected value="">All</option>
                                @foreach ($cats as $cat)
                                    <option value="{{ $cat->name }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </fieldset>
                    </div>
                    <div class="col-12 col-sm-6 col-lg-3">
                        <label for="side-by-side-status">{{ __('dashboard.side_by_side') }}</label>
                        <fieldset class="form-group">
                            <select class="form-control" id="orders-side-by-side" name="side_by_side">
                                <option selected value="">All</option>
                                <option value="1">{{ __('home.yes') }}</option>
                                <option value="0">{{ __('home.no') }}</option>
                            </select>
                        </fieldset>
                    </div>
                    <div class="col-12 col-sm-6 col-lg-3">
                        <label for="users-list-verified">{{ __('dashboard.filter') }}</label>
                        <input type="search" name="filter" id="orders-filter-input" class="form-control"
                            placeholder="{{ __('dashboard.filter') }}">
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
