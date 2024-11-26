<div class="kt-portlet__body kt-portlet__body--fit">
    <div class="kt-widget17">
        <div class="kt-widget17__stats m-0 w-100 text-center">
            <div class="kt-widget17__items">
                <div class="kt-widget17__item cursor-default p-0">
                    <div class="kt-portlet__head kt-portlet__space-x">
                        <div class="kt-portlet__head-label">
                            <h3 class="kt-portlet__head-title">
                                {__d('admin', 'khach_hang')}
                            </h3>
                        </div>
                    </div>

                    <div class="kt-portlet__body">
                        <div class="kt-widget12">
                            <div class="kt-widget12__content">
                                <div class="kt-widget12__item mb-0">
                                    <div class="kt-widget12__info">
                                        <span class="kt-widget12__desc">
                                            {__d('admin', 'tong_khach_hang')}
                                        </span>
                                        <span class="kt-widget12__value d-inline-block">
                                            {if !empty($number_customer)}
                                                {$number_customer|number_format:0:".":","}
                                            {else}
                                                0
                                            {/if}
                                            {__d('admin', 'khach_hang')}
                                        </span>
                                    </div>

                                    <div class="kt-widget12__info">
                                        <span class="kt-widget12__desc">
                                            {__d('admin', 'tong_binh_luan')}
                                        </span>
                                        <span class="kt-widget12__value d-inline-block">
                                            {if !empty($number_comment)}
                                                {$number_comment|number_format:0:".":","}
                                            {else}
                                                0
                                            {/if}
                                            {__d('admin', 'binh_luan')}
                                        </span>
                                    </div>

                                    <div class="kt-widget12__info">
                                        <span class="kt-widget12__desc">
                                            {__d('admin', 'tong_danh_gia')}
                                        </span>
                                        <span class="kt-widget12__value d-inline-block">
                                            {if !empty($number_rating)}
                                                {$number_rating|number_format:0:".":","}
                                            {else}
                                                0
                                            {/if}
                                            {__d('admin', 'danh_gia')}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="kt-widget17__items">
                <div class="kt-widget17__item cursor-default p-0">
                    <div class="kt-portlet__head kt-portlet__space-x">
                        <div class="kt-portlet__head-label">
                            <h3 class="kt-portlet__head-title">
                                {__d('admin', 'thong_ke_khach_hang')}
                            </h3>
                        </div>
                    </div>
                    <div class="kt-portlet__body">
                        <div class="kt-widget20 kt-widget14 pt-0">
                            
                            <div class="kt-widget14__chart" style="height:120px;">
                                <canvas id="chart-customer"></canvas>
                                
                                <input id="data-chart-customer" type="hidden" value="{if !empty($chart_data)}{htmlentities($chart_data|@json_encode)}{/if}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>