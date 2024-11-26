{assign var = list_language value = $this->LanguageAdmin->getList()}
{assign var = list_item value = []}
{if !empty($config_data.item)}
    {assign var = list_item value = $config_data.item}
{/if}

<div class="kt-portlet nh-portlet">
    <div class="kt-portlet__head">
        <div class="kt-portlet__head-label">
            <h3 class="kt-portlet__head-title">
                <i class="fa fa-database mr-5"></i>
                {__d('admin', 'cau_hinh_du_lieu')}
            </h3>
        </div>
    </div>
    <div class="kt-portlet__body">
        <label class="mb-15 kt-font-danger">{__d('admin', 'san_pham_dang_tab_chi_tra_ve_du_lieu_cac_danh_muc_duoc_chon')}</label>
        <form action="{ADMIN_PATH}/mobile-app/block/save-data-config{if !empty($code)}/{$code}{/if}" method="POST" autocomplete="off">
            <div class="row">
                <div class="col-lg-3 col-12">
                    <div class="form-group">
                        <label>
                            {__d('admin', 'so_san_pham_hien_thi')}
                        </label>            
                        <input name="{NUMBER_RECORD}" value="{if !empty($config_data[{NUMBER_RECORD}])}{$config_data[{NUMBER_RECORD}]}{/if}" class="form-control form-control-sm" type="number">
                        <span class="form-text text-muted">
                            {__d('admin', 'so_luong_ban_ghi_se_hien_thi_trong_block')}
                        </span>
                    </div>
                </div>

                <div class="col-lg-3 col-12">
                    <div class="form-group">
                        <label>
                            {__d('admin', 'su_dung_phan_trang')}
                        </label>
                        <div class="kt-radio-inline mt-5">
                            <label class="kt-radio kt-radio--tick kt-radio--danger mr-20">
                                <input type="radio" name="{HAS_PAGINATION}" value="0" {if empty($config_data[{HAS_PAGINATION}])}checked="true"{/if}> 
                                    {__d('admin', 'khong')}
                                <span></span>
                            </label>

                            <label class="kt-radio kt-radio--tick kt-radio--success mr-20">
                                <input type="radio" name="{HAS_PAGINATION}" value="1" {if !empty($config_data[{HAS_PAGINATION}])}checked="true"{/if}> 
                                    {__d('admin', 'co')}
                                <span></span>
                            </label>

                            <span class="form-text text-muted">
                                {__d('admin', 'neu_su_dung_phan_trang_thi_cau_hinh_so_san_pham_se_la_so_san_pham_tren_1_trang')}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="kt-separator kt-separator--space-lg kt-separator--border-solid mt-0 mb-20"></div>

            <div class="form-group text-right">
                <span id="add-item" class="btn btn-sm btn-success">
                    <i class="fa fa-plus"></i>
                    {__d('admin', 'them_tab')}
                </span>
            </div>

            <div class="row">
                <div id="wrap-item-config" class="col-12">
                    {if !empty($list_item)}
                        {foreach from = $list_item item = item}
                            {$this->element("../MobileTemplateBlock/load_item_tab", [
                                'item' => $item,
                                'list_language' => $list_language
                            ])}
                        {/foreach}
                    {else}
                        {$this->element("../MobileTemplateBlock/load_item_tab", [
                            'item' => [],
                            'list_language' => $list_language
                        ])}
                    {/if}
                </div>
            </div>

            <div class="kt-separator kt-separator--space-lg kt-separator--border-solid mt-10 mb-20"></div>

            <div class="form-group mb-0">
                <div class="btn-group">
                    <button type="button" class="btn btn-sm btn-brand btn-save">
                        {__d('admin', 'luu_cau_hinh')}
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="kt-portlet nh-portlet">
    <div class="kt-portlet__head">
        <div class="kt-portlet__head-label">
            <h3 class="kt-portlet__head-title">
                <i class="fa fa-tablet-alt mr-5"></i>
                {__d('admin', 'cau_hinh_giao_dien')}
            </h3>
        </div>
    </div>

    <div class="kt-portlet__body">
        <form action="{ADMIN_PATH}/mobile-app/block/save-layout-config{if !empty($code)}/{$code}{/if}" method="POST" autocomplete="off">
            <div class="row">
                <div class="col-lg-6 col-12">
                    <div class="form-group">
                        <label>
                            {__d('admin', 'tieu_de_hien_thi')}
                        </label>
                        <input name="title" value="{if !empty($config_layout.title)}{$config_layout.title}{/if}" class="form-control form-control-sm" type="text">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-3 col-12">
                    <div class="form-group">
                        <label>
                            {__d('admin', 'so_luong_tren_dong')}
                            <b>(Mobile)</b>
                        </label>            
                        <input name="number_on_line" value="{if !empty($config_layout.number_on_line)}{$config_layout.number_on_line}{/if}" class="form-control form-control-sm" type="number">
                    </div>
                </div>

                <div class="col-lg-3 col-12">
                    <div class="form-group">
                        <label>
                            {__d('admin', 'so_luong_tren_dong')}
                            <b>(Ipad/Tablet)</b>
                        </label>            
                        <input name="ipad_number_on_line" value="{if !empty($config_layout.ipad_number_on_line)}{$config_layout.ipad_number_on_line}{/if}" class="form-control form-control-sm" type="number">
                    </div>
                </div>  
            </div>
            {$this->element("../MobileTemplateBlock/config_color_block", ['config_layout' => $config_layout])}

            <div class="kt-separator kt-separator--space-lg kt-separator--border-solid mt-10 mb-20"></div>

            {$this->element("../MobileTemplateBlock/config_block_spacing", ['config_layout' => $config_layout])}
            {$this->element("../MobileTemplateBlock/config_item_spacing", ['config_layout' => $config_layout])}

            <div class="form-group mb-0">
                <div class="btn-group">
                    <button type="button" class="btn btn-sm btn-brand btn-save">
                        {__d('admin', 'luu_cau_hinh')}
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>