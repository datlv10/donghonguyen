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
        <form action="{ADMIN_PATH}/mobile-app/block/save-data-config{if !empty($code)}/{$code}{/if}" method="POST" autocomplete="off">
            <div class="row">
                <div class="col-lg-3 col-12">
                    <div class="form-group">
                        <label>
                            {__d('admin', 'so_danh_gia_hien_thi')}
                        </label>            
                        <input name="{NUMBER_RECORD}" value="{if !empty($config_data[{NUMBER_RECORD}])}{$config_data[{NUMBER_RECORD}]}{else}20{/if}" class="form-control form-control-sm" type="number" max="200">
                        <span class="form-text text-muted">
                            {__d('admin', 'so_luong_danh_gia_hien_thi_trong_1_trang')}
                        </span>
                    </div>
                </div>

                <div class="col-lg-3 col-12">
                    <div class="form-group">
                        <label>
                            {__d('admin', 'sap_xep_theo')}
                        </label>
                        {$this->Form->select("{SORT_FIELD}", $this->TemplateAdmin->getListSortFieldOfRating(), ['empty' => "{__d('admin', 'chon')}", 'default' => "{if !empty($config_data[{SORT_FIELD}])}{$config_data[{SORT_FIELD}]}{/if}", 'class' => 'form-control form-control-sm kt-selectpicker'])}
                    </div>
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


