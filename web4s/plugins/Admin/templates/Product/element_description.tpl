<div class="kt-portlet nh-portlet nh-active-hover position-relative">
    <div class="kt-portlet__head">
        <div class="kt-portlet__head-label">
            <h3 class="kt-portlet__head-title">
                {__d('admin', 'mo_ta_san_pham')}
            </h3>
        </div>
    </div>

    <div class="kt-portlet__body">
        <div class="form-group">
            <label>
                {__d('admin', 'mo_ta_ngan')}
            </label>
            <div class="clearfix">
                <textarea name="description" id="description" class="mce-editor-simple">{if !empty($product.description)}{$product.description}{/if}</textarea>
            </div>
        </div>

        <div class="form-group">
            <label>
                {__d('admin', 'noi_dung')}
            </label>
            <div class="clearfix">
                <textarea name="content" id="content" class="mce-editor">{if !empty($product.content)}{$product.content}{/if}</textarea>
            </div>
            
            {$this->element('attribute/embed_attribute', ['embed_attribute' => $embed_attribute])}
        </div>

        <div class="form-group">
            <label>
                {__d('admin', 'the_bai_viet')}
            </label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text">
                        <i class="la la-tags"></i>
                    </span>
                </div>

                {assign var = tags value = []}
                {if !empty($product.tags)}
                    {foreach from = $product.tags item = tag key = k_tag}
                        {$tags[$k_tag] = $tag.name}
                    {/foreach}
                {/if}
                <input name="tags" id="tags" value="{if !empty($tags)}{htmlentities($tags|@json_encode)}{/if}" type="text" class="form-control form-control-sm tagify-input">
            </div>
            <span class="form-text text-muted">
                {__d('admin', 'chi_ho_tro_{0}_the_va_do_dai_moi_the_khong_qua_{1}_ky_tu', [10, 45])}
            </span>
        </div>
    </div>
</div>