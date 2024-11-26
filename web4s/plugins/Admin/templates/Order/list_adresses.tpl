<div id="warp-list-address" class="kt-widget5 nh-widget-hover">
    <div class="clearfix">
        <span class="fs-13">
            {__d('admin', 'thay_doi_dia_chi')}
        </span>
        <a id="add-address" class="fs-13 float-right" href="javascript:;">
            {__d('admin', 'them_dia_chi_moi')}
        </a>
    </div>

    <div class="kt-separator kt-separator--space-lg kt-separator--border-soild mt-10 mb-10"></div>
    <div class="row {if !empty($list_address) && $list_address|@count > 3}kt-scroll{/if}" data-scroll="true" data-height="250" data-scrollbar-shown="true">
        {if !empty($list_address)}
            {foreach from = $list_address item = address}
                <div class="kt-widget5__item p-0 m-0" data-address="{htmlentities($address|@json_encode)}">
                    <div class="kt-widget5__content text-left pl-10 pr-10">
                        <div class="kt-widget5__section col-12 p-0">
                            {if !empty($address.name)}
                                <div class="kt-widget5__desc">
                                    {$address.name}
                                </div>
                            {/if}

                            {if !empty($address.phone)}
                                <div class="kt-widget5__desc">
                                    {$address.phone}
                                </div>
                            {/if}

                            {if !empty($address.full_address)}
                                <div class="kt-widget5__desc">
                                    {$address.full_address}
                                </div>
                            {/if}

                            <div class="kt-widget5__desc">
                                <a class="edit-address" href="javascript:;">
                                    {__d('admin', 'sua')}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            {/foreach}
        {/if}  
    </div>  
</div>

