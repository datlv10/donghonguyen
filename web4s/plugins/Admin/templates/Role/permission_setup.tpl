{assign var = url_list value = "{ADMIN_PATH}/role"}
{assign var = url_edit value = "{ADMIN_PATH}/role/permission"}

<div class="kt-subheader   kt-grid__item" id="kt_subheader">
    <div class="kt-container  kt-container--fluid ">
        <div class="kt-subheader__main">
            <h3 class="kt-subheader__title">
                {if !empty($title_for_layout)}{$title_for_layout}{/if}
            </h3>
        </div>

        <div class="kt-subheader__toolbar">
            <a href="{ADMIN_PATH}/setting/dashboard" class="btn btn-sm btn-secondary">
                {__d('admin', 'quay_lai')}
            </a>

            <span class="btn btn-sm btn-brand btn-save">
                {__d('admin', 'cap_nhat')}
            </span>
        </div>
    </div>
</div>

<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
    {if !empty($roles)}
        <form id="main-form" action="{ADMIN_PATH}/role/permission/save" method="POST" autocomplete="off">
            <div id="kt_page_portlet" class="nh-portlet-permission kt-portlet kt-portlet--responsive-mobile">
                <div class="thead-scroll kt-portlet__head kt-portlet--head-lg kt-portlet--responsive-mobile pl-10 pr-10" style="border-bottom: none;">
                    <table class="table mb-10 mt-10">
                        <thead class="thead-light">
                            <tr>
                                <th style="width:20%"></th>
                                {foreach from = $roles item = role}
                                    <th style="width: {$with_column}%;" class="text-center">
                                        {if !empty($role.name)}
                                            {$role.name}
                                        {/if}
                                    </th>
                                {/foreach}
                            </tr>
                        </thead>
                    </table>
                </div>

                <div class="kt-portlet__body pl-10 pr-10 pt-0">
                    <table class="table table-bordered table-hover mb-40 permisson-controller">
                        <tbody>
                            <tr>
                                <td style="width:20%">
                                    {__d('admin', 'chon_tat_ca')}
                                </td>

                                {foreach from = $roles item = role}
                                    <td style="width: {$with_column}%;" class="text-center">
                                        <label class="kt-checkbox h-5">
                                            <input class="check-all-column" type="checkbox"><span></span>
                                        </label>
                                    </td>
                                {/foreach}
                            </tr>
                        </tbody>
                    </table>

                    {foreach from = $permission_default key = controller_name item = permission}
                        <table class="table table-bordered table-hover mb-40 permisson-controller">
                            <thead class="thead-light">
                                <tr>
                                    <th style="width:20%">
                                        {if !empty($locales_key[{$controller_name}])}
                                            <label class="kt-font-bolder fs-14 m-0">
                                                {__d('admin', $locales_key[{$controller_name}])}
                                            </label>
                                        {/if}
                                    </th>

                                    {foreach from = $roles item = role}                                        
                                        <th style="width: {$with_column}%;" class="text-center">
                                            {* {if !empty($permission)}
                                                <label class="kt-checkbox h-5">
                                                    <input class="check-for-controller" type="checkbox">
                                                    <span></span>
                                                </label>
                                            {/if} *}
                                        </th>                                        
                                    {/foreach}
                                </tr>
                            </thead>

                            <tbody>
                                {if !empty($permission)}
                                    {foreach from = $permission key = type item = acitons}
                                        <tr>
                                            <td style="width: 20%;">
                                                {if $type == 'view'}
                                                    {__d('admin', 'xem')}
                                                {/if}

                                                {if $type == 'add'}
                                                    {__d('admin', 'them_moi')}
                                                {/if}

                                                {if $type == 'status'}
                                                    {__d('admin', 'thay_doi_trang_thai')}
                                                {/if}

                                                {if $type == 'update'}
                                                    {__d('admin', 'cap_nhat')}
                                                {/if}

                                                {if $type == 'delete'}
                                                    <label class="text-danger">
                                                        {__d('admin', 'xoa')}
                                                    </label>
                                                {/if}
                                            </td>

                                            {foreach from = $roles item = role}
                                                {assign var = permission value = []}
                                                {if !empty($role.permission) && !empty($role.permission[$controller_name])}
                                                    {$permission = $role.permission[$controller_name]}
                                                {/if}

                                                {assign var = checked value = false}
                                                {if !empty($permission[$type])}
                                                    {$checked = true}
                                                {/if}
                                                <td style="width: {$with_column}%;" class="text-center">
                                                    <label class="kt-checkbox kt-checkbox--tick kt-checkbox--success h-5">
                                                        <input name="{$role.id}[{$controller_name}][{$type}]" {if $checked}checked="true"{/if} value="1" type="checkbox">
                                                        <span></span>
                                                    </label>
                                                </td>
                                            {/foreach}
                                        </tr>
                                    {/foreach}
                                {else}
                                    <tr>
                                        <td style="width: 20%;">
                                            {__d('admin', 'tat_ca')}
                                        </td>

                                        {foreach from = $roles item = role}
                                            {assign var = permission value = []}
                                            {if !empty($role.permission) && !empty($role.permission[$controller_name])}
                                                {$permission = $role.permission[$controller_name]}
                                            {/if}

                                            {assign var = checked value = false}
                                            {if !empty($permission.all)}
                                                {$checked = true}
                                            {/if}

                                            <td style="width: {$with_column}%;" class="text-center">
                                                <label class="kt-checkbox kt-checkbox--tick kt-checkbox--success h-5">
                                                    <input name="{$role.id}[{$controller_name}][all]" {if $checked}checked="true"{/if} value="1" type="checkbox">
                                                    <span></span>
                                                </label>
                                            </td>
                                        {/foreach}
                                    </tr>
                                {/if}
                            </tbody>
                        </table>
                    {/foreach}
                </div>
            </div>
        </form>
    {else}
        <div class="kt-portlet">
            <div class="kt-portlet__body">
                <div class="row">
                    <div class="col-12">
                        <i>
                            <a href="{$url_list}">
                                {__d('admin', 'vui_long_them_quyen_tai_khoan_truoc_khi_cai_dat')}
                            </a>
                        </i>
                    </div>
                </div>
            </div>
        </div>
    {/if}
</div>