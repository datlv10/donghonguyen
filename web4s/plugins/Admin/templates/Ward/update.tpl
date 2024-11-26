{assign var = url_list value = "{ADMIN_PATH}/ward/{$district_id}"}
{assign var = url_add value = "{ADMIN_PATH}/ward/add/{$district_id}"}
{assign var = url_edit value = "{ADMIN_PATH}/ward/update/{$district_id}"}

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

            {if !empty($url_edit) || !empty($url_add)}
                <div class="btn-group">
                    {if empty($id)}
                        <button data-link="{$url_edit}" data-update="1" id="btn-save" type="button" class="btn btn-sm btn-brand btn-save" shortcut="112">
                            <i class="la la-plus"></i>
                            {__d('admin', 'them_moi')} (F1)
                        </button>
                    {else}
                        <button id="btn-save" type="button" class="btn btn-sm btn-brand btn-save" shortcut="112">
                            <i class="la la-edit"></i>
                            {__d('admin', 'cap_nhat')} (F1)
                        </button>
                    {/if}
                    
                    <button type="button" class="btn btn-brand dropdown-toggle dropdown-toggle-split" data-toggle="dropdown"></button>
                    <div class="dropdown-menu dropdown-menu-right">
                        <ul class="kt-nav p-0">

                            {if !empty($url_add)}
                                <li class="kt-nav__item">
                                    <span data-link="{$url_add}" class="kt-nav__link btn-save">
                                        <i class="kt-nav__link-icon flaticon2-medical-records"></i>
                                        <span class="kt-nav__link-text">
                                            {__d('admin', 'luu_&_them_moi')}
                                        </span>
                                    </span>
                                </li>
                            {/if}

                            {if !empty($url_list)}
                                <li class="kt-nav__item">
                                    <span data-link="{$url_list}" class="kt-nav__link btn-save">
                                        <i class="kt-nav__link-icon flaticon2-hourglass-1"></i>
                                        <span class="kt-nav__link-text">
                                            {__d('admin', 'luu_&_quay_lai')}
                                        </span>
                                    </span>
                                </li>
                            {/if}
                        </ul>
                    </div>
                </div>
            {/if}
        </div>
    </div>
</div>

<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
    <form id="main-form" action="{ADMIN_PATH}/ward/save{if !empty($id)}/{$id}{/if}" method="POST" autocomplete="off">
        <div class="kt-portlet nh-portlet position-relative">
            <div class="kt-portlet__head">
                <div class="kt-portlet__head-label">
                    <h3 class="kt-portlet__head-title">
                        {__d('admin', 'thong_tin_co_ban')}
                    </h3>
                </div>
            </div>
            
            <div class="kt-portlet__body">
                <div class="row">
                    <div class="col-xl-6 col-lg-6">
                        <div class="form-group">
                            <label>
                                {__d('admin', 'ten_phuong_xa')}
                                 <span class="kt-font-danger">*</span>
                            </label>
                            <input name="name" value="{if !empty($ward.name)}{$ward.name|escape}{/if}" class="form-control form-control-sm" type="text" maxlength="255">
                        </div>   
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-xl-2 col-lg-3">
                        <div class="form-group">
                            <label>
                                {__d('admin', 'vi_tri')}
                            </label>
                            <input name="position" value="{if !empty($ward.position)}{$ward.position}{/if}" class="form-control form-control-sm" type="text">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <input name="district_id" value="{$district_id}" type="hidden">
    </form>
</div>
