<div class="kt-portlet__body kt-portlet__body--fit">
    <div class="row row-no-padding row-col-separator-xl">
        <div class="col-md-12 col-lg-12 col-xl-6">

            <!--begin:: Widgets/Stats2-1 -->
            <div class="kt-widget1">
                <div class="kt-widget1__item">
                    <div class="kt-widget1__info">
                        <h3 class="kt-widget1__title">{__d('admin', 'doanh_thu_tam_tinh')}</h3>
                        <span class="kt-widget1__desc">{__d('admin', 'bao_gom_cac_don_hang_chua_thanh_cong')}</span>
                    </div>
                    <span class="kt-widget1__number kt-font-brand">
                        {if !empty($report.total_total)}
                            {$report.total_total|number_format:0:".":","}
                        {/if}
                    </span>
                </div>
                <div class="kt-widget1__item">
                    <div class="kt-widget1__info">
                        <h3 class="kt-widget1__title">{__d('admin', 'doanh_thu')}</h3>
                        <span class="kt-widget1__desc">{__d('admin', 'doanh_thu_thuc_te')}</span>
                    </div>
                    <span class="kt-widget1__number kt-font-success">
                        {if !empty($report.total_order_done)}
                            {$report.total_order_done|number_format:0:".":","}
                        {/if}
                    </span>
                </div>
                <div class="kt-widget1__item">
                    <div class="kt-widget1__info">
                        <h3 class="kt-widget1__title">{__d('admin', 'so_don_dat_hang')}</h3>
                        <span class="kt-widget1__desc">{__d('admin', 'bao_gom_cac_don_hang_chua_thanh_cong')}</span>
                    </div>
                    <span class="kt-widget1__number kt-font-primary">
                        {if !empty($report.total_number_order)}
                            {$report.total_number_order|number_format:0:".":","}
                        {/if}
                    </span>
                </div>
            </div>

            <!--end:: Widgets/Stats2-1 -->
        </div>
        <div class="col-md-12 col-lg-12 col-xl-6">

            <!--begin:: Widgets/Stats2-2 -->
            <div class="kt-widget1">
                <div class="kt-widget1__item">
                    <div class="kt-widget1__info">
                        <h3 class="kt-widget1__title">{__d('admin', 'don_huy')}</h3>
                        <span class="kt-widget1__desc">{__d('admin', 'tong_tien_don_huy')}</span>
                    </div>
                    <span class="kt-widget1__number kt-font-dark">
                        {if !empty($report.total_order_cancel)}
                            {$report.total_order_cancel|number_format:0:".":","}
                        {/if}
                    </span>
                </div>
                <div class="kt-widget1__item">
                    <div class="kt-widget1__info">
                        <h3 class="kt-widget1__title">{__d('admin', 'con_no')}</h3>
                        <span class="kt-widget1__desc">{__d('admin', 'tong_tien_chua_thanh_toan')}</span>
                    </div>
                    <span class="kt-widget1__number kt-font-warning">
                        {if !empty($report.total_debt)}
                            {$report.total_debt|number_format:0:".":","}
                        {/if}
                    </span>
                </div>
                <div class="kt-widget1__item">
                    <div class="kt-widget1__info">
                        <h3 class="kt-widget1__title">{__d('admin', 'so_luong_san_pham')}</h3>
                        <span class="kt-widget1__desc">{__d('admin', 'bao_gom_cac_don_hang_chua_thanh_cong')}</span>
                    </div>
                    <span class="kt-widget1__number kt-font-brand">
                        {if !empty($report.total_count_items)}
                            {$report.total_count_items|number_format:0:".":","}
                        {/if}
                    </span>
                </div>
            </div>

            <!--end:: Widgets/Stats2-2 -->
        </div>
    </div>
</div>