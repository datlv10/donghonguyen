{assign var = list_language value = $this->LanguageAdmin->getList()}
{assign var = list_item value = []}
{if !empty($config.item)}
    {assign var = list_item value = $config.item}
{/if}

<div class="kt-separator kt-separator--space-lg kt-separator--border-solid mt-0 mb-20"></div>

<div class="form-group text-right">
    <span id="add-item" class="btn btn-sm btn-success">
        <i class="fa fa-plus"></i>
        {__d('admin', 'them_slider')}
    </span>
</div>

<div class="row">
    <div id="wrap-item-config" class="col-12">
        {if !empty($list_item)}
            {foreach from = $list_item item = item}
                {$this->element("../TemplateBlock/load_item_slider", [
                    'item' => $item, 
                    'list_language' => $list_language
                ])}
            {/foreach}
        {else}
            {$this->element("../TemplateBlock/load_item_slider", [
                'item' => [], 
                'list_language' => $list_language
            ])}
        {/if}
    </div>
</div>

<div class="row mt-20">
    <div class="col-lg-12">
        <p>
            {__d('admin', 'luu_y')} : 
        </p>

        <p>
            
            - {__d('admin', 'he_thong_su_dung_thu_vien_{0}', ['Owl Carousel 2'])}, 
            {__d('admin', 'tham_khao_tai_lieu_huong_dan_tai_day')}
            <a href="https://owlcarousel2.github.io/OwlCarousel2/" target="_blank">
                <i class="fa fa-external-link-alt"></i>
            </a>
        </p>

        <p>
            - {__d('admin', 'thong_tin_cau_hinh_cua_slider_se_duoc_luu_ben_du_lieu_mo_rong')}
        </p>

        <p>
            - {__d('admin', 'tham_khao_cau_hinh_mac_dinh')},
            <a href="#" class="kt-font-bold" data-toggle="modal" data-target="#refer-code-modal">
                {__d('admin', 'xem_cau_hinh')}
            </a>
        </p>


    </div>
</div>

<div id="refer-code-modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    {__d('admin', 'cau_hinh_tham_khao')}
                </h5>
                <span class="close" data-dismiss="modal"></span>
            </div>

            <div class="modal-body">            
                <div class="row">
                    <div class="col-lg-6 col-12">
                        <div id="editor-refer-code" class="nh-editor">{
    "config_slider": {
        "items": 1,
        "margin": 0,        
        "loop" : true,
        "center": false,
        "mouseDrag": true,
        "touchDrag": true,
        "pullDrag": true,
        "freeDrag": true,
        "stagePadding": true,
        "merge": false,
        "mergeFit": true,
        "autoWidth": false,
        "startPosition": 0,
        "URLhashListener": false,
        "nav": true,
        "rewind": true,
        "navElement": "div",
        "slideBy": 1,
        "slideTransition": "",
        "dots": true,
        "dotsEach": false,
        "dotsData": false,
        "lazyLoad": false,
        "lazyLoadEager": 0,
        "autoplay": false,
        "autoplayTimeout": 5000,
        "autoplayHoverPause": false,
        "smartSpeed": 250,
        "video": false,
        "videoHeight": false,
        "videoWidth": false,
        "animateOut": false,
        "animateIn": false,
        "fallbackEasing": "swing",
        "itemElement": "div",
        "checkVisible":true,
        "responsive": {
            "0":{
                "items": 1
            },
            "600":{
                "items": 3
            },
            "1000":{
                "items": 5
            }
        }
    }
}
                        </div>
                    </div>
                    <div class="col-lg-6 col-12">
                        <p>
                            <strong>{__d('admin', 'huong_dan')}</strong>: 
                            {__d('admin', 'thong_tin_cau_hinh_duoc_viet_duoi_dang_json_va_chi_nhan_cac_gia_tri_nam_trong_key_config')}
                        </p>
                        <p>
                            <img src="{ADMIN_PATH}/assets/media/note/block_slider_refer_code.png">
                        </p>
                    </div>
                </div>                  
            </div>
        </div>
    </div>
</div>