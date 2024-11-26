{assign var = list_language value = $this->LanguageAdmin->getList()}

{assign var = list_item value = []}
{if !empty($config.item)}
    {assign var = list_item value = $config.item}
{/if}

<div class="kt-separator kt-separator--space-lg kt-separator--border-solid mt-0 mb-20"></div>

<div class="form-group text-right">
    <span id="add-item" class="btn btn-sm btn-success">
        <i class="fa fa-plus"></i>
        {__d('admin', 'them_menu')}
    </span>
</div>

<div class="row">
    <div id="wrap-item-config" class="col-lg-12">
        {if !empty($list_item)}
            {foreach from = $list_item item = item}
                {$this->element("../TemplateBlock/load_item_menu", [
                    'item' => $item,
                    'list_language' => $list_language
                ])}
            {/foreach}
        {else}
            {$this->element("../TemplateBlock/load_item_menu", [
                'item' => [],
                'list_language' => $list_language
            ])}
        {/if}
    </div>
</div>