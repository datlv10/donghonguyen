<form id="nhanh-form" action="{ADMIN_PATH}/setting/store-partner/nhanh" method="POST" autocomplete="off">

    <div class="kt-portlet__head px-0 pt-0 mb-20">
        <div class="kt-portlet__head-label">
            <h3 class="kt-portlet__head-title">
                {__d('admin', 'thong_tin_co_ban')}
            </h3>
        </div> 
    </div>
    <div class="kt-form__section kt-form__section--first">
        <div class="kt-wizard-v2__form">

            <div class="form-group">
                <label>
                    {__d('admin', 'trang_thai')}
                </label>
                <div class="kt-radio-inline mt-5">

                    <label class="kt-radio kt-radio--tick kt-radio--success mr-20">
                        <input type="radio" name="status" value="1" {if !empty($item.status)}checked{/if}> 
                            {__d('admin', 'dang_hoat_dong')}
                        <span></span>
                    </label>

                    <label class="kt-radio kt-radio--tick kt-radio--danger mr-20">
                        <input type="radio" name="status" value="0" {if empty($item.status)}checked{/if}> 
                            {__d('admin', 'khong_hoat_dong')}
                        <span></span>
                    </label>
                </div>
            </div>
        </div>
    </div>

    <div class="kt-separator kt-separator--space-lg kt-separator--border-solid mt-20 mb-20"></div>

    <div class="kt-heading kt-heading--md">
        {__d('admin', 'thong_tin_cau_hinh')}
    </div>

    <div class="kt-form__section kt-form__section--first">
        <div class="kt-wizard-v2__form">
            <div class="form-group row">
                <div class="col-md-6 col-12">
                    <label>
                        {__d('admin', 'ten_ket_noi')}
                    </label>

                    <input name="name" value="{if !empty($item.name)}{$item.name}{/if}" type="text" class="form-control" >
                </div>

                <div class="col-md-6 col-12">
                    <label>
                        Client ID
                    </label>

                    <input name="client_id" value="{if !empty($item.client_id)}{$item.client_id}{/if}" type="text" class="form-control" >
                </div>
            </div>

            <div class="form-group row">
                <div class="col-md-6 col-12">
                    <label>
                        {__d('admin', 'ma_bao_mat')}
                    </label>

                    <input name="code" value="{if !empty($item.code)}{$item.code}{/if}" type="text" class="form-control">
                </div>

                
            </div>
        </div>
    </div>

</form>