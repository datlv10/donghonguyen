{if !empty($user)}
    {if !empty($user.image_avatar)}
        {assign var = url_img value = "{CDN_URL}{$user.image_avatar}"}
    {else}
        {assign var = url_img value = "{ADMIN_PATH}{NO_IMAGE_URL}"}
    {/if}

    {assign var = url_list value = "{ADMIN_PATH}/user"}
    <div class="kt-subheader   kt-grid__item" id="kt_subheader">
        <div class="kt-container  kt-container--fluid ">
            <div class="kt-subheader__main">
                <h3 class="kt-subheader__title">
                    {if !empty($title_for_layout)}{$title_for_layout}{/if}
                </h3>
            </div>

            <div class="kt-subheader__toolbar">
                <a href="{$url_list}" class="btn btn-default btn-bold">
                    {__d('admin', 'quay_lai_danh_sach')}
                </a>
            </div>
        </div>
    </div>

    <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
        <div class="kt-wizard-v4">
            <div class="kt-portlet">
                <div class="kt-portlet__body kt-portlet__body--fit">
                    <div class="kt-grid">
                        <div class="kt-grid__item kt-grid__item--fluid kt-wizard-v4__wrapper">
                            <div class="kt-form" style="width: 95%">
                                <div class="kt-wizard-v4__content">
                                    <div class="kt-form__section kt-form__section--first">
                                        <div class="kt-wizard-v4__review entire-detail">
                                            <div class="kt-wizard-v4__review-item">
                                                <div class="kt-wizard-v4__review-title pb-20 pt-20">
                                                    {__d('admin', 'thong_tin_tai_khoan')}
                                                </div>
                                                <div class="kt-wizard-v4__review-content">
                                                    <p class="mb-5">
                                                        {__d('admin', 'ten_dang_nhap')}: 
                                                        <span class="kt-font-bolder">
                                                            {if !empty($user.username)}
                                                                {$user.username}
                                                            {/if}
                                                        </span>
                                                    </p>
                                                    <p class="mb-5">
                                                        {__d('admin', 'email')}: 
                                                        <span class="kt-font-bolder">
                                                            {if !empty($user.email)}
                                                                {$user.email}
                                                            {/if}
                                                        </span>
                                                    </p>
                                                    <p class="mb-5">
                                                        {__d('admin', 'nhom_quyen')}: 
                                                        <span class="kt-font-bolder">
                                                            {if !empty($user.role_name)}
                                                                {$user.role_name}
                                                            {/if}
                                                        </span>
                                                    </p>
                                                    <p class="mb-5">
                                                        {__d('admin', 'ngay_tao')}: 
                                                        <span class="kt-font-bolder">
                                                            {if !empty($user.created)}
                                                                {date("H:i - d/m/Y", $user.created)}
                                                            {/if}
                                                        </span>
                                                    </p>
                                                    <p class="mb-5">
                                                        {__d('admin', 'cap_nhat')}: 
                                                        <span class="kt-font-bolder">
                                                            {if !empty($user.updated)}
                                                                {date("H:i - d/m/Y", $user.updated)}
                                                            {/if}
                                                        </span>
                                                    </p>
                                                </div>
                                            </div>

                                            <div class="kt-wizard-v4__review-item">
                                                <div class="kt-wizard-v4__review-title pb-20 pt-20">
                                                    {__d('admin', 'thong_tin_ca_nhan')}
                                                </div>
                                                <div class="kt-wizard-v4__review-content">
                                                    <p class="mb-5">
                                                        {__d('admin', 'anh_dai_dien')}: 
                                                        <div class="d-flex flex-wrap kt-margin-t-10">
                                                            <a class="kt-media kt-media--xl  kt-margin-r-5 kt-margin-t-5" data-lightbox="image-avatar" href="{$url_img}">
                                                                <img class="img-cover" src="{$url_img}" alt="image-avatar" style="width: 80px;">
                                                            </a>
                                                        </div>
                                                    </p>
                                                    <p class="mb-5">
                                                        {__d('admin', 'ho_va_ten')}: 
                                                        <span class="kt-font-bolder">
                                                            {if !empty($user.full_name)}
                                                                {$user.full_name}
                                                            {/if}
                                                        </span>
                                                    </p>
                                                    <p class="mb-5">
                                                        {__d('admin', 'so_dien_thoai')}: 
                                                        <span class="kt-font-bolder">
                                                            {if !empty($user.phone)}
                                                                {$user.phone}
                                                            {/if}
                                                        </span>
                                                    </p>
                                                    <p class="mb-5">
                                                        {__d('admin', 'dia_chi')}: 
                                                        <span class="kt-font-bolder">
                                                            {if !empty($user.address)}
                                                                {$user.address}
                                                            {/if}
                                                        </span>
                                                    </p>
                                                    <p class="mb-5">
                                                        {__d('admin', 'ngay_sinh')}: 
                                                        <span class="kt-font-bolder">
                                                            {if !empty($user.birthday)}
                                                                {$this->UtilitiesAdmin->convertIntgerToDateString($user.birthday)}
                                                            {/if}
                                                        </span>
                                                    </p>
                                                    
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
{else}
    <span class="kt-datatable--error">{__d('admin', 'khong_lay_duoc_thong_tin_ban_ghi')}</span>
{/if}
