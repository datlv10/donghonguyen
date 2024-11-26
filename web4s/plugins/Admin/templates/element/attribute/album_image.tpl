{assign var = languages value = $this->LanguageAdmin->getList()}

{assign var = albums value = []}
{if !empty($value)}
    {$albums = $value|json_decode:1}
{/if}

<div attribute-code="{$code}" class="wrap-manager">
    <div class="form-group">
        <span id="add-item" class="btn btn-sm btn-success">
            <i class="fa fa-plus"></i>
            {__d('admin', 'them_ban_ghi')}
        </span>
    </div>
    <div class="list-item">
        {if !empty($albums)}
            {foreach from = $albums key = index item = album}
                {$this->element('Admin.attribute/album_image_item', [
                    'code' => $code,
                    'languages' => $languages,
                    'album' => $album,
                    'index' => $index
                ])}
            {/foreach}
        {else}
            {$this->element('Admin.attribute/album_image_item', [
                'code' => $code,
                'languages' => $languages,
                'album' => [],
                'index' => 0
            ])}
        {/if}
    </div>
</div>