<div class="kt-subheader kt-grid__item" id="kt_subheader">
    <div class="kt-container  kt-container--fluid ">
        <div class="kt-subheader__main">
            <h3 class="kt-subheader__title">
                {if !empty($title_for_layout)}{$title_for_layout}{/if}
            </h3>
        </div>
    </div>
</div>

<div class="kt-container kt-container--fluid kt-grid__item kt-grid__item--fluid">
    <div class="kt-portlet">
        <form id="main-form" action="{ADMIN_PATH}/setting/save/point" validate method="POST" autocomplete="off">
            <div class="kt-portlet__head">
                <div class="kt-portlet__head-label">
                    <h3 class="kt-portlet__head-title">
                        {__d('admin', 'cau_hinh_tich_diem')}
                    </h3>
                </div>
                <div class="kt-portlet__head-toolbar">
                    <button type="button" class="btn btn-sm btn-brand btn-save">
                        {__d('admin', 'luu_thong_tin')}
                    </button>
                </div>
            </div>
            <div class="kt-portlet__body">
                <div class="form-group">
                    <label class="kt-font-bold">
                        {__d('admin', 'mua_hang_bang_diem')}
                    </label>

                    <div class="kt-radio-inline mt-5">
                        <label class="kt-radio kt-radio--tick kt-radio--success mr-20">
                            <input type="radio" name="pay_by_point" value="1" {if !empty($point.pay_by_point)}checked{/if}>
                            {__d('admin', 'hoat_dong')}
                            <span></span>
                        </label>
                        <label class="kt-radio kt-radio--tick kt-radio--danger">
                            <input type="radio" name="pay_by_point" value="0" {if empty($point.pay_by_point)}checked{/if}>
                            {__d('admin', 'khong_hoat_dong')}
                            <span></span>
                        </label>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12">
                        <div class="form-group mb-0">
                            <label class="kt-font-bold">
                                {__d('admin', 'ty_le_quy_diem_ra_tien')}
                                <span data-toggle="kt-tooltip" title="" data-placement="top" data-original-title="{__d('admin', 'thiet_lap_gia_tri_tien_tuong_ung_voi_1_diem_dung_de_thanh_toan_bang_diem_hoac_doi_hang')}">
                                    <i class="fas fa-exclamation-circle text-primary fs-11"></i>
                                </span>
                            </label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <button class="btn btn-sm btn-secondary" type="button">1 {__d('admin', 'diem')} =</button>
                                </div>
                                <input class="form-control form-control-sm number-input text-right" type="text" value="{if !empty($point.point_to_money)}{$point.point_to_money}{else}1{/if}" name="point_to_money">
                            </div>
                            <span class="form-text text-muted">
                                {__d('admin', 'ty_le_quy_doi_tu_diem_ra_tien')} 
                            </span>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12">
                        <div class="form-group ">
                            <label class="kt-font-bold">
                                {__d('admin', 'thoi_han_diem_tang')} ({__d('admin', 'so_ngay')})
                            </label>
                            <div class="input-group">
                                <input class="form-control form-control-sm mr--1" type="text" value="{if !empty($point.time_used_point)}{$point.time_used_point}{/if}" name="time_used_point">
                                <div class="input-group-prepend">
                                    <button class="btn btn-sm btn-secondary text-capitalize" type="button">
                                        {__d('admin', 'ngay')}
                                    </button>
                                </div>
                            </div>
                            <span class="form-text text-muted">
                                {__d('admin', 'thoi_han_su_dung_diem_tang_mac_dinh_la_30_ngay')} 
                            </span>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-6 col-md-8 col-sm-12 col-xs-12">
                        <hr>
                    </div>
                </div>

                <div class="form-group">
                    <label class="kt-font-bold">
                        {__d('admin', 'ap_dung_tich_diem_don_hang')}
                    </label>

                    <div class="kt-radio-inline mt-5">
                        <label class="kt-radio kt-radio--tick kt-radio--success mr-20">
                            <input type="radio" name="apply_refund_order" value="1" {if !empty($point.apply_refund_order)}checked{/if}>
                            {__d('admin', 'co')}
                            <span></span>
                        </label>
                        <label class="kt-radio kt-radio--tick kt-radio--danger">
                            <input type="radio" name="apply_refund_order" value="0" {if empty($point.apply_refund_order)}checked{/if}>
                            {__d('admin', 'khong')}
                            <span></span>
                        </label>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3 col-sm-4 col-xs-12">
                        <div class="form-group">
                            <label class="kt-font-bold">
                                {__d('admin', 'dieu_kien_don_hang')}
                                <span data-toggle="kt-tooltip" title="" data-placement="top" data-original-title="Điều kiện đơn hàng áp dụng phải lớn hơn giá trị">
                                    <i class="fas fa-exclamation-circle text-primary fs-11"></i>
                                </span>
                            </label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <button class="btn btn-sm btn-secondary" type="button"><i class="fas fa-dollar-sign"></i></button>
                                </div>
                                <input class="form-control form-control-sm number-input text-right" type="text" value="{if !empty($point.condition_refund_order)}{$point.condition_refund_order}{else}1{/if}" name="condition_refund_order">
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3 col-sm-4 col-xs-12">
                        <div class="form-group">
                            <label class="kt-font-bold">
                                {__d('admin', 'diem_tang_theo_moi_don_hang')}
                            </label>
                            <div class="input-group">
                                <div class="input-group text-capitalize">
                                    <div class="input-group-prepend min-w-70">
                                        <select id="type_refund" class="form-control form-control-sm kt-selectpicker radius-0" name="type_refund">
                                            <option value="{PERCENT}" {if isset($point.type_refund) && $point.type_refund == "{PERCENT}"}selected{/if}>{__d('admin', '%')}</option>
                                            <option value="{POINT}" {if isset($point.type_refund) && $point.type_refund == "{POINT}"}selected{/if}>{__d('admin', 'diem')}</option>
                                        </select>
                                    </div>
                                    <input name="value_refund" value="{if !empty($point.value_refund)}{$point.value_refund}{/if}" type="text" class="form-control form-control-sm number-input text-right">
                                </div>
                                <span class="form-text text-muted">
                                    <strong class="d-inline-block w-40-px">%</strong>: {__d('admin', 'so_diem_duoc_tang_theo_%_moi_don_hang')}</br>
                                    <strong class="d-inline-block text-capitalize w-40-px">{__d('admin', 'diem')}</strong>: {__d('admin', 'so_diem_duoc_tang_theo_moi_don_hang')} 
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <div class="kt-portlet">
        <form id="main-form" action="{ADMIN_PATH}/setting/save/attendance" method="POST" autocomplete="off">
            <div class="kt-portlet__head">
                <div class="kt-portlet__head-label">
                    <h3 class="kt-portlet__head-title">
                        {__d('admin', 'cau_hinh_diem_danh')}
                    </h3>
                </div>
                <div class="kt-portlet__head-toolbar">
                    <button type="button" class="btn btn-sm btn-brand btn-save">
                        {__d('admin', 'luu_thong_tin')}
                    </button>
                </div>
            </div>
            <div class="kt-portlet__body">
                <div class="row">
                    <div class="col-md-8 col-sm-10 col-xs-12">
                        {if !empty($attendance)}
                            {$this->element("../Setting/point_attendance", ['attendance' => $attendance])}
                        {else}
                            {$this->element("../Setting/point_attendance", ['attendance' => ''])}
                        {/if}
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

{$this->element('Admin.page/popover_quick_change')}