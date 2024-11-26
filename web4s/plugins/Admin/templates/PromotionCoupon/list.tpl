{assign var = permissions value = $this->RoleAdmin->getPermissionAllRouter()}
{assign var = link_add_coupon value = "{ADMIN_PATH}/promotion/coupon/add-coupon"}
{assign var = access_add_coupon value = "{if !empty($permissions[$link_add_coupon])}1{/if}"}

<div class="kt-subheader kt-grid__item" id="kt_subheader">
    <div class="kt-container  kt-container--fluid ">
        <div class="kt-subheader__main">
            <h3 class="kt-subheader__title">
                {$title_for_layout}
            </h3>
        </div>
        <div class="kt-subheader__toolbar">
            {if !empty($access_add_coupon)}
                <span data-toggle="modal" data-target="#modal-add-coupon-handmade" class="kt-nav__link btn btn-sm btn-brand">
                    <i class="la la-plus"></i>
                    {__d('admin', 'them_ma_thu_cong')}
                </span>
                <span data-toggle="modal" data-toggle="modal" data-target="#modal-add-coupon-random" class="kt-nav__link btn btn-sm btn-brand">
                    <i class="la la-plus"></i>
                    {__d('admin', 'them_ma_ngau_nhien')}
                </span>
            {/if}
        </div>
    </div>
</div>

<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
    <div class="kt-portlet nh-portlet">
        <div class="kt-portlet__body">
            {$this->element('../PromotionCoupon/search_advanced', ['promotion' => $promotion])}

            <div id="nh-group-action" class="kt-form kt-form--label-align-right kt-margin-t-20 collapse">
            	<div class="kt-separator kt-separator--space-lg kt-separator--border-dotted mt-0 mb-20"></div>
                <div class="row align-items-center">
                    <div class="col-xl-12">
                        <div class="kt-form__group kt-form__group--inline">
                            <div class="kt-form__label kt-form__label-no-wrap">
                                <label class="kt-font-bold kt-font-danger-">
                                    {__d('admin', 'da_chon')}
                                    <span id="nh-selected-number">0</span> :
                                </label>
                            </div>

                            <div class="kt-form__control">
                                <div class="btn-toolbar">
                                    <div class="dropdown mr-10">
                                        <button type="button" class="btn btn-sm btn-label-primary dropdown-toggle mobile-mb-5" data-toggle="dropdown">
                                            {__d('admin', 'thay_doi_trang_thai')}
                                        </button>
                                        <div class="dropdown-menu">
                                            {foreach from = $this->ListConstantAdmin->listStatus() key = k_status item = status}
                                                <a class="dropdown-item nh-change-status-all" data-status="{$k_status}" href="javascript:;">
                                                    {$status}
                                                </a>
                                            {/foreach}
                                        </div>
                                    </div>
                                  
                                    <button class="btn btn-sm btn-label-danger nh-delete-all mobile-mb-5" type="button">
                                        <i class="la la-trash-o"></i>
                                        {__d('admin', 'xoa_tat_ca')}
                                    </button>                                  
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="kt-portlet__body kt-portlet__body--fit">
            <div class="kt-datatable"></div>
        </div>
        <input type="hidden" name="promotion_id" value="{if !empty($promotion.id)}{$promotion.id}{/if}">
    </div>
</div>

{$this->element('Admin.page/popover_quick_change')}
{$this->element("../PromotionCoupon/modal_them_ma_thu_cong")}
{$this->element("../PromotionCoupon/modal_them_ma_ngau_nhien")}