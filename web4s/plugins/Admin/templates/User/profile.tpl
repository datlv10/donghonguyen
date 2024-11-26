{assign var = url_list value = "{ADMIN_PATH}/user"}
{assign var = url_edit value = "{ADMIN_PATH}/user/profile"}

{$this->element('Admin.page/content_head')}

<div id="wrap-profile" class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
    <form id="profile-form" action="{ADMIN_PATH}/user/profile-save" method="POST" autocomplete="off">
        <div class="kt-portlet nh-portlet position-relative">
            <div class="kt-portlet__head">
                <div class="kt-portlet__head-label">
                    <h3 class="kt-portlet__head-title">
                        {__d('admin', 'cap_nhat_tai_khoan')}
                    </h3>
                </div>
            </div>
            <div class="kt-portlet__body">
                {$this->element('../User/profile_form')}
            </div>
        </div>
    </form>
</div>
