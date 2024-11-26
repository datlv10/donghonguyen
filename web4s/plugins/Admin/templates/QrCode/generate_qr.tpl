<div class="kt-subheader   kt-grid__item" id="kt_subheader">
    <div class="kt-container  kt-container--fluid ">
        <div class="kt-subheader__main">
            <h3 class="kt-subheader__title">
                {if !empty($title_for_layout)}{$title_for_layout}{/if}
            </h3>
        </div>
        <div class="kt-subheader__toolbar">
            <a href="{ADMIN_PATH}/setting/qr-code" class="btn btn-sm btn-secondary">
                {__d('admin', 'quay_lai')}
            </a>
        </div>
    </div>
</div>

<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid mb-40">
    <div class="row">
        <div class="col-lg-6 col-12">
            <div class="kt-portlet nh-portlet kt-portlet--height-fluid">
                <div class="kt-portlet__head">
                    <div class="kt-portlet__head-label">
                        <h3 class="kt-portlet__head-title">
                            {__d('admin', 'noi_dung_ma_qr')}
                        </h3>
                    </div>
                </div>
                
                <div class="kt-portlet__body">
            
                    <ul class="nav nav-tabs  nav-tabs-line" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-toggle="tab" href="#tab1" role="tab">
                                <i class="fa fa-file-alt"></i>
                                {__d('admin', 'van_ban')}
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#tab2" role="tab">
                                <i class="fa fa-link"></i>
                                {__d('admin', 'duong_dan')}
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#tab3" role="tab">
                                <i class="fa fa-credit-card"></i>
                                {__d('admin', 'tai_khoan_ngan_hang')}
                            </a>
                        </li>
                    </ul>

                    <div nh-tab="generate-qr" class="tab-content">
                        <div class="tab-pane active" id="tab1" role="tabpanel">
                            <form id="text-generate-form" action="{ADMIN_PATH}/setting/generate-qr" method="POST" autocomplete="off">
                                <div class="form-group">
                                    <label>
                                        {__d('admin', 'noi_dung')} 
                                    </label>
                                    <textarea name="fields[text]" rows="2" placeholder="{__d('admin', 'nhap_noi_dung')}" class="form-control form-control-sm" maxlength="255"></textarea>
                                </div>

                                <input name="type" value="{TEXT}" type="hidden">
                            </form>
                        </div>

                        <div class="tab-pane" id="tab2" role="tabpanel">
                            <form id="url-generate-form" action="{ADMIN_PATH}/setting/generate-qr" method="POST" autocomplete="off">
                                <div class="form-group">
                                    <label>
                                        {__d('admin', 'duong_dan')} 
                                    </label>
                                    <input name="fields[url]" placeholder="{__d('admin', 'nhap_duong_dan')}" class="form-control form-control-sm" type="text" maxlength="255" />
                                </div>

                                <input name="type" value="{URL}" type="hidden">
                            </form>
                        </div>

                        <div class="tab-pane" id="tab3" role="tabpanel">
                            <form id="bank-generate-form" action="{ADMIN_PATH}/setting/generate-qr" method="POST" autocomplete="off">
                                <div class="form-group">
                                    <label>
                                        {__d('admin', 'chon_ngan_hang')} 
                                    </label>
                                    {$this->Form->select('fields[bank]', $banks, ['id' => 'bank', 'empty' => null, 'default' => "", 'class' => 'form-control form-control-sm kt-selectpicker'])}
                                </div>

                                <div class="form-group">
                                    <label>
                                        {__d('admin', 'so_tai_khoan')} 
                                    </label>
                                    <input name="fields[account]" placeholder="{__d('admin', 'so_tai_khoan')}" class="form-control form-control-sm" type="text" />
                                </div>

                                <div class="form-group">
                                    <label>
                                        {__d('admin', 'ten_chu_tai_khoan')} 
                                    </label>
                                    <input name="fields[account_name]" placeholder="{__d('admin', 'ten_chu_tai_khoan')}" class="form-control form-control-sm" type="text" />
                                </div>

                                <input name="type" value="{BANK_ACCOUNT}" type="hidden">
                            </form>
                        </div>

                        <div class="form-group">
                            <span nh-btn="generate-qrcode" class="btn btn-sm btn-primary">
                                <i class="fa fa-qrcode"></i>
                                {__d('admin', 'tao_ma_qr')}
                            </span>
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6 col-12">
            <div class="kt-portlet nh-portlet kt-portlet--height-fluid">
                <div class="kt-portlet__head">
                    <div class="kt-portlet__head-label">
                        <h3 class="kt-portlet__head-title">
                            {__d('admin', 'xem_va_tai_ma')}
                        </h3>
                    </div>
                    <div class="kt-portlet__head-toolbar">
                        <div class="kt-portlet__head-actions">
                            <span nh-btn="download-qr" class="btn btn-sm btn-success d-none" data-src="">
                                <i class="fa fa-cloud-download-alt"></i>
                                {__d('admin', 'tai_xuong')}
                            </span>
                        </div>
                    </div>
                </div>
                
                <div class="kt-portlet__body">
                    <div class="row">
                        <div class="col-12 text-center">
                            <img id="image-qr" src="{if !empty($qr_image)}{$qr_image}{/if}" class="img-qrcode-preview" style="width: 400px;" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>        
</div>
