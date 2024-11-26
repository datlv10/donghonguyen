{if $type_load == {NORMAL}}
    <div class="row">
        <div class="col-lg-12 col-12">
            <div class="form-group">
                <label>
                    {__d('admin', 'giai_thich')}
                </label>
                <div class="clearfix">
                    <p class="mt-5 mb-5">
                        <strong>Normal</strong>: 
                        {__d('admin', 'block_se_duoc_hien_binh_thuong_khi_trang_web_duoc_tai')}. 
                    </p>
                </div>
            </div>
        </div>
    </div>
{/if}

{if $type_load == {TIMEOUT}}
    <div class="row">
        <div class="col-lg-6 col-12">
            <div class="form-group">
                <label>
                    {__d('admin', 'thoi_gian_cho')}
                </label>
                <div class="kt-ion-range-slider">
                    <input id="timeout-select" name="config[timeout]" value="{if !empty($config.timeout)}{$config.timeout}{/if}" type="hidden" />
                </div>
            </div>
        </div>

        <div class="col-lg-6 col-12">
            <div class="form-group">
                <label>
                    {__d('admin', 'giai_thich')}
                </label>
                <div class="clearfix">
                    <p class="mt-5 mb-5">
                        <strong>Timeout</strong>: 
                        {__d('admin', 'block_se_duoc_hien_sau_khi_trang_web_da_duoc_tai_thanh_cong')}. 
                    </p>
                    <p class="mb-0">
                        <strong>{__d('admin', 'thoi_gian_cho')}</strong>: 
                        {__d('admin', 'la_khoang_thoi_gian_tu_khi_trang_da_tai_xong_den_khi_block_duoc_hien_thi')}
                    </p>
                </div>
            </div>
        </div>
    </div>
{/if}

{if $type_load == {SCROLL}}
    <div class="row">
        <div class="col-lg-6 col-12">
            <div class="form-group">
                <label>
                    {__d('admin', 'khoang_cach')} (px)
                </label>
                <div class="row">
                    <div class="col-lg-6 col-12">
                        <input name="config[offset]" value="{if !empty($config.offset)}{$config.offset}{/if}" class="form-control form-control-sm number-input" type="text">
                    </div>
                </div>                            
            </div>
        </div>

        <div class="col-lg-6 col-12">
            <div class="form-group">
                <label class="mb-5">
                    {__d('admin', 'giai_thich')}
                </label>
                <div class="clearfix">
                    <p class="mb-0">
                        <strong>Scroll</strong>: 
                        {__d('admin', 'block_se_duoc_hien_sau_khi_con_tro_chuot_keo_den_vi_tri_cua_no')}. 
                    </p>
                    <p class="mb-0">
                        <strong>{__d('admin', 'khoang_cach')}</strong>: 
                        {__d('admin', 'la_khoang_cach_cua_block_den_vi_tri_ma_block_se_bat_dau_hien_thi_khi_con_tro_chuot_den_do')}
                    </p>
                </div>
            </div>
        </div>
    </div>
{/if}