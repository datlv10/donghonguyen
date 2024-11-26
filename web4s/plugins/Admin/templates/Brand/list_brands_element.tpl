<form id="form-apply-brands" action="{ADMIN_PATH}/setting/save/brands_category" method="POST" autocomplete="off">
    <div class="kt-portlet__head">
        <div class="kt-portlet__head-label">
            <h3 class="kt-portlet__head-title">
                {__d('admin', 'thuong_hieu')}
            </h3>
        </div>
    </div>

    <div class="kt-portlet__body">
        <div class="kt-scroll" data-scroll="true" data-height="520" nh-list-brands>
            {if !empty($brands)}
                <div class="row">
                    {foreach from=$brands key=key item=item}

                        {assign var = brand_id value = ""}
                        {if !empty($item.id)}
                            {assign var = brand_id value = $item.id}
                        {/if}

                        {assign var = checked value = ""}
                        {if !empty($brand_id) && !empty($data_apply) && in_array($brand_id,$data_apply)}
                            {assign var = checked value = "checked"}
                        {/if}
                        <div class="col-sm-6 col-12">
                            {if !empty($item.BrandsContent.name)}
                                <label class="kt-checkbox kt-checkbox--tick kt-checkbox--success">
                                    <input type="checkbox" value="{if !empty($brand_id)}{$brand_id}{/if}" name="brand[]" {$checked}> {$item.BrandsContent.name}
                                    <span></span>
                                </label>
                            {/if}
                        </div>
                    {/foreach}
                </div>
            {/if}
        </div> 
    </div>

    <div class="kt-portlet__foot">
        <div class="kt-form__actions">
            <button type="button" class="btn btn-sm btn-brand btn-save">
                {__d('admin', 'luu_thong_tin')}
            </button>
        </div>
    </div>
</form>