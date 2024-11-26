{assign var = url_list value = "{ADMIN_PATH}/user"}
{assign var = url_edit value = "{ADMIN_PATH}/user/language-admin"}

{$this->element('Admin.page/content_head')}
<div class="kt-subheader kt-grid__item" id="kt_subheader">
    <div class="kt-container  kt-container--fluid ">
        <div class="kt-subheader__main">
            <h3 class="kt-subheader__title">
                {$title_for_layout}
            </h3>
        </div>
        <div class="kt-subheader__toolbar">
            <button type="button" class="btn btn-sm btn-brand btn-save">
                <i class="la la-save"></i>
                {__d('admin', 'luu_cau_hinh')}
            </button>
        </div>
    </div>
</div>

<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid ">

    <form id="language-admin-form" action="{ADMIN_PATH}/user/language-admin-save" method="POST" autocomplete="off">
        <div class="kt-portlet nh-portlet position-relative mh-full">
            <div class="kt-portlet__head">
                <div class="kt-portlet__head-label">
                    <h3 class="kt-portlet__head-title">
                        {__d('admin', 'ngon_ngu_quan_tri_website')}
                    </h3>
                </div>
            </div>
            <div class="kt-portlet__body">
                <div class="form-group mt-10">
                    <div class="kt-radio-inline mt-5">
                        <label class="kt-radio kt-radio--tick kt-radio--success mr-20">
                            <input type="radio" name="language_admin"  value="vi" {if !empty($user.language_admin == 'vi') || empty($user.language_admin)}checked{/if}> 
                                    <img class="kt-margin-r-5 kt-nav__link-img-lang" src="{ADMIN_PATH}/assets/media/flags/vi.svg" alt="{__d('admin', 'tieng_viet')}">
                                {__d('admin', 'tieng_viet')}
                            <span></span>
                        </label>

                        <label class="kt-radio kt-radio--tick kt-radio--danger mr-20">
                            <input type="radio" name="language_admin" value="en" {if !empty($user.language_admin == 'en')}checked{/if}> 
                                <img class="kt-margin-r-5 kt-nav__link-img-lang" src="{ADMIN_PATH}/assets/media/flags/en.svg" alt="English">
                                English
                            <span></span>
                        </label>
                    </div>
                </div>
                <div class="kt-separator kt-separator--space-lg kt-separator--border-solid mt-10"></div>
            </div>
        </div>
    </form>
</div>
