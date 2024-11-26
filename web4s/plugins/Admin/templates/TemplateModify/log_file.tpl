<div class="kt-subheader kt-grid__item" id="kt_subheader">
    <div class="kt-container  kt-container--fluid ">
        <div class="kt-subheader__main">
            <h3 class="kt-subheader__title">
                {if !empty($title_for_layout)}{$title_for_layout}{/if}
            </h3>
        </div>

        <div class="kt-subheader__toolbar">
            <a href="{ADMIN_PATH}/template/modify/view" class="btn btn-sm btn-secondary">
                {__d('admin', 'quay_lai_danh_sach')}
            </a>
        </div>
    </div>
</div>

<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
    <div class="row">
        <div class="col-md-3 col-lg-2">
            <div class="kt-portlet nh-portlet kt-portlet--height-fluid">
                <div class="kt-portlet__head">
                    <div class="kt-portlet__head-label">
                        <h3 class="kt-portlet__head-title">
                            {if !empty($filename)}
                                {$filename}
                            {/if}
                        </h3>
                    </div>
                </div>
                <div class="kt-form">
                    <div class="kt-portlet__body p-5">
                        {if !empty($logs)}
                            <table class="table table-hover">
                                <tbody>
                                    {foreach from = $logs key = k item = log}
                                        {assign var = active value = "{if empty($k)}history-showing{/if}"}
                                        <tr nh-action="get-log" data-path="{if !empty($log.path)}{$log.path}{/if}" class="{$active} cursor-p">
                                            <td>
                                                {if !empty($log.time_label)}
                                                    <i class="fs-12">{$log.time_label}</i>
                                                {/if}
                                            </td>
                                        </tr>
                                    {/foreach}
                                </tbody>
                            </table>
                        {/if}
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-9 col-lg-10">
            <div class="kt-portlet nh-portlet kt-portlet--height-fluid">
                <div class="kt-portlet__head">
                    <div class="kt-portlet__head-label">
                        <h3 class="kt-portlet__head-title">
                            {__d('admin', 'so_sanh')}
                        </h3>
                    </div>

                    <div class="kt-portlet__head-toolbar">
                       
                    </div>
                </div>

                <div class="kt-portlet__body p-5">
                    <table class="table table-striped table-hover mb-5">
                        <thead class="thead-light">
                            <tr>
                                <th class="w-50">
                                    {__d('admin', 'ban_ghi_truoc')}
                                </th>
                                <th class="w-50 text-right">
                                    {__d('admin', 'ban_ghi_hien_tai')}
                                </th>
                            </tr>
                        </thead>
                    </table>
                    <div class="row">
                        <div class="col-12">
                            <div nh-editor="show-diff" style="width: 100%; height: 500px; position: relative;"></div>  
                        </div>
                    </div>                    
                </div>
            </div>
        </div>
    </div>
</div>

<div style="display:none;">
    <textarea id="origin-content" class="d-none">{if !empty($content)}{$content}{/if}</textarea>
    <textarea id="log-content" class="d-none">{if !empty($firt_content)}{$firt_content}{/if}</textarea>
</div>