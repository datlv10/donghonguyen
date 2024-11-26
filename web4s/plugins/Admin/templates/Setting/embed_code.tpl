<div class="kt-subheader kt-grid__item" id="kt_subheader">
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

            <span id="btn-save" class="btn btn-brand btn-sm btn-save" shortcut="112">
                <i class="la la-edit"></i>
                {__d('admin', 'cap_nhat')} (F1)
            </span>
        </div>
    </div>
</div>

<div class="kt-container kt-container--fluid kt-grid__item kt-grid__item--fluid">
    <form id="main-form" action="{ADMIN_PATH}/setting/save/{$group}" method="POST" autocomplete="off">
        <div class="kt-portlet">
            <div class="kt-form">
                <div class="kt-portlet__body">
                    <div class="form-group">
                        <label>
                            {__d('admin', 'su_dung_ma_nhung')}
                        </label>

                        <div class="kt-radio-inline mt-5">
                            <label class="kt-radio kt-radio--tick kt-radio--success">
                                <input type="radio" name="load_embed" value="1" {if !empty($embed_code.load_embed)}checked{/if}> 
                                {__d('admin', 'co')}
                                <span></span>
                            </label>

                            <label class="kt-radio kt-radio--tick kt-radio--danger">
                                <input type="radio" name="load_embed" value="0" {if empty($embed_code.load_embed)}checked{/if}> 
                                {__d('admin', 'khong')}
                                <span></span>
                            </label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>
                            {__d('admin', 'thoi_gian_cho_tai_ma_nhung')}
                            (ms)
                        </label>

                        <div class="row">
                            <div class="col-md-6 col-12">
                                <div id="slider-time-delay" class="kt-nouislider--drag-danger mt-10"></div>
                            </div>

                            <div class="col-md-1 col-12">
                                {assign var = time_delay value = 0}

                                {if !isset($embed_code.time_delay)}
                                    {assign var = time_delay value = 4000}
                                {/if}

                                {if !empty($embed_code.time_delay)}
                                    {assign var = time_delay value = $embed_code.time_delay}
                                {/if}
                                
                                <input name="time_delay" id="input-time-delay" value="{$time_delay}" type="text" class="form-control" readonly="true">
                            </div>                            
                        </div>
                    </div>

                    <div class="form-group">
                        <i class="text-danger fs-12">
                            {__d('admin', 'luu_y')}:
                            {__d('admin', 'tuy_chinh_thoi_gian_tai_ma_nhung_phu_hop_giup_website_cua_ban_rut_ngan_thoi_gian_tai_ban_dau')}
                        </i>
                    </div>

                    <div class="form-group">
                        <label>
                            {__d('admin', 'ma_nhung_trong_the_head')}
                        </label>
                        <div id="embed-code-header" class="nh-embed-code">{if !empty($embed_code.head)}{htmlentities($embed_code.head)}{/if}</div>
                    </div>

                    <div class="form-group">
                        <label>
                            {__d('admin', 'ma_nhung_phia_tren_the_body')}
                        </label>
                        <div id="embed-code-top-body" class="nh-embed-code">{if !empty($embed_code.top_body)}{htmlentities($embed_code.top_body)}{/if}</div>
                    </div>

                    <div class="form-group">
                        <label>
                            {__d('admin', 'ma_nhung_phia_duoi_the_body')}
                        </label>
                        <div id="embed-code-bottom-body" class="nh-embed-code">{if !empty($embed_code.bottom_body)}{htmlentities($embed_code.bottom_body)}{/if}</div>
                    </div>
                    
                </div>
            </div>
        </div>
    </form>
</div>