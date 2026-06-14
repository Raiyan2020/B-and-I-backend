<!-- BEGIN: Footer-->
<footer class="footer footer-static footer-light">
    <p class="clearfix blue-grey lighten-2 mb-0"><span class="float-md-left d-block d-md-inline-block mt-25">COPYRIGHT &copy; 2023 Maskan Emar, Developed With Love By <a
                class="text-bold-800 grey darken-2" href="https://badee.com.sa/" target="_blank">Badee</a></span>
        <span class="float-md-right d-none d-md-block">Hand-crafted & Made with<i
                class="feather icon-heart pink"></i></span>
    </p>
</footer>
<!-- Scroll to Top Button -->
<button class="btn btn-primary btn-icon scroll-top" type="button" style="display: none;">
    <i class="feather icon-arrow-up"></i>
</button>
<!-- END: Footer-->

<!-- BEGIN: Core JavaScript (Loaded Globally)-->
<!-- jQuery from CDN (vendors.min.js also includes jQuery, but CDN is loaded first for compatibility)-->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.1/jquery.min.js"
        integrity="sha512-aVKKRRi/Q/YV+4mjoKBsE4x3H+BkegoM/em46NNlCqNTmUYADjBbeNefNxYV7giUp0VxICtqdrbqU7iVaeZNXA=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<!-- Vendor JS (Core - Contains Bootstrap, Popper, etc.)-->
<script src="{{asset('dashboardAssets/app-assets/vendors/js/vendors.min.js')}}"></script>
<!-- END: Core JavaScript-->

<!-- BEGIN: Theme JS (Core - Loaded Globally)-->
<script src="{{asset('dashboardAssets/app-assets/js/core/app-menu.js')}}"></script>
<script src="{{asset('dashboardAssets/app-assets/js/core/app.js')}}"></script>
<script src="{{asset('dashboardAssets/app-assets/js/scripts/components.js')}}"></script>
<!-- END: Theme JS-->

<!-- SweetAlert2 JS (Global - Used for flash messages)-->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.5/dist/sweetalert2.all.min.js"></script>

<!-- Toastr (validation toasts for .store AJAX forms) -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script>
    window.dashboardValidationTitle = @json(__('dashboard.validation_errors_title'));
    window.dashboardRequiredFieldMessage = @json(__('dashboard.required_field_message'));
    if (typeof toastr !== 'undefined') {
        toastr.options = {
            closeButton: true,
            progressBar: true,
            newestOnTop: true,
            timeOut: 10000,
            extendedTimeOut: 3000,
            positionClass: (document.documentElement.getAttribute('data-textdirection') === 'rtl')
                ? 'toast-top-left'
                : 'toast-top-right',
            preventDuplicates: true,
            showMethod: 'fadeIn',
            hideMethod: 'fadeOut'
        };
    }
</script>
<script src="{{ asset('dashboardAssets/custom/js/html-validation.js') }}"></script>

<!-- Dashboard Core JS (Extracted from inline scripts)-->
<script src="{{ asset('dashboardAssets/custom/js/dashboard-core.js') }}"></script>

<!-- Flash Messages Handler (Global - Handles Laravel flash messages)-->
<script>
    // Pass Laravel flash messages to JavaScript; validation errors are rendered inline.
    window.dashboardErrors = '';
    window.dashboardSuccess = @json(session('success') ?: '');
    window.dashboardError = @json(session('error') ?: '');
    window.dashboardDataTablesSearch = @json(__('dashboard.search') . ':');
    window.dashboardDataTablesLanguage = {
        search: @json(__('dashboard.search') . ':'),
        processing: "<span class='fa-stack fa-lg'><i class='fa fa-spinner fa-spin fa-stack-2x fa-fw'></i></span>",
        lengthMenu: @json(__('dashboard.show') . ' _MENU_ ' . __('dashboard.entries')),
        info: @json(__('dashboard.showing') . ' _START_ ' . __('dashboard.to') . ' _END_ ' . __('dashboard.of') . ' _TOTAL_ ' . __('dashboard.entries')),
        infoEmpty: @json(__('dashboard.showing') . ' 0 ' . __('dashboard.to') . ' 0 ' . __('dashboard.of') . ' 0 ' . __('dashboard.entries')),
        infoFiltered: @json('(' . __('dashboard.filtered from') . ' _MAX_ ' . __('dashboard.total entries') . ')'),
        emptyTable: @json(__('dashboard.no data available in table')),
        zeroRecords: @json(__('dashboard.no matching records found')),
        paginate: {
            first: @json(__('dashboard.first')),
            last: @json(__('dashboard.last')),
            next: @json(__('dashboard.next')),
            previous: @json(__('dashboard.previous')),
        },
        aria: {
            sortAscending: @json(__('dashboard.activate to sort column ascending')),
            sortDescending: @json(__('dashboard.activate to sort column descending')),
        },
    };
    // Backward-compatible aliases used by datatables-shared.js
    window.dashboardDataTablesLengthMenu = window.dashboardDataTablesLanguage.lengthMenu;
    window.dashboardDataTablesInfo = window.dashboardDataTablesLanguage.info;
    window.dashboardDataTablesInfoEmpty = window.dashboardDataTablesLanguage.infoEmpty;
    window.dashboardDataTablesInfoFiltered = window.dashboardDataTablesLanguage.infoFiltered;
    window.dashboardDataTablesEmptyTable = window.dashboardDataTablesLanguage.emptyTable;
    window.dashboardDataTablesZeroRecords = window.dashboardDataTablesLanguage.zeroRecords;
    window.dashboardDataTablesFirst = window.dashboardDataTablesLanguage.paginate.first;
    window.dashboardDataTablesLast = window.dashboardDataTablesLanguage.paginate.last;
    window.dashboardDataTablesNext = window.dashboardDataTablesLanguage.paginate.next;
    window.dashboardDataTablesPrevious = window.dashboardDataTablesLanguage.paginate.previous;
    window.dashboardDataTablesSortAscending = window.dashboardDataTablesLanguage.aria.sortAscending;
    window.dashboardDataTablesSortDescending = window.dashboardDataTablesLanguage.aria.sortDescending;
