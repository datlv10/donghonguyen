{if !empty($structure)}
    <div class="row mb-10">
        <div class="col-6">
            {if !empty($layout_info.name)}
                {__d('admin', 'dang_su_dung_trang_bo_cuc')} 
                <b> {$layout_info.name} </b>
            {/if}
        </div>
        <div class="col-6 text-right">
            <a target="_blank" href="/{$url}?nh-config-block=1" class="kt-link kt-font-bolder kt-link--info">
                {__d('admin', 'xem_cau_hinh_thuc_te')}
            </a>
        </div>
    </div>

    {foreach from = $structure item = rows key = type}
        <div class="kt-portlet nh-template-portlet">
            <div class="kt-portlet__head p-10">
                <div class="kt-portlet__head-label">
                    <h3 class="kt-portlet__head-title">
                        {if $type == {HEADER}}
                            {__d('admin', 'dau_trang')}
                        {/if}

                        {if $type == {CONTENT}}
                            {__d('admin', 'giua_trang')}
                        {/if}

                        {if $type == {FOOTER}}
                            {__d('admin', 'cuoi_trang')}
                        {/if}
                    </h3>
                </div>

                <div class="kt-portlet__head-toolbar">
                    <div class="kt-portlet__head-group">
                        <span class="btn btn-sm btn-icon btn-secondary bg-white btn-icon-md m-0 nh-btn-toggle-row d-none" data-toggle="kt-tooltip" data-placement="top" title="{__d('admin', 'dong_mo')}">
                            <i class="la la-angle-down"></i>
                        </span>
                    
                        <span class="btn btn-sm btn-success btn-icon-md m-0 nh-btn-add-row" data-toggle="kt-tooltip" data-placement="top" title="{__d('admin', 'them_dong_moi')}" >
                            <i class="la la-plus"></i>
                            {__d('admin', 'them_dong_moi')}
                        </span>
                    </div>
                </div>
            </div>

            <div class="kt-portlet__body p-0">
                <div class="kt-portlet__content p-10">
                    <div class="row">
                        <div class="col-12 wrap-list-item" data-type-row="{$type}">
                            {if !empty($rows)}
                                {foreach from = $rows item = row}
                                    {if !empty($row.columns)}
                                        <div data-code="{if !empty($row.code)}{$row.code}{/if}" data-config="{if !empty($row.config)}{htmlentities($row.config|@json_encode)}{/if}" class="row nh-row-item">
                                            {foreach from = $row.columns item = column}                                               
                                                {assign var = column_value value = "{if !empty($column.column_value)}{$column.column_value}{/if}"}
                                                <div data-column-value="{$column_value}" class="nh-column-item col-{$column_value}">
                                                    <div class="nh-content-column">
                                                        {if !empty($column.blocks)}
                                                            {foreach from = $column.blocks item = block_code}
                                                                {assign var = block_info value = []}
                                                                {if !empty($blocks[$block_code])}
                                                                    {assign var = block_info value = $blocks[$block_code]}
                                                                {/if}

                                                                {if !empty($block_info)}
                                                                    <li data-code="{$block_code}" class="block-item {if empty($block_info.status)}disable{/if}">
                                                                        <i class="fa fa-file-alt w-20px fs-14"></i>
                                                                        <span>
                                                                            {if !empty($block_info.name)}{$block_info.name}{/if}
                                                                        </span>
                                                                    </li>
                                                                {/if}
                                                            {/foreach}
                                                        {/if}
                                                    </div>
                                                </div>
                                            {/foreach}
                                        </div>
                                    {/if}
                                {/foreach}
                            {/if}
                        </div>                                    
                    </div>                                    
                </div>
            </div>
        </div>
    {/foreach}
{/if}