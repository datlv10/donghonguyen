{assign var = list_language value = $this->LanguageAdmin->getList()}
{assign var = list_item value = []}
{if !empty($config.item)}
    {assign var = list_item value = $config.item}
{/if}
<div class="row">
    <div class="col-lg-3 col-12">
        <div class="form-group">
            <label>
                {__d('admin', 'so_bai_viet_hien_thi')}
            </label>            
            <input name="config[{NUMBER_RECORD}]" value="{if !empty($config[{NUMBER_RECORD}])}{$config[{NUMBER_RECORD}]}{/if}" class="form-control form-control-sm" type="number">
            <span class="form-text text-muted">
                {__d('admin', 'so_luong_ban_ghi_se_hien_thi_trong_block')}
            </span>
        </div>
    </div>
</div>

<div class="kt-separator kt-separator--space-lg kt-separator--border-solid mt-0 mb-20"></div>

<div class="form-group text-right">
    <span id="add-item" class="btn btn-sm btn-success">
        <i class="fa fa-plus"></i>
        {__d('admin', 'them_tab')}
    </span>
</div>

<div class="row">
    <div id="wrap-item-config" class="col-12">
        {if !empty($list_item)}
            {foreach from = $list_item item = item}
                {$this->element("../TemplateBlock/load_item_tab", [
                    'type'=> $type,
                    'item' => $item,
                    'list_language' => $list_language
                ])}
            {/foreach}
        {else}
            {$this->element("../TemplateBlock/load_item_tab", [
                'type'=> $type,
                'item' => [],
                'list_language' => $list_language
            ])}
        {/if}
    </div>
</div>