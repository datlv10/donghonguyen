{if !empty($customer)}
    {assign var = url_list value = "{ADMIN_PATH}/customer"}
    {assign var = url_edit value = "{ADMIN_PATH}/customer/update{if !empty($id)}/{$id}{/if}"}

    <div class="kt-subheader   kt-grid__item" id="kt_subheader">
        <div class="kt-container  kt-container--fluid ">
            <div class="kt-subheader__main">
                <h3 class="kt-subheader__title">
                    {if !empty($title_for_layout)}{$title_for_layout}{/if}
                </h3>
            </div>

            <div class="kt-subheader__toolbar">
                <a href="{$url_list}" class="btn btn-sm btn-default">
                    {__d('admin', 'quay_lai_danh_sach')}
                </a>

                <a href="{$url_edit}" class="btn btn-sm btn-brand">
                    <i class="la la-edit"></i>
                    {__d('admin', 'chinh_sua')}
                </a>
            </div>
        </div>
    </div>


    <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
        <div class="kt-portlet kt-portlet--tabs">
            <div class="kt-portlet__head">
                <div class="kt-portlet__head-label">
                    <h3 class="kt-portlet__head-title">
                        {__d('admin', 'thong_tin_chinh')}
                    </h3>
                </div>
            </div>

            <div class="kt-portlet__body">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group row">
                            <label class="col-xl-3 col-lg-4 col-form-label">
                                {__d('admin', 'ho_va_ten')}
                            </label>

                            <div class="col-lg-8 col-xl-9">
                                <span class="form-control-plaintext kt-font-bolder">
                                    {if !empty($customer.full_name)}
                                        {$customer.full_name}
                                    {/if}
                                </span>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-xl-3 col-lg-4 col-form-label">
                                {__d('admin', 'ma_khach_hang')}
                            </label>

                            <div class="col-lg-8 col-xl-9">
                                <span class="form-control-plaintext kt-font-bolder">
                                    {if !empty($customer.code)}
                                        {$customer.code}
                                    {/if}
                                </span>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-xl-3 col-lg-4 col-form-label">
                                {__d('admin', 'email')}
                            </label>

                            <div class="col-lg-8 col-xl-9">
                                <span class="form-control-plaintext kt-font-bolder">
                                    {if !empty($customer.email)}
                                        {$customer.email}
                                    {/if}
                                </span>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-xl-3 col-lg-4 col-form-label">
                                {__d('admin', 'tai_khoan')}
                            </label>

                            <div class="col-lg-8 col-xl-9">                                
                                {if !empty($customer.username)}
                                    <span class="form-control-plaintext kt-font-bolder">
                                        {$customer.username}
                                    
                                        {if isset($customer.account_status) && $customer.account_status == 0}
                                            <i class="text-danger fs-12">
                                                ({__d('admin', 'ngung_hoat_dong')})
                                            </i>
                                        {/if}

                                        {if isset($customer.account_status) && $customer.account_status == 1}
                                            <i class="text-success fs-12">
                                                ({__d('admin', 'dang_kich_hoat')})
                                            </i>
                                        {/if}

                                        {if isset($customer.account_status) && $customer.account_status == 2}
                                            <i class="text-danger fs-12">
                                                ({__d('admin', 'cho_kich_hoat')})
                                            </i>
                                        {/if}
                                    </span>
                                {else}
                                    <i class="fs-12">
                                        {__d('admin', 'chua_tao')}
                                    </i>
                                {/if}
                                
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="form-group row">
                            <label class="col-xl-3 col-lg-4 col-form-label">
                                {__d('admin', 'gioi_tinh')}
                            </label>

                            <div class="col-lg-8 col-xl-9">
                                <span class="form-control-plaintext kt-font-bolder">
                                    {if !empty($customer.sex) && $customer.sex eq 'male'}
                                        {__d('admin', 'nam')}
                                    {elseif !empty($customer.sex) && $customer.sex eq 'female'}
                                        {__d('admin', 'nu')}
                                    {elseif !empty($customer.sex) && $customer.sex eq 'other'}
                                        {__d('admin', 'khac')}
                                    {/if}
                                </span>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-xl-3 col-lg-4 col-form-label">
                                {__d('admin', 'so_dien_thoai')}
                            </label>

                            <div class="col-lg-8 col-xl-9">
                                <span class="form-control-plaintext kt-font-bolder">
                                    {if !empty($customer.phone)}
                                        {$customer.phone}
                                    {/if}
                                </span>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-xl-3 col-lg-4 col-form-label">
                                {__d('admin', 'dia_chi')}
                            </label>

                            <div class="col-lg-8 col-xl-9">
                                <span class="form-control-plaintext kt-font-bolder">
                                    {if !empty($customer.full_address)}
                                        {$customer.full_address}
                                    {/if}
                                </span>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-xl-3 col-lg-4 col-form-label">
                                {__d('admin', 'ngay_sinh')}
                            </label>

                            <div class="col-lg-8 col-xl-9">
                                <span class="form-control-plaintext kt-font-bolder">
                                    {if !empty($customer.birthday)}
                                        {$customer.birthday}
                                    {/if}
                                </span>
                            </div>
                        </div>   

                    </div>
                </div>
            </div>

            <div class="kt-portlet__head">
                <div class="kt-portlet__head-toolbar">
                    <ul class="nav nav-tabs nav-tabs-space-xl nav-tabs-line nav-tabs-bold nav-tabs-line-3x nav-tabs-line-brand" role="tablist">

                        <li class="nav-item">
                            <a class="nav-link active" data-toggle="tab" href="#dia_chi" role="tab">
                                <i class="fa fa-map-marker-alt"></i> 
                                {__d('admin', 'so_dia_chi')}
                            </a>
                        </li>

                        {if !empty($plugins[{PRODUCT}])}
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" href="#don_hang_cua_toi" role="tab">
                                        <i class="fa fa-file-alt"></i> 
                                        {__d('admin', 'don_hang_cua_toi')}
                                    </a>
                                </li>
                        {/if}
                    
                        {if !empty($customer.note)}
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab" href="#ghi_chu" role="tab">
                                    <i class="fa fa-file-alt"></i> 
                                    {__d('admin', 'ghi_chu')}
                                </a>
                            </li>
                        {/if}

                    </ul>
                </div>
            </div>

            <div class="kt-portlet__body">
                <div class="tab-content">
                                       <div class="tab-pane active" id="dia_chi" role="tabpanel">
                        <div class="kt-form kt-form--label-right">
                            <div class="kt-form__body">
                                <div class="kt-section">
                                    <div class="kt-section__content">

                                        <table class="table">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th style="width: 250px;">
                                                        {__d('admin', 'ten_dia_chi')}
                                                    </th>

                                                    <th>
                                                        {__d('admin', 'so_dien_thoai')}
                                                    </th>

                                                    <th>
                                                        {__d('admin', 'dia_chi')}
                                                    </th>

                                                    <th>
                                                        {__d('admin', 'zip_code')}
                                                    </th>

                                                    <th style="width: 200px;">
                                                        {__d('admin', 'mac_dinh')}
                                                    </th>
                                                </tr>
                                            </thead>

                                            <tbody>
                                                {if !empty($customer.addresses)}
                                                    {foreach from = $customer.addresses item = item}
                                                        <tr>
                                                            <th scope="row">
                                                                {if !empty($item.address_name)}
                                                                    {$item.address_name}
                                                                {/if}
                                                            </th>

                                                            <td>
                                                                {if !empty($item.phone)}
                                                                    {$item.phone}
                                                                {/if}
                                                            </td>

                                                            <td>
                                                                {if !empty($item.full_address)}
                                                                    {$item.full_address}
                                                                {/if}
                                                            </td>

                                                            <td>
                                                                {if !empty($item.zip_code)}
                                                                    {$item.zip_code}
                                                                {/if}
                                                            </td>

                                                            <td>
                                                                <label class="kt-radio kt-radio--tick kt-radio--success kt-radio--disabled">
                                                                    <input class="btn-is-default" disabled type="radio" name="is_default" value="1" {if $item.is_default eq 1}checked{/if} data-id="{$item.id}" data-customer-id="{if !empty($item.customer_id)}{$item.customer_id}{/if}">
                                                                    <span></span>
                                                                </label>
                                                            </td>
                                                        </tr>
                                                    {/foreach}
                                                {/if}
                                            </tbody>                                            
                                        </table>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane" id="don_hang_cua_toi" role="tabpanel">
                        <div class="kt-section">
                            <div class="kt-section__content">
                                <table class="table">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>
                                                {__d('admin', 'ma_don_hang')}
                                            </th>

                                            <th>
                                                {__d('admin', 'ngay_tao')}
                                            </th>

                                            <th>
                                                {__d('admin', 'tong_tien')}
                                            </th>

                                            <th>
                                                {__d('admin', 'ghi_chu')}
                                            </th>

                                            <th>
                                                {__d('admin', 'trang_thai')}
                                            </th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        {foreach from = $orders item = item}
                                            <tr>
                                                <td>
                                                    <a href="{ADMIN_PATH}/order/detail/{$item.id}" title="{__d('admin', 'ma_don_hang')}">
                                                        <b>
                                                            {if !empty($item.code)}
                                                                {$item.code}
                                                            {/if}
                                                        </b>
                                                    </a>
                                                </td>

                                                <td>
                                                    {if !empty($item.created)}
                                                        {date('H:i - d/m/Y', $item.created)}
                                                    {/if}
                                                </td>

                                                <td>
                                                    {if !empty($item.total)}
                                                        {$item.total|number_format:0:".":","}
                                                    {/if}
                                                </td>

                                                <td>
                                                    {if !empty($item.note)}
                                                        {$item.note}
                                                    {/if}
                                                </td>

                                                <td style="width: 200px">
                                                    {if !empty($item.status) && $item eq {NEW_ORDER}}
                                                        <span class="kt-badge  kt-badge--danger kt-badge--inline kt-badge--pill">
                                                            {__d('admin', 'don_hang_moi')}
                                                        </span>
                                                    {/if}

                                                    {if !empty($item.status) && $item.status eq {CONFIRM}}
                                                        <span class="kt-badge kt-badge--danger kt-badge--inline kt-badge--pill">
                                                            {__d('admin', 'xac_nhan')}
                                                        </span> 
                                                    {/if}

                                                    {if !empty($item.status) && $item.status eq {PACKAGE}}
                                                        <span class="kt-badge  kt-badge--brand kt-badge--inline kt-badge--pill">
                                                            {__d('admin', 'dong_goi')}
                                                        </span>
                                                    {/if}

                                                    {if !empty($item.status) && $item.status eq {EXPORT}}
                                                        <span class="kt-badge  kt-badge--brand kt-badge--inline kt-badge--pill">
                                                            {__d('admin', 'xuat_kho')}
                                                        </span>
                                                    {/if}

                                                    {if !empty($item.status) && $item.status eq {DONE}}
                                                        <span class="kt-badge  kt-badge--success kt-badge--inline kt-badge--pill">
                                                            {__d('admin', 'hoan_thanh')}
                                                        </span> 
                                                    {/if}

                                                    {if !empty($item.status) && $item.status eq {CANCEL}}
                                                        <span class="kt-badge  kt-badge--dark kt-badge--inline kt-badge--pill">
                                                            {__d('admin', 'huy')}
                                                        </span> 
                                                    {/if}
                                                </td>
                                            </tr>
                                        {/foreach}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    {if !empty($customer.note)}
                        <div class="tab-pane" id="ghi_chu" role="tabpanel">
                            <div class="kt-form kt-form--label-right">
                                <div class="kt-form__body">
                                    <div class="kt-section">
                                        <div class="kt-section__content">
                                            <table class="table">
                                                <thead class="thead-light">
                                                    <tr>
                                                        <th style="width: 250px;">
                                                            {__d('admin', 'nguoi_them')}
                                                        </th>

                                                        <th>
                                                            {__d('admin', 'noi_dung')}
                                                        </th>

                                                        <th style="width: 200px;">
                                                            {__d('admin', 'ngay_tao')}
                                                        </th>
                                                    </tr>
                                                </thead>

                                                <tbody>
                                                    {foreach from = $customer.note item = item}
                                                        <tr>
                                                            <th scope="row">
                                                                {if !empty($item.created_by)}
                                                                    {$item.created_by}
                                                                {/if}
                                                            </th>

                                                            <td>
                                                                {if !empty($item.comment)}
                                                                    {$item.comment}
                                                                {/if}
                                                            </td>

                                                            <td>
                                                                {if !empty($item.created)}
                                                                    {$this->UtilitiesAdmin->convertIntgerToDateString($item.created)}
                                                                {/if}
                                                            </td>
                                                        </tr>
                                                    {/foreach}
                                               
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    {/if}
                </div>
            </div>
        </div>
    </div>
{else}
    <span class="kt-datatable--error">
        {__d('admin', 'khong_lay_duoc_thong_tin_ban_ghi')}
    </span>
{/if}

