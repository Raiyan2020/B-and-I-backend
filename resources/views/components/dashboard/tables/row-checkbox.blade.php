{{-- Row Checkbox for Table Body --}}
@props(['id'])

<td class="text-center">
    <input type="checkbox" class="dt-select-row" data-id="{{ $id }}" title="{{ __('dashboard.select') }}">
</td>
