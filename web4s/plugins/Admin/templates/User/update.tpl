{assign var = url_list value = "{ADMIN_PATH}/user"}
{assign var = url_add value = "{ADMIN_PATH}/user/add"}
{assign var = url_edit value = "{ADMIN_PATH}/user/update"}

{$this->element('Admin.page/content_head', [
    'url_list' => $url_list
])}

<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
    <div id="wrap-profile" class="kt-portlet kt-portlet--tabs">
        <div class="kt-portlet__head">
            <div class="kt-portlet__head-toolbar">
                <ul class="nav nav-tabs nav-tabs-space-xl nav-tabs-line nav-tabs-bold nav-tabs-line-3x nav-tabs-line-brand" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" data-toggle="tab" href="#info-user" role="tab">
                            <i class="fa fa-user-alt"></i> {__d('admin', 'thong_tin_tai_khoan')}
                        </a>
                    </li>
                    {if !empty($id)}
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#change-password" role="tab">
                                <i class="fa fa-user-lock"></i> {__d('admin', 'thay_doi_mat_khau')}
                            </a>
                        </li>
                    {/if}
                </ul>
            </div>
        </div>
        <div class="kt-portlet__body">
            <div class="tab-content">
                <div class="tab-pane active" id="info-user" role="tabpanel">
                    <form id="profile-form" action="{ADMIN_PATH}/user/save{if !empty($id)}/{$id}{/if}" method="POST" autocomplete="off">
                        {$this->element('../User/element_user')}
                    </form>
                </div>
                {if !empty($id)}
                    <div class="tab-pane" id="change-password" role="tabpanel">
                        <form id="change-password-form" action="{ADMIN_PATH}/user/change-password/{$id}" method="POST" autocomplete="off">
                            {$this->element('../User/element_change_password')}
                        </form>
                    </div>
                {/if}
            </div>
        </div>
    </div>
</div>
