<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
    <div class="row">
        <div class="col-md-8 col-12 offset-md-2">
            <div class="kt-portlet kt-callout kt-callout--brand kt-callout--diagonal-bg">
                <div class="kt-portlet__body">
                    <div class="kt-callout__body">
                        <div class="kt-callout__content">
                            <h3 class="kt-callout__title">
                                <i class="fa fa-phone-square-alt"></i>
                                1900 6680 - 0901191616
                            </h3>
                            <p class="kt-callout__desc">
                                Hỗ trợ khách hàng 24/7
                            </p>
                        </div>
                        <div class="kt-callout__action">
                            <a href="tel:19006680" class="btn btn-custom btn-bold btn-upper btn-font-sm  btn-brand">
                                <i class="fa fa-phone-square-alt"></i>
                                {__d('admin', 'goi_ngay')}
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <form id="feedback-form" action="{ADMIN_PATH}/feedback/send" method="POST" autocomplete="off">
                <div class="kt-portlet nh-portlet position-relative">
                    <div class="kt-portlet__head">
                        <div class="kt-portlet__head-label">
                            <h3 class="kt-portlet__head-title kt-font-bold">
                                Gửi thông tin hướng dẫn quản trị và kiểm tra lỗi website
                            </h3>
                        </div>
                    </div>
                    
                    <div class="kt-portlet__body">
                        <div class="row">
                            <div class="col-md-6 col-12 offset-md-3">
                                <div class="form-group">
                                    <label>
                                        {__d('admin', 'ho_va_ten')}
                                        <span class="kt-font-danger">*</span>
                                    </label>
                                    <input name="full_name" value="" class="form-control form-control-sm" type="text" maxlength="100">
                                </div>

                                <div class="form-group">
                                    <label>
                                        {__d('admin', 'so_dien_thoai')}
                                        <span class="kt-font-danger">*</span>
                                    </label>
                                    <input name="phone" value="" class="form-control form-control-sm" type="text" maxlength="12">
                                </div>

                                <div class="form-group">
                                    <label>
                                        {__d('admin', 'noi_dung')}
                                        <span class="kt-font-danger">*</span>
                                    </label>
                                    <textarea name="content" class="form-control form-control-sm" rows="5" maxlength="500"></textarea>
                                </div>

                                <div class="form-group">
                                    <label>
                                        {__d('admin', 'tep_dinh_kem')}
                                    </label>
                                    <div>
                                        <div id="files-attach" class="dropzone dropzone-multi">
                                            <div class="dropzone-panel">
                                                <a class="dropzone-select btn btn-label-brand btn-bold btn-sm">
                                                    <i class="fa fa-cloud-upload-alt"></i>
                                                    {__d('admin', 'chon_tep')}
                                                </a>
                                                <a class="dropzone-remove-all btn btn-label-danger btn-bold btn-sm">
                                                    <i class="fa fa-trash-alt"></i>
                                                    {__d('admin', 'xoa_tat_ca')}
                                                </a>
                                            </div>

                                            <div class="dropzone-items">
                                                <div class="dropzone-item" style="display:none">
                                                    <div class="dropzone-file">
                                                        <div class="dropzone-filename" title="">
                                                            <span data-dz-name></span> 
                                                            <strong>
                                                                (<span data-dz-size></span>)
                                                            </strong>
                                                        </div>
                                                        <div class="dropzone-error" data-dz-errormessage></div>
                                                    </div>
                                                    <div class="dropzone-progress">
                                                        <div class="progress">
                                                            <div class="progress-bar kt-bg-brand" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0" data-dz-uploadprogress></div>
                                                        </div>
                                                    </div>
                                                    <div class="dropzone-toolbar">
                                                        <span class="dropzone-start" style="display: none;">
                                                            <i class="flaticon2-arrow"></i>
                                                        </span>

                                                        <span class="dropzone-cancel" data-dz-remove style="display: none;">
                                                            <i class="flaticon2-cross"></i>
                                                        </span>

                                                        <span class="dropzone-delete" data-dz-remove>
                                                            <i class="flaticon2-cross"></i>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <span class="form-text text-muted">
                                            {__d('admin', 'dung_luong_toi_da_{0}_va_so_luong_toi_da_{1}_tep_tin', ['2Mb', '5'])}.
                                        </span>

                                        <input name="files" type="hidden" value="">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="kt-portlet__foot p-15">
                        <div class="kt-form__actions">
                            <div class="row">
                                <div class="col-md-9 col-12 offset-md-3">
                                    <div class="form-group">
                                    <i class="fs-12 text-danger">
                                        Lưu : Các yêu cầu chỉnh sửa giao diện và nâng cấp tính năng. Quý khách hàng vui lòng gửi mail <a href="mailto:web@nhanhoa.com" class="text-danger kt-font-bold">web@nhanhoa.com</a>
                                    </i>
                                </div>
                                    <span data-link="{ADMIN_PATH}/feedback/success" class="btn-save btn btn-brand btn-sm">
                                        <i class="fab fa-telegram-plane"></i>
                                        {__d('admin', 'gui_yeu_cau')}
                                    </span>

                                    <a href="{ADMIN_PATH}/feedback" class="btn btn-secondary btn-sm">
                                        {__d('admin', 'huy_bo')}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
</div>