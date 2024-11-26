<div class="kt-subheader   kt-grid__item" id="kt_subheader">
    <div class="kt-container  kt-container--fluid ">
        <div class="kt-subheader__main">
            <h3 class="kt-subheader__title">
                {if !empty($title_for_layout)}{$title_for_layout}{/if}
            </h3>
        </div>
        <div class="kt-subheader__toolbar">
            <a href="{ADMIN_PATH}/setting/dashboard-attribute" class="btn btn-sm btn-secondary">
                {__d('admin', 'quay_lai')}
            </a>
        </div>
    </div>
</div>

<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
    <form id="main-form" action="{ADMIN_PATH}/setting/save/{$group}" method="POST" autocomplete="off">
        <div class="kt-portlet nh-portlet">
            <div class="kt-portlet__head">
                <div class="kt-portlet__head-label">
                    <h3 class="kt-portlet__head-title">
                        {if $type == {ARTICLE}}
                            <i class="fa fa-file-alt mr-5"></i>
                        {else}
                            <i class="fa fa-dice-d6 mr-5"></i>
                        {/if}
                        {__d('admin', 'them_ma_nhung_thuoc_tinh')}
                    </h3>
                </div>
            </div>
            
            <div class="kt-portlet__body">
                <div class="form-group">
                    <label>
                        {__d('admin', 'them_ma_nhung_thuoc_tinh')}
                    </label>
                    <div class="kt-radio-inline mt-5">
                        <label class="kt-radio kt-radio--tick kt-radio--success mr-20">
                            <input type="radio" name="use_embed_attribute" value="1" {if !empty($setting.use_embed_attribute)}checked{/if}> 
                                {__d('admin', 'co')}
                            <span></span>
                        </label>

                        <label class="kt-radio kt-radio--tick kt-radio--danger mr-20">
                            <input type="radio" name="use_embed_attribute" value="0" {if !isset($setting.use_embed_attribute) || empty($setting.use_embed_attribute)}checked{/if}> 
                                {__d('admin', 'khong')}
                            <span></span>
                        </label>
                    </div>
                </div>

                <div class="kt-separator kt-separator--space-lg kt-separator--border-solid mt-10 mb-20"></div>

                <div class="row">
                    <div class="col-lg-3 col-xl-3 col-sm-3 col-12">
                        <div class="form-group">
                            <label>
                                {__d('admin', 'thuoc_tinh')}
                            </label>
                            <select id="attribute" class="form-control form-control-sm kt-selectpicker">
                                <option value="">{__d('admin', 'chon')}</option>
                                {if !empty($attributes)}
                                    {foreach from = $attributes key = code item = name}
                                        <option value="{$code}">{$name}</option>
                                    {/foreach}
                                {/if}
                            </select>
                        </div>
                    </div>

                    <div class="col-lg-3 col-xl-3 col-sm-3 col-12">
                        <div class="form-group">
                            <label>
                                View Embed
                            </label>
                            <select id="view" class="form-control form-control-sm kt-selectpicker">
                                <option value="">{__d('admin', 'chon')}</option>
                                {if !empty($views)}
                                    {foreach from = $views item = view}
                                        <option value="{$view}">{$view}</option>
                                    {/foreach}
                                {/if}
                            </select>
                        </div>
                    </div>

                    <div class="col-lg-4 col-xl-4 col-sm-4 col-12">
                        <div class="form-group">
                            <label>
                                {__d('admin', 'ten_ma_nhung')}
                            </label>
                            <input id="name" value="" class="form-control form-control-sm" type="text">
                        </div>
                    </div>

                    <div class="col-lg-2 col-xl-2 col-sm-2 col-12">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <div class="clearfix">
                                <span id="btn-add-attribute" class="btn btn-sm btn-success mt-25">
                                    {__d('admin', 'them_ma_nhung')}
                                </span>
                            </div>                            
                        </div>
                        
                    </div>
                </div>
                
                {assign var = config_embed_attribute value = []}
                {if !empty($setting.config_embed_attribute)}
                    {$config_embed_attribute = $setting.config_embed_attribute|json_decode:1}
                {/if}

                <table id="table-attribute" class="table">
                    <thead class="thead-light">
                        <tr>
                            <th class="w-40">
                                {__d('admin', 'ten_ma_nhung')}
                            </th>
                            <th class="w-30">
                                {__d('admin', 'thuoc_tinh')}
                            </th>
                            <th class="w-20">View</th>
                            <th class="w-10"></th>
                        </tr>
                    </thead>
                    <tbody>
                        {if !empty($config_embed_attribute)}
                            {foreach from = $config_embed_attribute item = config}
                                {assign var = attribute value = "{if !empty($config.attribute)}{$config.attribute}{/if}"}
                                {assign var = attribute_name value = "{if !empty($config.attribute_name)}{$config.attribute_name}{/if}"}
                                {assign var = view value = "{if !empty($config.view)}{$config.view}{/if}"}
                                {assign var = name value = "{if !empty($config.name)}{$config.name}{/if}"}

                                <tr nh-attribute="{$attribute}" nh-attribute-name="{$attribute_name}" nh-view="{$view}" nh-name="{$name}">
                                    <td class="kt-font-bolder">{$name}</td>
                                    <td>{$attribute_name}</td>
                                    <td>{$view}</td>
                                    <td class="text-right">
                                        <i class="fa fa-trash-alt cursor-p text-danger" nh-delete-attribute></i>
                                    </td>
                                </tr>
                            {/foreach}
                        {else}
                            <tr class="no-attribute">
                                <td colspan="3" class="text-center">
                                    <i class="fs-12">
                                        {__d('admin', 'chua_co_cau_hinh')}
                                    </i>
                                </td>
                            </tr>
                        {/if}                        
                    </tbody>
                </table>

                <input name="config_embed_attribute" type="hidden" value="{if !empty($setting.config_embed_attribute)}{htmlentities($setting.config_embed_attribute)}{/if}">

                <div class="form-group mb-0">
                    <span class="btn btn-sm btn-brand btn-save">
                        {__d('admin', 'luu_cau_hinh')}
                    </span>
                </div>
            </div>
        </div>
    </form>
</div>