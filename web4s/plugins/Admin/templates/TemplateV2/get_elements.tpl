{strip}
    {foreach from = $categories_element key = category item = item}
        <div id="nh-panel-category-{$category}" class="nh-panel-category">
            <div class="nh-panel-category-title">
                {if !empty($item.title)}
                    {$item.title}
                {/if}
            </div>
            <div class="nh-panel-category-items nh-responsive-panel">
                {if !empty($item.elements)}
                    {foreach from = $item.elements item = element}
                        <div class="nh-element-wrapper">
                            <div class="nh-element" draggable="true" data-insert-html="<i>{$element.icon} <i>">
                                <div class="icon">
                                    {if !empty($element.icon)}
                                        <i class="{$element.icon}" aria-hidden="true"></i>
                                    {/if}
                                </div>
                                <div class="nh-element-title-wrapper">                                    
                                    <div class="title">
                                        {if !empty($element.icon)}
                                            {$element.icon}
                                        {/if}
                                    </div>                                    
                                </div>
                            </div>
                        </div>
                    {/foreach}
                {/if}
            </div>
        </div>
    {/foreach}
{/strip}