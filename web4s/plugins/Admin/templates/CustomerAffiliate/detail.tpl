{assign var = url_list value = "{ADMIN_PATH}/customer/affiliate/list"}

<div class="kt-subheader kt-grid__item" id="kt_subheader">
    <div class="kt-container  kt-container--fluid ">
        <div class="kt-subheader__main">
            <h3 class="kt-subheader__title">
                {$title_for_layout}
            </h3>
        </div>

        <div class="kt-subheader__toolbar">
            <a href="{$url_list}" class="btn btn-sm btn-default">
                {__d('admin', 'quay_lai_danh_sach')}
            </a>
        </div>
    </div>
</div>

<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
    <div class="row">
        <div class="col-lg-6 col-md-6 col-sm-6 col-12">
            <div class="kt-portlet kt-portlet--height-fluid">
                <div class="kt-form kt-form--label-right">
                    <div class="kt-portlet__body">
                        <div class="kt-heading kt-heading--sm kt-heading--space-sm mx-1 py-1 mt-0">
                            <span class="kt-font-bolder py-2">
                                {if !empty($customer.full_name)}
                                    {$customer.full_name}
                                {/if}
                            </span>
                        </div>

                        <div class="kt-widget17">
                            <div class="kt-widget17__stats m-0 w-100 text-center">
                                <div class="kt-widget17__items">
                                    <div class="kt-widget17__item cursor-default d-flex align-items-start justify-content-start flex-column shadow-none bg-light rounded p-15">
                                        <span class="kt-widget17__icon mb-5">
                                            <img src="/admin/assets/media/affiliate/order_success.png" class="mr-10" width="48">
                                        </span>

                                        <span class="kt-widget17__subtitle m-0 text-left">
                                            {__d('admin', 'don_thanh_cong')}

                                            <span class="kt-font-bolder d-block fs-20">
                                                {if isset($customer_affiliate.total_order_success)}
                                                    {$customer_affiliate.total_order_success|number_format:0:".":","}
                                                {else}
                                                    0
                                                {/if}
                                                <small class="fs-13 text-lowercase">{__d('admin', 'VND')}</small>
                                            </span>
                                        </span>
                                    </div>

                                    <div class="kt-widget17__item cursor-default d-flex align-items-start justify-content-start flex-column shadow-none bg-light rounded p-15">
                                        <span class="kt-widget17__icon mb-5">
                                            <img src="/admin/assets/media/affiliate/order_faild.png" class="mr-10" width="48">
                                        </span>

                                        <span class="kt-widget17__subtitle m-0 text-left">
                                            {__d('admin', 'don_that_bai')}

                                            <span class="kt-font-bolder d-block fs-20">
                                                {if isset($customer_affiliate.total_order_failed)}
                                                    {$customer_affiliate.total_order_failed|number_format:0:".":","}
                                                {else}
                                                    0
                                                {/if}
                                                <small class="fs-13 text-lowercase">{__d('admin', 'VND')}</small>
                                            </span>
                                        </span>
                                    </div>

                                    <div class="kt-widget17__item cursor-default d-flex align-items-start justify-content-start flex-column shadow-none bg-light rounded p-15">
                                        {if !empty($level_partner_info.image)}
                                            {assign var = url_img value = "{CDN_URL}{$level_partner_info.image}"}
                                        {else}
                                            {assign var = url_img value = "data:image/gif;base64,R0lGODlhAQABAIAAAMLCwgAAACH5BAAAAAAALAAAAAABAAEAAAICRAEAOw=="}
                                        {/if}
                                        <span class="kt-widget17__icon mb-5">
                                            <img src="{$url_img}" class="mr-10" width="48">
                                        </span>

                                        <span class="kt-widget17__subtitle m-0 text-left">
                                            {__d('admin', 'thu_hang')} : 
                                            {if !empty($level_partner_info.name)}
                                                {$level_partner_info.name}
                                            {/if}

                                            <span class="kt-font-bolder d-block fs-20">
                                                {if !empty($level_partner_info.profit)}
                                                    {$level_partner_info.profit} %
                                                {/if}
                                            </span>
                                        </span>
                                    </div>

                                    <div class="kt-widget17__item cursor-default d-flex align-items-start justify-content-start flex-column shadow-none bg-light rounded p-15">
                                        <span class="kt-widget17__icon mb-5">
                                            <img src="/admin/assets/media/affiliate/profit.png" class="mr-10" width="48">
                                        </span>

                                        <span class="kt-widget17__subtitle m-0 text-left">
                                            {__d('admin', 'tong_hoa_hong')}

                                            <span class="kt-font-bolder d-block fs-20">
                                                {if isset($customer_affiliate.total_point)}
                                                    {$customer_affiliate.total_point|number_format:0:".":","}
                                                {else}
                                                    0
                                                {/if}
                                                <small class="fs-13 text-lowercase">{__d('admin', 'diem')}</small>
                                            </span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <div class="col-lg-6 col-md-6 col-sm-6 col-12">
            <div class="kt-portlet kt-portlet--height-fluid">
                <div class="kt-form kt-form--label-right">
                    <div class="kt-portlet__body">
                        <div id="wrap-dashboard-statistic-element"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="kt-portlet kt-portlet--tabs">
        <div class="kt-portlet__head">
            <div class="kt-portlet__head-toolbar">
                <ul class="nav nav-tabs nav-tabs-line nav-tabs-line-primary nav-tabs-line-2x nav-tabs-line-right" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" data-toggle="tab" href="#partner_order" role="tab" aria-selected="false">
                            {__d('admin', 'danh_sach_don_hang')}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" history-point href="#partner_history_point" role="tab" aria-selected="false">
                            {__d('admin', 'thong_tin_thanh_toan')}
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="kt-portlet__body p-0">
            <div class="tab-content">
                <div class="tab-pane active" id="partner_order" role="tabpanel">
                    <div class="kt-portlet__body">
                        {$this->element('../CustomerAffiliate/search_advanced_order')}
                    </div>

                    <div id="list_order_affiliate" class=""></div>
                </div>
                <div class="tab-pane" id="partner_history_point" role="tabpanel">
                    <div class="kt-portlet__body">
                        <h3 class="fw-600 text-dark">
                            {if !empty($customer.full_name)}
                                {$customer.full_name}
                            {/if}
                        </h3>
                        <p class="mb-5">
                            {if !empty($customer.email)}
                                {$customer.email}
                            {/if}
                        </p>
                        <p class="mb-5">
                            {if !empty($customer.phone)}
                                {$customer.phone}
                            {/if}
                        </p>

                        {if !empty($bank_info)}
                            <div class="row mt-20">
                                {foreach from = $bank_info item = item}
                                    <div class="col-12 col-md-3">
                                        <div class="item-address-member mb-15 border rounded p-15">
                                            {if !empty($item.bank_name)}
                                                <div class="kt-font-bold mb-10">
                                                    {__d('template', 'ten_ngan_hang')}: {$item.bank_name}
                                                </div>
                                            {/if}

                                            {if !empty($item.account_holder)}
                                                <div class="mb-5">
                                                    {__d('template', 'chu_tai_khoan')}: {$item.account_holder}
                                                </div>
                                            {/if}

                                            {if !empty($item.bank_branch)}
                                                <div class="mb-5">
                                                    {__d('template', 'chi_nhanh')}: {$item.bank_branch}
                                                 </div>
                                            {/if}

                                            {if !empty($item.account_number)}
                                                <div class="mb-5">
                                                    {__d('template', 'so_tai_khoan')}: {$item.account_number}
                                                </div>
                                            {/if}
                                        </div>
                                    </div>
                                {/foreach}
                            </div>
                        {else}
                            <div>
                                {__d('template', 'hien_chua_co_ngan_hang_nao_duoc_lien_ket')}
                            </div>
                        {/if}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <input type="hidden" id="customer_id" value="{if !empty($customer.id)}{$customer.id}{/if}">
</div>