</script>
<script src="{{ asset('dashboardAssets/custom/js/admin-collapsible-panels.js') }}"></script>

<!-- Form Reset Handler (Global - Used on create forms)-->
<script src="{{ asset('dashboardAssets/custom/js/form-reset.js') }}"></script>

<!-- Submit Add Form Handler (Global - Used across forms)-->
<script src="{{ asset('dashboardAssets/custom/js/submit-add-form.js') }}"></script>

<!-- Image Preview JS (Global - Used for image uploads)-->
<script src="{{ asset('dashboardAssets/app-assets/js/image-preview.js') }}"></script>

<!-- Additional translations -->
<script>
    window.dashboardSendText = @json(__('dashboard.send'));
    window.dashboardAddedSuccessfully = @json(__('dashboard.added_successfully'));

    // Delete row translations
    window.dashboardSwalTitle = @json(__('dashboard.swal_title'));
    window.dashboardSwalText = @json(__('dashboard.swal_text'));
    window.dashboardConfirm = @json(__('dashboard.confirm'));
    window.dashboardCancel = @json(__('dashboard.cancel'));
    window.dashboardItemDeletedSuccessfully = @json(__('dashboard.item deleted successfully'));

    // Bulk delete translations
    window.dashboardBulkDeleteText = @json(__('dashboard.bulk_delete_text', ['default' => 'سيتم حذف العناصر المحددة. لا يمكن التراجع عن هذا الإجراء!']));
    window.dashboardSelectedItems = @json(__('dashboard.selected_items', ['default' => 'عنصر محدد']));
    window.dashboardItemsDeletedSuccessfully = @json(__('dashboard.items_deleted_successfully', ['default' => 'تم الحذف بنجاح']));
    window.dashboardNoSelection = @json(__('dashboard.no_selection', ['default' => 'لا يوجد اختيار']));
    window.dashboardPleaseSelectItems = @json(__('dashboard.please_select_items', ['default' => 'يرجى تحديد عناصر للحذف']));
    window.dashboardDeleting = @json(__('dashboard.deleting', ['default' => 'جاري الحذف...']));
    window.dashboardPleaseWait = @json(__('dashboard.please_wait', ['default' => 'يرجى الانتظار']));

    window.dashboardGenericError = @json(__('dashboard.something_went_wrong'));

    // Toggle block translations
    window.dashboardToggleBlockText = @json(__('dashboard.toggle_block_text', ['default' => 'هل تريد تغيير حالة الحظر لهذا العنصر؟']));
    window.dashboardLoading = @json(__('dashboard.loading', ['default' => 'جاري المعالجة...']));
    window.dashboardItemUpdatedSuccessfully = @json(__('dashboard.item updated successfully', ['default' => 'تم التحديث بنجاح']));
</script>
<script src="{{ asset('dashboardAssets/custom/js/swal-flash.js') }}"></script>

<!-- Table Delete Row Handler (Global - Replaces inline deleteOne.blade.php)-->
<script src="{{ asset('dashboardAssets/custom/js/shared/table-delete-row.js') }}"></script>

<!-- Table Toolkit (Selection + Bulk Delete) - Loaded globally for reuse -->
<script src="{{ asset('dashboardAssets/custom/js/shared/table-selection.js') }}"></script>
<script src="{{ asset('dashboardAssets/custom/js/shared/table-bulk-delete.js') }}"></script>
<script src="{{ asset('dashboardAssets/custom/js/shared/table-toggle-block.js') }}"></script>
<script src="{{ asset('dashboardAssets/custom/js/shared/table-helpers.js') }}"></script>
<script src="{{ asset('dashboardAssets/custom/js/shared/datatables-shared.js') }}"></script>

<!-- Stacks for vendor and page-specific scripts -->
@stack('vendor-scripts')

<!-- DataTables Initializer — MUST load after DataTables vendor JS and translations -->
<script src="{{ asset('dashboardAssets/custom/js/datatable-initializer.js') }}"></script>

@stack('page-scripts')
@yield('script')
@stack('script')
