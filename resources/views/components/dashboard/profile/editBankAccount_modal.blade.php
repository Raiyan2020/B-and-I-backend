<div class="modal fade text-left" id="inlineForm" tabindex="-1" role="dialog" aria-labelledby="myModalLabel33" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel33">{{__('dashboard.edit bank-account')}} </h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form  method="POST" id="modal-edit-form">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="form-group">
                        <label for="first-name-icon">{{__('dashboard.bank-name')}}</label>
                        <div class="position-relative has-icon-left">
                            <input type="text" id="modal-bank-name" class="form-control" name="E_bank_name" placeholder="{{__('dashboard.bank-name')}}">
                            <div class="form-control-position">
                                <i class="feather icon-grid"></i>
                            </div>
                        </div>
                        @error('E_bank_name')
                        <span class="text text-danger">{{$message}}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="first-name-icon">{{__('dashboard.bank account')}}</label>
                        <div class="position-relative has-icon-left">
                            <input type="number" id="modal-account-num" class="form-control" name="E_account_number" placeholder="{{__('dashboard.bank account')}}"
                                   pattern="/^-?\d+\.?\d*$/" onKeyPress="if(this.value.length==16) return false;" min="1111111111111111">
                            <div class="form-control-position">
                                <i class="feather icon-grid"></i>
                            </div>
                        </div>
                        @error('E_account_number')
                        <span class="text text-danger">{{$message}}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="contact-info-icon">{{__('dashboard.name on card')}}</label>
                        <div class="position-relative has-icon-left">
                            <input type="text" id="modal-card-name" class="form-control" name="E_name_on_card" placeholder="{{__('dashboard.name on card')}}"/>
                            <div class="form-control-position">
                                <i class="fa fa-pencil"></i>
                            </div>
                        </div>
                        @error('E_name_on_card')
                        <span class="text text-danger">{{$message}}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="password-icon">{{__('dashboard.IBAN')}}</label>
                        <div class="position-relative has-icon-left">
                            <input type="number" id="modal-bank-iban" class="form-control" name="E_IBAN" placeholder="{{__('dashboard.IBAN')}}"
                                   pattern="/^-?\d+\.?\d*$/" onKeyPress="if(this.value.length==29) return false;" min="11111111111111111111111111111">
                            <div class="form-control-position">
                                <i class="fa fa-pencil"></i>
                            </div>
                        </div>
                        @error('E_IBAN')
                        <span class="text text-danger">{{$message}}</span>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" >
                        {{__('dashboard.save')}}</button>
                </div>
            </form>
        </div>
    </div>
</div>
