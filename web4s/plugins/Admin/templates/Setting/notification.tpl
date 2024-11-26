{assign var = url_list value = "{ADMIN_PATH}/setting/dashboard"}

<div class="kt-subheader   kt-grid__item" id="kt_subheader">
    <div class="kt-container  kt-container--fluid ">
        <div class="kt-subheader__main">
            <h3 class="kt-subheader__title">
                {if !empty($title_for_layout)}{$title_for_layout}{/if}
            </h3>
        </div>
        <div class="kt-subheader__toolbar">
            {if !empty($url_list)}
                <a href="{$url_list}" class="btn btn-sm btn-secondary">
                    {__d('admin', 'quay_lai_danh_sach')}
                </a>
            {/if}
        </div>
    </div>
</div>

<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
    <form id="main-form" action="{ADMIN_PATH}/setting/save/{$group}" method="POST" autocomplete="off">
        <div class="kt-portlet nh-portlet">
            <div class="kt-portlet__head">
                <div class="kt-portlet__head-label">
                    <h3 class="kt-portlet__head-title">
                        {__d('admin', 'cau_hinh_chung')}
                    </h3>
                </div>
            </div>
            
            <div class="kt-portlet__body">
                <div class="form-group">
                    <label>
                        Web Push Certificates
                    </label>

                    <input name="web_push_certificates" value="{if !empty($notification.web_push_certificates)}{$notification.web_push_certificates}{/if}" type="text" class="form-control" >
                </div>

                <div class="form-group">
                    <label>
                        Icon
                    </label>
                    <div class="clearfix">
                        {assign var = icon value = ''}
                        {if !empty($notification.icon)}
                            {assign var = icon value = "background-image: url('{CDN_URL}{$notification.icon}');background-size: contain;background-position: 50% 50%;"}
                        {/if}

                        {assign var = url_select_icon value = "{CDN_URL}/myfilemanager/?type_file=image&cross_domain=1&token={$access_key_upload}&lang={LANGUAGE_ADMIN}&field_id=icon"}

                        <div class="kt-avatar kt-avatar--outline kt-avatar--circle- {if !empty($icon)}kt-avatar--changed{/if}">
                            <a {if !empty($notification.icon)}href="{CDN_URL}{$notification.icon}"{/if} target="_blank" class="kt-avatar__holder d-block" style="{$icon}"></a>
                            <label data-src="{$url_select_icon}" class="kt-avatar__upload btn-select-image" data-toggle="kt-tooltip" data-original-title="{__d('admin', 'chon_anh')}" data-type="iframe">
                                <i class="fa fa-pen"></i>
                            </label>
                            <span class="kt-avatar__cancel btn-clear-image" data-toggle="kt-tooltip" data-original-title="{__d('admin', 'xoa_anh')}">
                                <i class="fa fa-times"></i>
                            </span>

                            <input id="icon" name="icon" value="{if !empty($notification.icon)}{htmlentities($notification.icon)}{/if}" type="hidden" />
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

    <div class="kt-portlet nh-portlet">
        <div class="kt-portlet__head">
            <div class="kt-portlet__head-label">
                <h3 class="kt-portlet__head-title">
                    {__d('admin', 'huong_dan_cau_hinh')}
                </h3>
            </div>
        </div>
        
        <div class="kt-portlet__body">
            <div class="kt-section">
                <div class="kt-section__content kt-section__content--solid">
                    <p>
                        Đăng nhập <a href="https://console.firebase.google.com/u/0/?hl=en" target="_blank">Google Console</a>
                    </p>

                    <p>
                        Tạo dự án 
                    </p>

                    <p>
                        <img src="{ADMIN_PATH}/assets/media/notification/step1.png" style="height: 300px;">
                    </p>

                    <p>
                        Vào cài đặt dự án -> chọn "Add app" và khởi tạo thông tin cho 
                    </p>

                    <p>
                        <img src="{ADMIN_PATH}/assets/media/notification/step2.png" style="height: 300px;">
                    </p>

                    <p>
                        Vào cấu hình của App
                    </p>

                    <p>
                        Chọn cấu hình SDK kiểu CDN
                    </p>

                    <p>
                        <img src="{ADMIN_PATH}/assets/media/notification/step3.png" style="height: 300px;">
                    </p>

                    <p>
                        Đưa đoạn mã và vào tệp "firebase-init.js"
                    </p>

                    <p>
                        Thêm tệp "firebase-init.js" vào thư mục root của website khách hàng
                    </p>

                    <p>
                        Chuyển sang mục "Service accounts" -> chọn "Generate new private key"
                    </p>

                    <p>
                        <img src="{ADMIN_PATH}/assets/media/notification/step4.png" style="height: 300px;">
                    </p>

                    <p>
                        Tải tệp chứa "Private Key" về máy và đổi tên tệp này thành "google-service-account.json"
                    </p>
                    <p>
                        Thêm tệp "google-service-account.json" vào thư mục root của website khách hàng
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>