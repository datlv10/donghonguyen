<div class="kt-subheader   kt-grid__item" id="kt_subheader">
    <div class="kt-container  kt-container--fluid ">
        <div class="kt-subheader__main">
            <h3 class="kt-subheader__title">
                {if !empty($title_for_layout)}{$title_for_layout}{/if}
            </h3>
        </div>
        <div class="kt-subheader__toolbar">
            <a href="{ADMIN_PATH}/setting/dashboard" class="btn btn-sm btn-secondary">
                {__d('admin', 'quay_lai')}
            </a>
        </div>
    </div>
</div>

<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
    <form id="fpt-telecom-form" action="{ADMIN_PATH}/setting/save/sms_brandname" method="POST" autocomplete="off">
        <div class="kt-portlet nh-portlet">
            <div class="kt-portlet__head">
                <div class="kt-portlet__head-label">
                    <h3 class="kt-portlet__head-title">
                        {__d('admin', 'lua_chon_doi_tac')}
                    </h3>
                </div>
            </div>

            <div class="kt-portlet__body">
                <div class="form-group">
                    <div class="kt-radio-inline mt-5">
                        <label class="kt-radio kt-radio--tick kt-radio--success mr-20">
                            <input type="radio" name="default_partner" value="fpt_telecom" {if !empty($sms_brandname.default_partner) && $sms_brandname.default_partner == {FPT_TELECOM}}checked{/if}> 
                                FPT Telecom
                            <span></span>
                        </label>                        
                    </div>

                    <div class="kt-radio-inline mt-5">
                        <label class="kt-radio kt-radio--tick kt-radio--success mr-20">
                            <input type="radio" name="default_partner" value="esms" {if !empty($sms_brandname.default_partner) && $sms_brandname.default_partner == {ESMS}}checked{/if}> 
                                ESMS
                            <span></span>
                        </label>                        
                    </div>
                </div>
                <div class="kt-separator kt-separator--space-lg kt-separator--border-solid mt-10 mb-20"></div>

                <div class="form-group mb-0">
                    <button type="button" class="btn btn-sm btn-brand btn-save">
                        {__d('admin', 'luu_cau_hinh')}
                    </button>
                </div>
            </div>
        </div>
    </form>

    <form id="fpt-telecom-form" action="{ADMIN_PATH}/setting/sms-brandname/save-fpt-telecom" method="POST" autocomplete="off">
        <div class="kt-portlet nh-portlet">
            <div class="kt-portlet__head">
                <div class="kt-portlet__head-label">
                    <h3 class="kt-portlet__head-title">
                        FPT Telecom
                    </h3>
                </div>
            </div>
            
            <div class="kt-portlet__body">
                <div class="form-group">
                    <label>
                        {__d('admin', 'trang_thai')}
                    </label>
                    <div class="kt-radio-inline mt-5">
                        <label class="kt-radio kt-radio--tick kt-radio--success mr-20">
                            <input type="radio" name="fpt_telecom[status]"  value="1" {if !empty($sms_brandname.fpt_telecom.status)}checked{/if}> 
                                {__d('admin', 'dang_hoat_dong')}
                            <span></span>
                        </label>

                        <label class="kt-radio kt-radio--tick kt-radio--danger mr-20">
                            <input type="radio" name="fpt_telecom[status]" value="0" {if !isset($sms_brandname.fpt_telecom.status) || empty($sms_brandname.fpt_telecom.status)}checked{/if}> 
                                {__d('admin', 'khong_hoat_dong')}
                            <span></span>
                        </label>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 col-12">
                        <div class="form-group">
                            <label>
                                Brandname
                            </label>

                            <input name="fpt_telecom[brandname]" value="{if !empty($sms_brandname.fpt_telecom.brandname)}{$sms_brandname.fpt_telecom.brandname}{/if}" type="text" class="form-control">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 col-12">
                        <div class="form-group">
                            <label>
                                Client ID
                            </label>

                            <input name="fpt_telecom[client_id]" value="{if !empty($sms_brandname.fpt_telecom.client_id)}{$sms_brandname.fpt_telecom.client_id}{/if}" type="text" class="form-control" >
                        </div>
                    </div>
                    <div class="col-md-6 col-12">
                        <div class="form-group">
                            <label>
                                Client Secret
                            </label>

                            <input name="fpt_telecom[client_secret]" value="{if !empty($sms_brandname.fpt_telecom.client_secret)}{$sms_brandname.fpt_telecom.client_secret}{/if}" type="text" class="form-control">
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6 col-12">
                        <div class="form-group">
                            <label>
                                {__d('admin', 'che_do')}
                            </label>

                            {$this->Form->select('fpt_telecom[mode]', $this->ListConstantAdmin->listMode(), ['empty' => '', 'default' => "{if !empty($sms_brandname.fpt_telecom.mode)}{$sms_brandname.fpt_telecom.mode}{/if}", 'class' => 'form-control form-control-sm kt-selectpicker'])}
                        </div>
                    </div>
                </div>

                <div class="kt-separator kt-separator--space-lg kt-separator--border-solid mt-10 mb-20"></div>

                <div class="form-group mb-0">
                    <button type="button" class="btn btn-sm btn-brand btn-save">
                        {__d('admin', 'luu_cau_hinh')}
                    </button>
                </div>
            </div>
        </div>
    </form>

    <form id="esms-form" action="{ADMIN_PATH}/setting/sms-brandname/save-esms" method="POST" autocomplete="off">
        <div class="kt-portlet nh-portlet">
            <div class="kt-portlet__head">
                <div class="kt-portlet__head-label">
                    <h3 class="kt-portlet__head-title">
                        ESMS
                    </h3>
                </div>
            </div>
            
            <div class="kt-portlet__body">
                <div class="form-group">
                    <label>
                        {__d('admin', 'trang_thai')}
                    </label>
                    <div class="kt-radio-inline mt-5">
                        <label class="kt-radio kt-radio--tick kt-radio--success mr-20">
                            <input type="radio" name="esms[status]"  value="1" {if !empty($sms_brandname.esms.status)}checked{/if}> 
                                {__d('admin', 'dang_hoat_dong')}
                            <span></span>
                        </label>

                        <label class="kt-radio kt-radio--tick kt-radio--danger mr-20">
                            <input type="radio" name="esms[status]" value="0" {if !isset($sms_brandname.esms.status) || empty($sms_brandname.esms.status)}checked{/if}> 
                                {__d('admin', 'khong_hoat_dong')}
                            <span></span>
                        </label>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 col-12">
                        <div class="form-group">
                            <label>
                                Brandname
                            </label>

                            <input name="esms[brandname]" value="{if !empty($sms_brandname.esms.brandname)}{$sms_brandname.esms.brandname}{/if}" type="text" class="form-control">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 col-12">
                        <div class="form-group">
                            <label>
                                Client ID
                            </label>

                            <input name="esms[client_id]" value="{if !empty($sms_brandname.esms.client_id)}{$sms_brandname.esms.client_id}{/if}" type="text" class="form-control" >
                        </div>
                    </div>
                    <div class="col-md-6 col-12">
                        <div class="form-group">
                            <label>
                                Client Secret
                            </label>

                            <input name="esms[client_secret]" value="{if !empty($sms_brandname.esms.client_secret)}{$sms_brandname.esms.client_secret}{/if}" type="text" class="form-control">
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6 col-12">
                        <div class="form-group">
                            <label>
                                {__d('admin', 'che_do')}
                            </label>

                            {$this->Form->select('esms[mode]', $this->ListConstantAdmin->listMode(), ['empty' => '', 'default' => "{if !empty($sms_brandname.esms.mode)}{$sms_brandname.esms.mode}{/if}", 'class' => 'form-control form-control-sm kt-selectpicker'])}
                        </div>
                    </div>
                </div>

                <div class="kt-separator kt-separator--space-lg kt-separator--border-solid mt-10 mb-20"></div>

                <div class="form-group mb-0">
                    <button type="button" class="btn btn-sm btn-brand btn-save">
                        {__d('admin', 'luu_cau_hinh')}
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>