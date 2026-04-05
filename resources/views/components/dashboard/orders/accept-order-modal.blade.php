<div class="modal fade text-left" id="acceptModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel33"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success white">
                <h4 class="modal-title" id="myModalLabel33">{{ __('dashboard.accept_order') }} </h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="get" id="modal-change-state-form">
                @csrf
                <div class="modal-body">
                    <h5>{{ __('dashboard.accept_order_msg') }}</h5>
                </div>
                <div class="modal-footer">
                    <a class="btn btn-success" id="accept-btn-order-modal">
                        {{ __('dashboard.save') }}</a>
                </div>
            </form>
        </div>
    </div>
</div>
