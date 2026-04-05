<div class="modal fade text-left" id="chargeWalletModal" tabindex="-1" role="dialog" aria-labelledby="chargeWalletModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success white">
                <h4 class="modal-title" id="chargeWalletModalLabel">{{ __('dashboard.charge_wallet') }}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST" id="charge-wallet-form" action="{{ route('admin.users.chargeWallet', $userId) }}">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info" id="current-balance-alert">
                        <i class="feather icon-info mr-1"></i>
                        {{ __('dashboard.current_balance') }}: 
                        <strong id="current-balance-value">{{ $currentBalance ?? 0 }} {{ __('dashboard.currency') }}</strong>
                    </div>
                    <div class="form-group">
                        <label for="charge_amount">{{ __('dashboard.amount') }} <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" class="form-control" id="charge_amount" name="amount" 
                                   placeholder="{{ __('dashboard.enter_amount') }}" 
                                   min="0.01" step="0.01" required>
                            <div class="input-group-append">
                                <span class="input-group-text">{{ __('dashboard.currency') }}</span>
                            </div>
                        </div>
                        <small class="text-muted">{{ __('dashboard.minimum_charge_amount') }}</small>
                    </div>
                    <div class="form-group">
                        <label for="charge_description">{{ __('dashboard.description') }}</label>
                        <textarea class="form-control" id="charge_description" name="description" rows="3" 
                                  placeholder="{{ __('dashboard.enter_description_optional') }}"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('dashboard.cancel') }}</button>
                    <button type="submit" class="btn btn-success" id="charge-submit-btn">
                        <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                        {{ __('dashboard.charge') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
