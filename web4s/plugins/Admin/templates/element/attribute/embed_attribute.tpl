{if !empty($embed_attribute)}
    <div class="dropdown mt-10">
        <button class="btn btn-secondary dropdown-toggle" type="button" data-toggle="dropdown">
            <i class="fa fa-indent"></i>
            {__d('admin', 'ma_nhung_thuoc_tinh')}
        </button>
        <div class="dropdown-menu p-0">
            {foreach from = $embed_attribute item = item}
                {assign var = code value = "{if !empty($item.attribute)}{$item.attribute}{/if}"}
                {assign var = view value = "{if !empty($item.view)}{$item.view}{/if}"}

                {assign var = embed value = "[--embed-start--][nh-embed=\"{$code}||{$view}\"][--embed-end--]"}
                <a class="dropdown-item" href="javascript:;" nh-embed-attribute="{htmlentities($embed)}">
                    {if !empty($item.name)}
                        {$item.name}
                    {/if}
                </a>
                <div class="dropdown-divider m-0"></div>
            {/foreach}
        </div>
    </div>
{/if}