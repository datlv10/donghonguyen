<div class="kt-header__topbar">

    {assign var = last_time value = $this->NhNotificationAdmin->getLastTimeNotification()}
    <div class="kt-header__topbar-item kt-header__topbar-item--quick-panel" data-toggle="kt-tooltip" title="{__d('admin', 'thong_bao')}" data-placement="bottom">
        <span nh-notification="mini" id="kt_quick_panel_toggler_btn" class="kt-header__topbar-icon">
            <span nh-notification="count-notification" data-last-time="{$last_time}" class="kt-badge kt-badge--outline kt-badge--danger mini-notification d-none">
                <span class="text-danger"></span>
            </span>
            <i class="flaticon2-bell-1 text-info"></i>
        </span>
    </div>
    <div class="kt-header__topbar-item kt-header__topbar-item--user kt-margin-l-10">
        <div class="kt-header__topbar-wrapper" data-toggle="dropdown" data-offset="0px,0px">
            <div class="kt-header__topbar-user">
                <span class="kt-header__topbar-welcome kt-hidden-mobile">
                    {__d('admin', 'xin_chao')},
                </span>
                <span class="kt-header__topbar-username kt-hidden-mobile">
                    {if !empty($auth_user.full_name)}
                        {preg_replace("/\s.*$/","", $auth_user.full_name)}
                    {/if}
                </span>
                {* <img class="kt-hidden" alt="Pic" src="{ADMIN_PATH}/assets/media/users/300_25.jpg" /> *}

                <!--use below badge element instead the user avatar to display username's first letter(remove kt-hidden class to display it) -->
                <span class="kt-badge kt-badge--username kt-badge--unified-success kt-badge--lg kt-badge--rounded kt-badge--bold">
                    {if !empty($auth_user.full_name)}
                        {mb_substr($auth_user.full_name, 0, 1, 'UTF-8')}
                    {/if}
                </span>
            </div>
        </div>
        <div class="dropdown-menu dropdown-menu-fit dropdown-menu-right dropdown-menu-anim dropdown-menu-top-unround dropdown-menu-xl dropdown-info-user">

            <!--begin: Head -->
            <div class="kt-user-card kt-user-card--skin-dark kt-notification-item-padding-x" style="background-image: url({ADMIN_PATH}/assets/media/misc/bg-1.jpg)">
                <div class="kt-user-card__avatar">
                    {* <img class="kt-hidden" alt="Pic" src="{ADMIN_PATH}/assets/media/users/300_25.jpg" /> *}

                    <!--use below badge element instead the user avatar to display username's first letter(remove kt-hidden class to display it) -->
                    <span class="kt-badge kt-badge--lg kt-badge--rounded kt-badge--bold kt-font-success">
                        {if !empty($auth_user.full_name)}
                            {mb_substr($auth_user.full_name, 0, 1, 'UTF-8')}
                        {/if}
                    </span>
                </div>
                <div class="kt-user-card__name">
                    {if !empty($auth_user.full_name)}
                        {$auth_user.full_name}
                    {/if}
                </div>
            </div>

            <!--end: Head -->

            <!--begin: Navigation -->
            <div class="kt-notification">
                <a href="{ADMIN_PATH}/user/profile" class="kt-notification__item">
                    <div class="kt-notification__item-icon">
                        <i class="fa fa-user-alt kt-font-success"></i>
                    </div>
                    <div class="kt-notification__item-details">
                        <div class="kt-notification__item-title kt-font-bold">
                            {__d('admin', 'thong_tin_tai_khoan')}
                        </div>
                    </div>
                </a>

                <a href="{ADMIN_PATH}/user/profile-change-password" class="kt-notification__item">
                    <div class="kt-notification__item-icon">
                        <i class="fa fa-unlock-alt kt-font-success"></i>
                    </div>
                    <div class="kt-notification__item-details">
                        <div class="kt-notification__item-title kt-font-bold">
                            {__d('admin', 'thay_doi_mat_khau')}
                        </div>
                    </div>
                </a>

                <a href="{ADMIN_PATH}/user/language-admin" class="kt-notification__item">
                    
                    <div class="kt-notification__item-icon">
                        <i class="fa fa-language kt-font-success"></i>
                    </div>
                    
                    <div class="kt-notification__item-details kt-notification__item-details-lang">
                        <div class="kt-notification__item-title kt-font-bold">
                            {__d('admin', 'ngon_ngu_quan_tri')}
                        </div>
                    </div>

                </a>

                <div class="kt-notification__custom kt-space-between border-top-0 kt-padding-t-15 kt-padding-b-15">
                    <a href="{ADMIN_PATH}/logout" class="btn btn-label btn-label-danger btn-sm btn-bold">
                        <i class="fa fa-sign-out-alt"></i>
                        
                        {__d('admin', 'dang_xuat')}
                    </a>
                </div>
            </div>

            <!--end: Navigation -->
        </div>
    </div>

</div>