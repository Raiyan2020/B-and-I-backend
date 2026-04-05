<style>
    /* ===== Modern DataTable Styles ===== */
    .table-responsive {
        border-radius: 8px;
        overflow: hidden;
    }

    .table-striped,
    .table {
        width: 100% !important;
        border-collapse: separate;
        border-spacing: 0;
        margin-bottom: 0;
        background: #fff;
        border-radius: 8px;
        overflow: hidden;
    }

    .table thead {
        background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
        border-bottom: 2px solid #e7e7e7;
    }

    .table thead th {
        padding: 1rem;
        font-weight: 600;
        color: #5e5873;
        text-align: center;
        border-bottom: 2px solid #e7e7e7;
        white-space: nowrap;
        position: relative;
        font-size: 0.875rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .table thead th:first-child {
        border-top-left-radius: 8px;
    }

    .table thead th:last-child {
        border-top-right-radius: 8px;
    }

    .table tbody td {
        padding: 1rem;
        vertical-align: middle;
        border-bottom: 1px solid #f3f3f3;
        color: #5e5873;
        text-align: center;
        transition: background-color 0.2s ease;
        font-size: 0.9rem;
    }

    .table tbody tr {
        transition: all 0.2s ease;
    }

    .table-striped tbody tr:nth-of-type(odd) {
        background-color: #fafafa;
    }

    .table tbody tr:hover {
        background-color: #f8f9ff !important;
        transform: scale(1.001);
        box-shadow: 0 2px 4px rgba(156, 136, 255, 0.1);
    }

    .table tbody tr:last-child td {
        border-bottom: none;
    }

    /* DataTables Wrapper */
    .dataTables_wrapper {
        margin-top: 1rem;
    }

    /* DataTables Processing */
    .dataTables_processing {
        background: rgba(255, 255, 255, 0.95) !important;
        border: 1px solid #e7e7e7 !important;
        border-radius: 8px !important;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1) !important;
        padding: 1.5rem !important;
        font-weight: 600 !important;
        color: #9C88FF !important;
        top: 50% !important;
        left: 50% !important;
        transform: translate(-50%, -50%) !important;
        z-index: 1000 !important;
    }

    /* DataTables Length */
    .dataTables_wrapper .dataTables_length {
        margin-bottom: 1rem;
    }

    .dataTables_wrapper .dataTables_length label {
        font-weight: 500;
        color: #5e5873;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 0;
    }

    .dataTables_wrapper .dataTables_length select {
        border-radius: 6px;
        border: 1px solid #d8d6de;
        padding: 0.375rem 1.75rem 0.375rem 0.75rem;
        margin: 0 0.5rem;
        background-color: #fff;
        color: #5e5873;
        transition: all 0.3s ease;
        font-size: 0.875rem;
    }

    .dataTables_wrapper .dataTables_length select:focus {
        border-color: #9C88FF;
        box-shadow: 0 0 0 0.2rem rgba(156, 136, 255, 0.25);
        outline: none;
    }

    /* DataTables Filter/Search */
    .dataTables_wrapper .dataTables_filter {
        margin-bottom: 1rem;
        text-align: left;
    }

    .dataTables_wrapper .dataTables_filter label {
        font-weight: 500;
        color: #5e5873;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 0;
    }

    .dataTables_wrapper .dataTables_filter input {
        border-radius: 6px;
        border: 1px solid #d8d6de;
        padding: 0.5rem 1rem;
        margin-left: 0.5rem;
        transition: all 0.3s ease;
        font-size: 0.875rem;
        width: 250px;
    }

    .dataTables_wrapper .dataTables_filter input:focus {
        border-color: #9C88FF;
        box-shadow: 0 0 0 0.2rem rgba(156, 136, 255, 0.25);
        outline: none;
        width: 300px;
    }

    /* DataTables Info */
    .dataTables_wrapper .dataTables_info {
        padding-top: 1rem;
        color: #5e5873;
        font-weight: 500;
        font-size: 0.875rem;
    }

    /* DataTables Pagination */
    .dataTables_wrapper .dataTables_paginate {
        margin-top: 1.5rem;
        padding-top: 1rem;
        border-top: 1px solid #e7e7e7;
        text-align: center;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button {
        padding: 0.5rem 0.75rem !important;
        margin: 0 0.25rem !important;
        border-radius: 6px !important;
        border: 1px solid #d8d6de !important;
        background: #fff !important;
        color: #5e5873 !important;
        transition: all 0.3s ease !important;
        font-weight: 500 !important;
        font-size: 0.875rem !important;
        cursor: pointer !important;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
        background: #9C88FF !important;
        border-color: #9C88FF !important;
        color: #fff !important;
        transform: translateY(-2px);
        box-shadow: 0 2px 6px rgba(156, 136, 255, 0.3) !important;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        background: #9C88FF !important;
        border-color: #9C88FF !important;
        color: #fff !important;
        font-weight: 600 !important;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
        background: #8B7EC8 !important;
        border-color: #8B7EC8 !important;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button.disabled {
        opacity: 0.5;
        cursor: not-allowed !important;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button.disabled:hover {
        background: #fff !important;
        border-color: #d8d6de !important;
        color: #5e5873 !important;
        transform: none !important;
        box-shadow: none !important;
    }

    /* Table Images */
    .table-image {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #e7e7e7;
        transition: all 0.3s ease;
    }

    .table-image:hover {
        border-color: #9C88FF;
        transform: scale(1.1);
        box-shadow: 0 2px 8px rgba(156, 136, 255, 0.3);
    }

    .table-image-placeholder {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        background: linear-gradient(135deg, #f0f0f0 0%, #e0e0e0 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto;
        color: #9C88FF;
        font-size: 1.2rem;
        transition: all 0.3s ease;
    }

    .table-image-placeholder:hover {
        background: linear-gradient(135deg, #9C88FF 0%, #8B7EC8 100%);
        color: #fff;
        transform: scale(1.1);
    }

    /* Badge Styles in Tables */
    .table .badge {
        padding: 0.4rem 0.7rem;
        font-weight: 500;
        font-size: 0.75rem;
        border-radius: 6px;
        display: inline-block;
    }

    /* Action Buttons in Tables */
    .table .btn {
        border-radius: 6px;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .table .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    }

    /* Dark Mode */
    body.dark-layout .table {
        background: #2b3553 !important;
    }

    body.dark-layout .table thead {
        background: linear-gradient(135deg, #323a5a 0%, #2b3553 100%) !important;
        border-bottom-color: #414561 !important;
    }

    body.dark-layout .table thead th {
        color: #ebeefd !important;
        border-bottom-color: #414561 !important;
    }

    body.dark-layout .table tbody td {
        color: #c2c6dc !important;
        border-bottom-color: #414561 !important;
    }

    body.dark-layout .table-striped tbody tr:nth-of-type(odd) {
        background-color: #323a5a !important;
    }

    body.dark-layout .table tbody tr:hover {
        background-color: #3a4260 !important;
    }

    body.dark-layout .dataTables_wrapper .dataTables_info {
        color: #c2c6dc !important;
    }

    body.dark-layout .dataTables_wrapper .dataTables_length label,
    body.dark-layout .dataTables_wrapper .dataTables_filter label {
        color: #ebeefd !important;
    }

    body.dark-layout .dataTables_wrapper .dataTables_length select,
    body.dark-layout .dataTables_wrapper .dataTables_filter input {
        background: #323a5a !important;
        border-color: #414561 !important;
        color: #ebeefd !important;
    }

    body.dark-layout .dataTables_wrapper .dataTables_length select:focus,
    body.dark-layout .dataTables_wrapper .dataTables_filter input:focus {
        border-color: #9C88FF !important;
        background: #3a4260 !important;
    }

    body.dark-layout .dataTables_wrapper .dataTables_paginate .paginate_button {
        background: #2b3553 !important;
        border-color: #414561 !important;
        color: #c2c6dc !important;
    }

    body.dark-layout .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
        background: #9C88FF !important;
        color: #fff !important;
    }

    body.dark-layout .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        background: #9C88FF !important;
        color: #fff !important;
    }

    body.dark-layout .dataTables_processing {
        background: rgba(43, 53, 83, 0.95) !important;
        border-color: #414561 !important;
        color: #9C88FF !important;
    }

    body.dark-layout .table-image-placeholder {
        background: linear-gradient(135deg, #323a5a 0%, #2b3553 100%) !important;
    }

    /* DataTables Top Bar */
    .dataTables_wrapper .row:first-child {
        margin-bottom: 1rem;
        padding: 0 1.5rem;
    }

    .dataTables_wrapper .row:last-child {
        margin-top: 1rem;
        padding: 0 1.5rem;
    }

    /* Table Actions Dropdown */
    .table .dropdown-toggle {
        border-radius: 6px;
        transition: all 0.3s ease;
    }

    .table .dropdown-menu {
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        border: 1px solid #e7e7e7;
        padding: 0.5rem;
    }

    .table .dropdown-item {
        border-radius: 6px;
        padding: 0.5rem 0.75rem;
        transition: all 0.2s ease;
        font-size: 0.875rem;
    }

    .table .dropdown-item:hover {
        background: #f8f9ff;
        color: #9C88FF;
        transform: translateX(5px);
    }

    /* Responsive */
    @media (max-width: 768px) {
        .table thead th,
        .table tbody td {
            padding: 0.75rem 0.5rem;
            font-size: 0.8rem;
        }

        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_filter {
            margin-bottom: 0.75rem;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }

        .dataTables_wrapper .dataTables_length label,
        .dataTables_wrapper .dataTables_filter label {
            flex-direction: column;
            align-items: flex-start;
            width: 100%;
        }

        .dataTables_wrapper .dataTables_filter input {
            width: 100% !important;
            margin-left: 0 !important;
            margin-top: 0.5rem;
        }

        .dataTables_wrapper .dataTables_length select {
            width: 100%;
            margin: 0.5rem 0 0 0;
        }

        .dataTables_wrapper .dataTables_info,
        .dataTables_wrapper .dataTables_paginate {
            text-align: center;
            width: 100%;
        }

        .table-image,
        .table-image-placeholder {
            width: 35px;
            height: 35px;
            font-size: 1rem;
        }
    }

    /* DataTables Top Bar */
    .dataTables_wrapper .row:first-child {
        margin-bottom: 1rem;
        padding: 0 1.5rem;
    }

    .dataTables_wrapper .row:last-child {
        margin-top: 1rem;
        padding: 0 1.5rem;
    }

    /* Table Actions Dropdown */
    .table .dropdown-toggle {
        border-radius: 6px;
        transition: all 0.3s ease;
    }

    .table .dropdown-menu {
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        border: 1px solid #e7e7e7;
        padding: 0.5rem;
        margin-top: 0.5rem;
    }

    .table .dropdown-item {
        border-radius: 6px;
        padding: 0.5rem 0.75rem;
        transition: all 0.2s ease;
        font-size: 0.875rem;
        color: #5e5873;
    }

    .table .dropdown-item:hover {
        background: #f8f9ff;
        color: #9C88FF;
        transform: translateX(5px);
    }

    .table .dropdown-item i {
        margin-right: 0.5rem;
    }

    body.dark-layout .table .dropdown-menu {
        background: #2b3553 !important;
        border-color: #414561 !important;
    }

    body.dark-layout .table .dropdown-item {
        color: #c2c6dc !important;
    }

    body.dark-layout .table .dropdown-item:hover {
        background: #323a5a !important;
        color: #9C88FF !important;
    }

    body.dark-layout .table-image-placeholder {
        background: linear-gradient(135deg, #323a5a 0%, #2b3553 100%) !important;
    }
</style>
