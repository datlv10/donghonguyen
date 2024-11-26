<div class="kt-radio-inline mt-5">
    <label class="kt-radio kt-radio--tick kt-radio--success mr-20">
        <input type="radio" name="{if !empty($code)}{$code}{/if}" value="1" {if !empty($value)}checked{/if} class="{$class}">
            {__d('admin', 'co')}
        <span></span>
    </label>

    <label class="kt-radio kt-radio--tick kt-radio--danger mr-20">
        <input type="radio" name="{if !empty($code)}{$code}{/if}" value="0" {if empty($value)}checked{/if} class="{$class}"> 
            {__d('admin', 'khong')}
        <span></span>
    </label>
</div>