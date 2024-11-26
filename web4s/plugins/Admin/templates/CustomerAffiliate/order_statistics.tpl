<div class="kt-portlet__head kt-portlet__head--noborder kt-portlet__space-x">
    <div class="kt-portlet__head-label">
        <a href="{ADMIN_PATH}/customer/affiliate/order" class="kt-portlet__head-title">
            {__d('admin', 'don_hang')}
        </a>
    </div>

    <div class="kt-portlet__head-toolbar">
        <a href="javascript:;" class="btn btn-label-light btn-sm btn-bold dropdown-toggle" data-toggle="dropdown">
            {if !empty($filter_date)}
                {__d('admin', {$filter_date})}
            {else}
                {$smarty.now|date_format:"%m"}
            {/if}
        </a>

        <div class="dropdown-menu dropdown-menu-fit dropdown-menu-right">
            <ul class="kt-nav p-0">
                <li class="kt-nav__item">
                    <a filter-date="thang_1" class="kt-nav__link" href="javascript:;">
                        <span class="kt-nav__link-text">
                            {__d('admin', 'thang_1')}
                        </span>
                    </a>
                </li>
                <li class="kt-nav__item">
                    <a filter-date="thang_2" class="kt-nav__link" href="javascript:;">
                        <span class="kt-nav__link-text">
                            {__d('admin', 'thang_2')}
                        </span>
                    </a>
                </li>
                <li class="kt-nav__item">
                    <a filter-date="thang_3" class="kt-nav__link" href="javascript:;">
                        <span class="kt-nav__link-text">
                            {__d('admin', 'thang_3')}
                        </span>
                    </a>
                </li>
                <li class="kt-nav__item">
                    <a filter-date="thang_4" class="kt-nav__link" href="javascript:;">
                        <span class="kt-nav__link-text">
                            {__d('admin', 'thang_4')}
                        </span>
                    </a>
                </li>
                <li class="kt-nav__item">
                    <a filter-date="thang_5" class="kt-nav__link" href="javascript:;">
                        <span class="kt-nav__link-text">
                            {__d('admin', 'thang_5')}
                        </span>
                    </a>
                </li>
                <li class="kt-nav__item">
                    <a filter-date="thang_6" class="kt-nav__link" href="javascript:;">
                        <span class="kt-nav__link-text">
                            {__d('admin', 'thang_6')}
                        </span>
                    </a>
                </li>
                <li class="kt-nav__item">
                    <a filter-date="thang_7" class="kt-nav__link" href="javascript:;">
                        <span class="kt-nav__link-text">
                            {__d('admin', 'thang_7')}
                        </span>
                    </a>
                </li>
                <li class="kt-nav__item">
                    <a filter-date="thang_8" class="kt-nav__link" href="javascript:;">
                        <span class="kt-nav__link-text">
                            {__d('admin', 'thang_8')}
                        </span>
                    </a>
                </li>
                <li class="kt-nav__item">
                    <a filter-date="thang_9" class="kt-nav__link" href="javascript:;">
                        <span class="kt-nav__link-text">
                            {__d('admin', 'thang_9')}
                        </span>
                    </a>
                </li>
                <li class="kt-nav__item">
                    <a filter-date="thang_10" class="kt-nav__link" href="javascript:;">
                        <span class="kt-nav__link-text">
                            {__d('admin', 'thang_10')}
                        </span>
                    </a>
                </li>
                <li class="kt-nav__item">
                    <a filter-date="thang_11" class="kt-nav__link" href="javascript:;">
                        <span class="kt-nav__link-text">
                            {__d('admin', 'thang_11')}
                        </span>
                    </a>
                </li>
                <li class="kt-nav__item">
                    <a filter-date="thang_12" class="kt-nav__link" href="javascript:;">
                        <span class="kt-nav__link-text">
                            {__d('admin', 'thang_12')}
                        </span>
                    </a>
                </li>
                <li class="kt-nav__item">
                    <a filter-date="year" class="kt-nav__link" href="javascript:;">
                        <span class="kt-nav__link-text">
                            {__d('admin', 'trong_nam')}
                        </span>
                    </a>
                </li>
            </ul>
        </div>
        </div>
    </div>
</div>

<div class="kt-portlet__body kt-portlet__body--fit">
    <div class="kt-widget17">
        <div class="kt-widget17__visual kt-widget17__visual--chart kt-portlet-fit--top kt-portlet-fit--sides" style="background-image: url(/admin/assets/media/bg/bg-7.jpg); background-size: cover;">
            <div class="kt-widget17__chart" style="height:175px;"></div>
        </div>

        <div class="kt-widget17__stats mb-30">
            <div class="kt-widget17__items">
                <div class="kt-widget17__item rounded-lg">
                    <span class="kt-widget17__icon">
                        <img src="/admin/assets/media/affiliate/number_order.png" class="mr-10" width="38">
                    </span>

                    <span class="kt-widget17__subtitle">
                        {__d('admin', 'tong_don_hang')}
                    </span>

                    <span class="kt-widget17__desc">
                        <span class="kt-font-bolder">
                            {if isset($total_order)}
                                {$total_order|number_format:0:".":","}
                            {else}
                                0
                            {/if}
                        </span>
                        {__d('admin', 'don_hang')}
                    </span>
                </div>

                <div class="kt-widget17__item rounded-lg">
                    <span class="kt-widget17__icon">
                        <img src="/admin/assets/media/affiliate/order_faild.png" class="mr-10" width="38">
                    </span>

                    <span class="kt-widget17__subtitle">
                        {__d('admin', 'don_huy')}
                    </span>

                    <span class="kt-widget17__desc">
                        <span class="kt-font-bolder">
                            {if isset($failed_order)}
                                {$failed_order|number_format:0:".":","}
                            {else}
                                0
                            {/if}
                        </span>
                        {__d('admin', 'don_hang')}
                    </span>
                </div>
            </div>

            <div class="kt-widget17__items">
                <div class="kt-widget17__item rounded-lg">
                    <span class="kt-widget17__icon">
                        <img src="/admin/assets/media/affiliate/profit.png" class="mr-10" width="38">
                    </span>

                    <span class="kt-widget17__subtitle">
                        {__d('admin', 'hoa_hong')}
                    </span>

                    <span class="kt-widget17__desc">
                        <span class="kt-font-bolder">
                            {if isset($profit_success_point)}
                                {$profit_success_point|number_format:0:".":","}
                            {else}
                                0
                            {/if}
                        </span>
                        {__d('admin', 'diem')}
                    </span>
                </div>

                <div class="kt-widget17__item rounded-lg">
                    <span class="kt-widget17__icon">
                        <img src="/admin/assets/media/affiliate/profit_temporary.png" class="mr-10" width="38">
                    </span>

                    <span class="kt-widget17__subtitle">
                        {__d('admin', 'tam_tinh')}
                    </span>

                    <span class="kt-widget17__desc">
                        <span class="kt-font-bolder">
                            {if isset($profit_success_point_to_money)}
                                {$profit_success_point_to_money|number_format:0:".":","}
                            {else}
                                0
                            {/if}
                        </span>
                        {__d('admin', 'diem')}
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>