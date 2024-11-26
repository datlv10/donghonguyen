{if !empty($product.attributes_item_apply)}
    <div class="entire-attribute mb-4">
        {foreach from = $product.attributes_item_apply item = attribute key = attribute_id name = foreach_attribute}
            {assign var = attribute_code value = null}
            {if !empty($attribute.code)}
                {$attribute_code = $attribute.code}
            {/if}

            {assign var = input_type value = null}
            {if !empty($attribute.input_type)}
                {$input_type = $attribute.input_type}
            {/if}

            {assign var = has_image value = false}
            {if !empty($attribute.has_image)}
                {$has_image = true}
            {/if}
            
            <div nh-attribute="{if !empty($attribute.code)}{$attribute.code}{/if}" class="mb-2 list-attribute d-flex flex-column">
                {if $input_type == {SPECICAL_SELECT_ITEM}}
                    {if !empty($attribute.options)}
                        <div class="d-flex align-items-center justify-content-between">
                            {if !empty($attribute.name)}
                                <label>
                                    {$attribute.name}:
                                </label>
                            {/if}

                            {if $smarty.foreach.foreach_attribute.first}
                                <a nh-btn-action="clear-attribute-option" class="reset-attribute effect-border-scale d-inline-block" href="javascript:;">
                                    {__d('template', 'xoa_thuoc_tinh')}
                                </a>
                            {/if}
                        </div>
                        <div class="product-attribute-switch d-flex justify-content-start {if !empty($has_image)}image-switch{else}text-switch{/if}">
                            {foreach from = $attribute.options item = option key = attribute_option_id name = foreach_option}                                
                                {assign var = thumb_50 value = "data:image/gif;base64,R0lGODlhAQABAIAAAMLCwgAAACH5BAAAAAAALAAAAAABAAEAAAICRAEAOw=="}
                                {assign var = thumb_150 value = "data:image/gif;base64,R0lGODlhAQABAIAAAMLCwgAAACH5BAAAAAAALAAAAAABAAEAAAICRAEAOw=="}

                                {if !empty($has_image) && !empty($option.image)}
                                    {$thumb_50 = "{CDN_URL}{$this->Utilities->getThumbs($option.image, 50)}"}
                                    {$thumb_150 = "{CDN_URL}{$this->Utilities->getThumbs($option.image, 150)}"}
                                {/if}
                                
                                <div nh-attribute-option="{if !empty($option.code)}{$option.code}{/if}" {if !empty($has_image) && !empty($option.image)}data-trigger="{$thumb_150}"{/if} class="inner-product-attribute" {if !empty($has_image)}style="background-image: url('{$thumb_50}'); background-color: #fff; background-repeat: no-repeat; background-size: contain;"{/if} >
                                    {if !empty($option.name) && $has_image == false}
                                        {$option.name}
                                    {/if}
                                </div>
                            {/foreach}
                        </div>
                    {/if}
                {else}
                    {if !empty($first_item.attributes_normal) && !empty($first_item.attributes_normal[$attribute_code])}
                        {assign var = item_attribute_normal value = $first_item.attributes_normal[$attribute_code]}

                        {assign var = label_attributes_normal value = ""}
                        {if empty($attribute.name) || empty($item_attribute_normal.value_format)}
                            {assign var = label_attributes_normal value = "style='display: none;'"}
                        {/if}

                        <div class="mb-10 d-flex align-items-center justify-content-between">
                            <label nh-label-attributes-normal="{$attribute_code}" {$label_attributes_normal}>
                                {$attribute.name}:
                            </label>

                            {if $smarty.foreach.foreach_attribute.first && !empty($attribute.options)}
                                <a nh-btn-action="clear-attribute-option" class="reset-attribute effect-border-scale d-inline-block" href="javascript:;">
                                    {__d('template', 'xoa_thuoc_tinh')}
                                </a>
                            {/if}
                        </div>
                        
                        <span nh-attribute-normal="{$attribute_code}">
                            {if !empty($item_attribute_normal.value_format)}
                                {$item_attribute_normal.value_format}
                            {/if}
                        </span>
                    {/if}
                {/if}

            </div>
        {/foreach}
    </div>
{/if}