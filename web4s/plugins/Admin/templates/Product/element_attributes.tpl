{if !isset($main_category_id)}
    {assign var = main_category_id value = ''}
{/if}

{assign var = attributes value = $this->AttributeAdmin->getAttributeByMainCategory($main_category_id, PRODUCT, $lang)}

{if !empty($attributes)}
    <div nh-anchor="thuoc_tinh_mo_rong" class="kt-portlet nh-portlet nh-active-hover position-relative">
        <div class="kt-portlet__head">
            <div class="kt-portlet__head-label">
                <h3 class="kt-portlet__head-title">
                    {__d('admin', 'thuoc_tinh_mo_rong')}
                </h3>
            </div>
        </div>
        
        <div class="kt-portlet__body">
            {foreach from = $attributes item = attribute key = attribute_id}
                <div class="form-group">
                    <label>
                        {if !empty($attribute.name)}
                            {$attribute.name}
                        {/if}
                        {if !empty($attribute.required)}
                            <span class="kt-font-danger">*</span>
                        {/if}
                    </label>

                    {if !empty($all_options.{$attribute_id})}
                        {$attribute.options = $all_options.{$attribute_id}}
                    {/if}

                    {if !empty($attribute.code) && !empty($product.attributes.{$attribute.code}.value)}
                        {$attribute.value = $product.attributes.{$attribute.code}.value}
                    {/if}

                    {$this->AttributeAdmin->generateInput($attribute, $lang)}
                </div>
            {/foreach}
        </div>
    </div>
{/if}