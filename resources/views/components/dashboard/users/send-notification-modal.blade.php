<div class="modal fade text-left" id="sendNotificationModal" tabindex="-1" role="dialog" aria-labelledby="sendNotificationModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary white">
                <h4 class="modal-title" id="sendNotificationModalLabel">{{ __('dashboard.send_notification') }}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST" id="send-notification-form" action="{{ route('admin.users.sendNotification', $userId) }}">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="notification_title_ar">{{ __('dashboard.title_ar') }} <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="notification_title_ar" name="title_ar" 
                               placeholder="{{ __('dashboard.enter_title_ar') }}" required>
                    </div>
                    <div class="form-group">
                        <label for="notification_title_en">{{ __('dashboard.title_en') }} <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="notification_title_en" name="title_en" 
                               placeholder="{{ __('dashboard.enter_title_en') }}" required>
                    </div>
                    <div class="form-group">
                        <label for="notification_body_ar">{{ __('dashboard.body_ar') }} <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="notification_body_ar" name="body_ar" rows="4" 
                                  placeholder="{{ __('dashboard.enter_body_ar') }}" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="notification_body_en">{{ __('dashboard.body_en') }} <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="notification_body_en" name="body_en" rows="4" 
                                  placeholder="{{ __('dashboard.enter_body_en') }}" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('dashboard.cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('dashboard.send') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
