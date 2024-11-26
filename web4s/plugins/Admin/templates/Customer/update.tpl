{assign var = url_list value = "{ADMIN_PATH}/customer"}
{assign var = url_add value = "{ADMIN_PATH}/customer/add"}
{assign var = url_edit value = "{ADMIN_PATH}/customer/update"}

{$this->element('Admin.page/content_head', [
    'url_list' => $url_list,
    'url_add' => $url_add,
    'url_edit' => $url_edit
])}

<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
    <form id="main-form" action="{ADMIN_PATH}/customer/save{if !empty($id)}/{$id}{/if}" method="POST" autocomplete="off">
        <div class="kt-portlet nh-portlet">
            <div class="kt-portlet__head">
                <div class="kt-portlet__head-label">
                    <h3 class="kt-portlet__head-title">
                        {__d('admin', 'thong_tin_chinh')}
                    </h3>
                </div>
            </div>
            
            <div class="kt-portlet__body">
                <div class="form-group row">
                    <label class="col-lg-2 col-xl-2 col-form-label">
                        {__d('admin', 'ho_va_ten')}
                        <span class="kt-font-danger">*</span>
                    </label>

                    <div class="col-lg-10 col-xl-5">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="fa fa-user-tie"></i>
                                </span>
                            </div>
                            <input name="full_name" value="{if !empty($customer.full_name)}{htmlentities($customer.full_name)}{/if}" class="form-control form-control-sm" type="text" maxlength="255">
                        </div>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-lg-2 col-xl-2 col-form-label">
                        {__d('admin', 'email')}
                    </label>

                    <div class="col-lg-10 col-xl-5">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="fa fa-envelope"></i>
                                </span>
                            </div>

                            <input name="email" value="{if !empty($customer.email)}{htmlentities($customer.email)}{/if}" type="text" class="form-control form-control-sm" maxlength="255">
                        </div>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-lg-2 col-xl-2 col-form-label">
                        {__d('admin', 'so_dien_thoai')}
                    </label>

                    <div class="col-lg-10 col-xl-5">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="fa fa-phone"></i>
                                </span>
                            </div>

                            <input name="phone" value="{if !empty($customer.phone)}{htmlentities($customer.phone)}{/if}" type="text" class="form-control form-control-sm" maxlength="255">
                        </div>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-lg-2 col-xl-2 col-form-label">
                        {__d('admin', 'ma_khach_hang')}
                    </label>

                    <div class="col-lg-10 col-xl-5">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="fa fa-qrcode"></i>
                                </span>
                            </div>

                            <input name="code" value="{if !empty($customer.code)}{htmlentities($customer.code)}{/if}" class="form-control form-control-sm" type="text">
                        </div>

                        <span class="form-text text-muted">
                            {__d('admin', 'neu_khong_nhap_he_thong_se_tu_dong_sinh_ra_ma_khach_hang')}
                        </span>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-lg-2 col-xl-2 col-form-label">
                        {__d('admin', 'ngay_sinh')}
                    </label>

                    <div class="col-lg-10 col-xl-5">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="fa fa-birthday-cake"></i>
                                </span>
                            </div>

                            <input name="birthday" value="{if !empty($customer.birthday)}{htmlentities($customer.birthday)}{/if}" class="form-control form-control-sm" type="text">
                        </div>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-lg-2 col-xl-2 col-form-label">
                        {__d('admin', 'gioi_tinh')}
                    </label>

                    <div class="col-lg-10 col-xl-5">
                        <div class="kt-radio-inline mt-5">
                            <label class="kt-radio kt-radio--tick kt-radio--success">
                                <input type="radio" name="sex" value="male" {if !empty($customer.sex) && $customer.sex == 'male'}checked{/if}> {__d('admin', 'nam')}
                                <span></span>
                            </label>

                            <label class="kt-radio kt-radio--tick kt-radio--success mr-20">
                                <input type="radio" name="sex" value="female" {if !empty($customer.sex) && $customer.sex == 'female'}checked{/if}> {__d('admin', 'nu')}
                                <span></span>
                            </label>

                            <label class="kt-radio kt-radio--tick kt-radio--success">
                                <input type="radio" name="sex" value="other" {if !empty($customer.sex) && $customer.sex == 'other'}checked{/if}> {__d('admin', 'khac')}
                                <span></span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="kt-portlet__head">
                <div class="kt-portlet__head-label">
                    <h3 class="kt-portlet__head-title">
                        {__d('admin', 'dia_chi')}
                    </h3>
                </div>
            </div>
            <div class="kt-portlet__body">
                <div class="form-group">
                    <a href="javascript:;" class="btn btn-sm btn-brand btn-add-customer-address" data-address-id="{if !empty($id)}{$id}{/if}" data-toggle="modal" data-target="#add-address-modal">
                        <i class="la la-plus"></i> 
                        {__d('admin', 'them_moi_dia_chi')}
                    </a>
                </div>

                <div class="kt-section">
                    <div class="kt-section__content">
                        <table class="table">
                            <thead class="thead-light">
                                <tr>
                                    <th style="width: 250px;">
                                        {__d('admin', 'ten_dia_chi')}
                                    </th>
                                    <th>{__d('admin', 'so_dien_thoai')}</th>
                                    <th>{__d('admin', 'dia_chi')}</th>
                                    <th>{__d('admin', 'zip_code')}</th>
                                    <th>{__d('admin', 'mac_dinh')}</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                {if !empty($customer.addresses)}
                                    {foreach from = $customer.addresses item = item}
                                        <tr>
                                            <td scope="row">
                                                {if !empty($item.address_name)}
                                                    {$item.address_name}
                                                {/if}
                                            </td>

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
                                                <label class="kt-radio kt-radio--tick kt-radio--success">
                                                    <input class="btn-is-default" type="radio" name="is_default" value="1" {if $item.is_default eq 1}checked{/if} data-id="{$item.id}" data-customer-id="{if !empty($item.customer_id)}{$item.customer_id}{/if}">
                                                    <span></span>
                                                </label>
                                            </td>

                                            <td style="text-align: right;">
                                                <a href="javascript:;" class="btn-sm btn btn-label-brand btn-bold btn-edit-customer-address" data-toggle="modal" data-target="#edit-address-modal" data-address="{htmlentities($item|@json_encode)}">
                                                    <i class="la la-edit"></i>
                                                    {__d('admin', 'sua')}
                                                </a>
                                                <a href="javascript:;" class="btn-sm btn btn-label-danger btn-bold btn-delete-customer-address" data-id="{if !empty($item.id)}{$item.id}{/if}">
                                                    <i class="la la-trash-o"></i>
                                                    {__d('admin', 'xoa')}
                                                </a>
                                            </td>
                                        </tr>
                                    {/foreach}
                                {/if}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="kt-portlet__head">
                <div class="kt-portlet__head-label">
                    <h3 class="kt-portlet__head-title">
                        {__d('admin', 'ghi_chu')}
                    </h3>
                </div>
            </div>

            <div class="kt-portlet__body">
                <div class="form-group">
                    <a href="javascript:;" class="btn btn-sm btn-brand" data-toggle="modal" data-target="#add_note">
                        <i class="la la-plus"></i>
                        {__d('admin', 'them_moi_ghi_chu')}
                    </a>
                </div>

                {if !empty($customer.note)}
                    <div class="kt-section">
                        <div class="kt-section__content">
                            <table class="table">
                                <thead class="thead-light">
                                    <tr>
                                        <th style="width: 250px;">{__d('admin', 'nguoi_them')}</th>
                                        <th>{__d('admin', 'noi_dung')}</th>
                                        <th>{__d('admin', 'ngay_tao')}</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {foreach from = $customer.note key=key item=item}
                                        <tr>
                                            <th scope="row">{if !empty($item.created_by)}{$item.created_by}{/if}</th>
                                            <td>{if !empty($item.comment)}{$item.comment}{/if}</td>
                                            <td>{if !empty($item.created)}{$this->UtilitiesAdmin->convertIntgerToDateString($item.created)}{/if}</td>
                                            <td style="text-align: right;">
                                                <a class="btn-sm btn btn-label-danger btn-bold btn-delete-customer-note" data-id="{if !empty($id)}{$id}{/if}" data-index="{$key}">
                                                    <i class="la la-trash-o"></i>
                                                    {__d('admin', 'xoa')}
                                                </a>
                                            </td>
                                        </tr>
                                    {/foreach}
                               
                                </tbody>
                            </table>
                        </div>
                    </div>
                {/if}
            </div>
        </div>
    </form>
</div>

{$this->element('../Customer/add_note_modal')}
{$this->element('Admin.page/modal_add_address')}