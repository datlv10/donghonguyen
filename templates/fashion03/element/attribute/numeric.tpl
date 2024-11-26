{assign var = attribute_type value = $attribute.attribute_type}
{assign var = filter_type value = "item_"}
{if !empty($attribute_type) && $attribute_type eq "product"}
    {assign var = filter_type value = "attr_"}
{/if}

<div class="d-flex justify-content-between align-items-center border-bottom border-gray pb-4 mb-4">
    {assign var = "delete_{$code}" value = "style='display: block'"}
    {if strpos($current_url, "{$filter_type}{$code}") === false}
        {assign var = "delete_{$code}" value = "style='display: none'"}
    {/if}
    <div class="text-uppercase h5 font-weight-bold mb-0">
    	{if !empty($attribute.name)}
        	{$attribute.name}
    	{/if}
    </div>
    <a {$delete_{$code}} href="javascript:;" nh-link-redirect="{$this->Utilities->addParamsToUrl($current_url, [], ["{$filter_type}{$code}"])}" class="reset-attribute border-0 color-highlight">
        {__d('template', 'xoa')}
    </a>
</div> 
<div class="product-attribute-switch d-flex justify-content-start text-switch flex-wrap mb-5">
    <a href="javascript:;" nh-link-redirect="{$this->Utilities->addParamsToUrl($current_url, ["{$filter_type}{$code}" => '500_lt'])}" nh-link-toggle="{$this->Utilities->toggleValueParamsToUrl($current_url, "{$filter_type}{$code}", '500_lt')}" class="inner-product-attribute">
        < 500
    </a>
    <a href="javascript:;" nh-link-redirect="{$this->Utilities->addParamsToUrl($current_url, ["{$filter_type}{$code}" => '500_gte-1000_lte'])}" nh-link-toggle="{$this->Utilities->toggleValueParamsToUrl($current_url, "{$filter_type}{$code}", '500_gte-1000_lte')}" class="inner-product-attribute">
        500 - 1000
    </a>
    <a href="javascript:;" nh-link-redirect="{$this->Utilities->addParamsToUrl($current_url, ["{$filter_type}{$code}" => '1000_gt'])}" nh-link-toggle="{$this->Utilities->toggleValueParamsToUrl($current_url, "{$filter_type}{$code}", '1000_gt')}" class="inner-product-attribute">
        > 1000
    </a>
</div>