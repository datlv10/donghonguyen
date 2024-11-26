{assign var = url_list value = "{ADMIN_PATH}/notification"}
{assign var = url_add value = "{ADMIN_PATH}/notification/add"}
{assign var = url_edit value = "{ADMIN_PATH}/notification/update"}

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
    <form id="main-form" action="{ADMIN_PATH}/notification/save{if !empty($id)}/{$id}{/if}" method="POST" autocomplete="off">
        <div class="kt-portlet nh-portlet">
            <div class="kt-portlet__head">
                <div class="kt-portlet__head-label">
                    <h3 class="kt-portlet__head-title">
                        {__d('admin', 'thong_tin_co_ban')}
                    </h3>
                </div>
            </div>
            
            <div class="kt-portlet__body">
                <div class="row">
                    <div class="col-lg-4 col-xs-3">
                        <div class="form-group">
                            <label>
                                {__d('admin', 'loai_thong_bao')}
                            </label>
                            {assign var = type value = 'all'}
                            {if !empty($notification.type)}
                                {$type = $notification.type}
                            {/if}

                            {$this->Form->select('type', $this->NotificationAdmin->listTypeNotification(), ['id' => 'type' ,'empty' => null, 'default' => $type, 'class' => 'form-control form-control-sm kt-selectpicker'])}
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label>
                        {__d('admin', 'tieu_de')}
                        <span class="kt-font-danger">*</span>
                    </label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">
                                <i class="fa fa-bell w-20px"></i>
                            </span>
                        </div>
                        <input name="title" value="{if !empty($notification.title)}{$notification.title}{/if}" class="form-control form-control-sm" type="text">
                    </div>
                </div>

                <div class="form-group">
                    <label>
                        {__d('admin', 'noi_dung')}
                        <span class="kt-font-danger">*</span>
                    </label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">
                                <i class="fa fa-file-alt w-20px"></i>
                            </span>
                        </div>
                        <input name="body" value="{if !empty($notification.body)}{$notification.body}{/if}" class="form-control form-control-sm" type="text">
                    </div>
                </div>

                <div class="form-group">
                    <label>
                        {__d('admin', 'anh_chinh')}
                    </label>
                    <div class="clearfix">
                        {assign var = image value = ''}
                        {if !empty($notification.image)}
                            {assign var = image value = "background-image: url('{CDN_URL}{$notification.image}');background-size: contain;background-position: 50% 50%;"}
                        {/if}

                        {assign var = url_select_image value = "{CDN_URL}/myfilemanager/?type_file=image&cross_domain=1&token={$access_key_upload}&field_id=image&lang={LANGUAGE_ADMIN}"}

                        <div class="kt-avatar kt-avatar--outline kt-avatar--circle- {if !empty($image)}kt-avatar--changed{/if}">
                            <a {if !empty($notification.image)}href="{CDN_URL}{$notification.image}"{/if} target="_blank" class="kt-avatar__holder d-block" style="{$image}"></a>
                            <label data-src="{$url_select_image}" class="kt-avatar__upload btn-select-image" data-toggle="kt-tooltip" data-original-title="{__d('admin', 'chon_anh')}" data-type="iframe">
                                <i class="fa fa-pen"></i>
                            </label>
                            <span class="kt-avatar__cancel btn-clear-image" data-toggle="kt-tooltip" data-original-title="{__d('admin', 'xoa_anh')}">
                                <i class="fa fa-times"></i>
                            </span>

                            <input id="image" name="image" value="{if !empty($notification.image)}{htmlentities($notification.image)}{/if}" type="hidden" />
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label>
                        Icon
                    </label>
                    <div class="clearfix">
                        {assign var = icon_bg value = ''}
                        {if !empty($icon_default)}
                            {assign var = icon_bg value = "background-image: url('{CDN_URL}{$icon_default}');background-size: contain;background-position: 50% 50%;"}
                        {/if}

                        {if !empty($notification.icon)}
                            {assign var = icon_bg value = "background-image: url('{CDN_URL}{$notification.icon}');background-size: contain;background-position: 50% 50%;"}
                        {/if}

                        {assign var = icon value = ''}
                        {if !empty($icon_default)}
                            {assign var = icon value = $icon_default}
                        {/if}

                        {if !empty($notification.icon)}
                            {assign var = icon value = $notification.icon}
                        {/if}

                        {assign var = url_select_icon value = "{CDN_URL}/myfilemanager/?type_file=image&cross_domain=1&token={$access_key_upload}&field_id=icon&lang={LANGUAGE_ADMIN}"}

                        <div class="kt-avatar kt-avatar--outline kt-avatar--circle- {if !empty($icon)}kt-avatar--changed{/if}">
                            <a {if !empty($icon)}href="{CDN_URL}{$icon}"{/if} target="_blank" class="kt-avatar__holder d-block" style="{$icon_bg}"></a>
                            <label data-src="{$url_select_icon}" class="kt-avatar__upload btn-select-image" data-toggle="kt-tooltip" data-original-title="{__d('admin', 'chon_anh')}" data-type="iframe">
                                <i class="fa fa-pen"></i>
                            </label>
                            <span class="kt-avatar__cancel btn-clear-image" data-toggle="kt-tooltip" data-original-title="{__d('admin', 'xoa_anh')}">
                                <i class="fa fa-times"></i>
                            </span>

                            <input id="icon" name="icon" value="{if !empty($icon)}{htmlentities($icon)}{/if}" type="hidden" />
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label>
                        {__d('admin', 'duong_dan')} (Website)
                    </label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text kt-font-bolder w-auto">
                                {$this->UtilitiesAdmin->getUrlWebsite()}/
                            </span>
                        </div>
                        <input name="link" value="{if !empty($notification.link)}{$notification.link}{/if}" class="form-control form-control-sm" type="text">
                    </div>
                </div>

                <div class="form-group">
                    <label>
                        Mobile Action (IOS/Android)
                    </label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">
                                <i class="fa fa-mobile-alt w-20px"></i>
                            </span>
                        </div>
                        <input name="mobile_action" value="{if !empty($notification.mobile_action)}{htmlentities($notification.mobile_action)}{/if}" class="form-control form-control-sm" type="text">
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>