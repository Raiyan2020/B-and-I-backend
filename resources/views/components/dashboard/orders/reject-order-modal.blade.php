<div class="modal fade text-left" id="rejectModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel33"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger white">
                <h4 class="modal-title" id="myModalLabel33">{{ __('dashboard.reject_order') }} </h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="post" action="" class="reject-order-form">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="first-name-icon">{{ __('dashboard.rejected_reason') }}</label>
                        <input type="text" name="reject_reason" class="form-control" placeholder="{{ __('dashboard.rejected_reason') }}" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-danger">
                        {{ __('dashboard.save') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
