<div class="kt-heading kt-heading--sm kt-heading--space-sm mx-1 mt-0">
    <div class="d-flex align-items-center justify-content-between">
        <span class="kt-font-bolder text-dark">
            {__d('admin', 'du_lieu_thang')}
        </span>
        <div class="dropdown show">
            <a class="btn btn-sm btn-secondary dropdown-toggle" href="javascript:;" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                {if !empty($filter_date)}
                    {__d('admin', {$filter_date})}
                {else}
                    {$smarty.now|date_format:"%m"}
                {/if}
            </a>

            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuLink">
                <a filter-date="thang_1" class="dropdown-item" href="javascript:;">{__d('admin', 'thang_1')}</a>
                <a filter-date="thang_2" class="dropdown-item" href="javascript:;">{__d('admin', 'thang_2')}</a>
                <a filter-date="thang_3" class="dropdown-item" href="javascript:;">{__d('admin', 'thang_3')}</a>
                <a filter-date="thang_4" class="dropdown-item" href="javascript:;">{__d('admin', 'thang_4')}</a>
                <a filter-date="thang_5" class="dropdown-item" href="javascript:;">{__d('admin', 'thang_5')}</a>
                <a filter-date="thang_6" class="dropdown-item" href="javascript:;">{__d('admin', 'thang_6')}</a>
                <a filter-date="thang_7" class="dropdown-item" href="javascript:;">{__d('admin', 'thang_7')}</a>
                <a filter-date="thang_8" class="dropdown-item" href="javascript:;">{__d('admin', 'thang_8')}</a>
                <a filter-date="thang_9" class="dropdown-item" href="javascript:;">{__d('admin', 'thang_9')}</a>
                <a filter-date="thang_10" class="dropdown-item" href="javascript:;">{__d('admin', 'thang_10')}</a>
                <a filter-date="thang_11" class="dropdown-item" href="javascript:;">{__d('admin', 'thang_11')}</a>
                <a filter-date="thang_12" class="dropdown-item" href="javascript:;">{__d('admin', 'thang_12')}</a>
                <a filter-date="year" class="dropdown-item" href="javascript:;">{__d('admin', 'trong_nam')}</a>
            </div>
        </div>
    </div>
</div>

<div class="kt-widget17">
    <div class="kt-widget17__stats m-0 w-100 text-center">
        <div class="kt-widget17__items">
            <div class="kt-widget17__item cursor-default d-flex align-items-start justify-content-start flex-column shadow-none bg-light rounded p-15">
                <span class="kt-widget17__icon mb-5">
                    <img src="/admin/assets/media/affiliate/number_order.png" class="mr-10" width="48">
                </span>

                <span class="kt-widget17__subtitle m-0 text-left">
                    {__d('admin', 'tong_don')}

                    <span class="kt-font-bolder d-block fs-20">
                        {if isset($affiliate.total_order)}
                            {$affiliate.total_order|number_format:0:".":","}
                        {else}
                            0
                        {/if}
                        <small class="fs-13 text-lowercase">{__d('admin', 'don_hang')}</small>
                    </span>
                </span>
            </div>

            <div class="kt-widget17__item cursor-default d-flex align-items-start justify-content-start flex-column shadow-none bg-light rounded p-15">
                <span class="kt-widget17__icon mb-5">
                    <img src="/admin/assets/media/affiliate/order_faild.png" class="mr-10" width="48">
                </span>

                <span class="kt-widget17__subtitle m-0 text-left">
                    {__d('admin', 'don_hang_that_bai')}

                    <span class="kt-font-bolder d-block fs-20">
                        {if isset($affiliate.failed_order)}
                            {$affiliate.failed_order|number_format:0:".":","}
                        {else}
                            0
                        {/if}
                        <small class="fs-13 text-lowercase">{__d('admin', 'don_hang')}</small>
                    </span>
                </span>
            </div>

            <div class="kt-widget17__item cursor-default d-flex align-items-start justify-content-start flex-column shadow-none bg-light rounded p-15">
                <span class="kt-widget17__icon mb-5">
                    <img src="/admin/assets/media/affiliate/profit.png" class="mr-10" width="48">
                </span>

                <span class="kt-widget17__subtitle m-0 text-left">
                    {__d('admin', 'hoa_hong')}

                    <span class="kt-font-bolder d-block fs-20">
                        {if isset($affiliate.profit_success_point)}
                            {$affiliate.profit_success_point|number_format:0:".":","}
                        {else}
                            0
                        {/if}
                        <small class="fs-13 text-lowercase">{__d('admin', 'diem')}</small>
                    </span>
                </span>
            </div>

            <div class="kt-widget17__item cursor-default d-flex align-items-start justify-content-start flex-column shadow-none bg-light rounded p-15">
                <span class="kt-widget17__icon mb-5">
                    <img src="/admin/assets/media/affiliate/profit_temporary.png" class="mr-10" width="48">
                </span>

                <span class="kt-widget17__subtitle m-0 text-left">
                    {__d('admin', 'tam_tinh')}

                    <span class="kt-font-bolder d-block fs-20">
                        {if isset($affiliate.profit_success_point_to_money)}
                            {$affiliate.profit_success_point_to_money|number_format:0:".":","}
                        {else}
                            0
                        {/if}
                        <small class="fs-13 text-lowercase">{__d('admin', 'VND')}</small>
                    </span>
                </span>
            </div>
        </div>
    </div>
</div>