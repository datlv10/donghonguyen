{assign var = data_type value = ''}
{if !empty($config[{DATA_TYPE}])}
    {assign var = data_type value = $config[{DATA_TYPE}]}
{/if}

<div class="row">
    <div class="col-lg-6 col-12">
        <div class="form-group">
            <label>
                {__d('admin', 'lay_du_lieu_theo')}
            </label>
            <select id="{DATA_TYPE}" name="config[{DATA_TYPE}]" class="form-control form-control-sm kt-selectpicker">
                <option value="{BY_URL}" {if $data_type == {BY_URL}}selected="true"{/if}>
                    {__d('admin', 'lay_tu_dong_theo_duong_dan')}
                </option>

                <option value="{ARTICLE}" {if $data_type == {ARTICLE}}selected="true"{/if}>
                    {__d('admin', 'bai_viet')}
                </option>
            </select>
        </div>
    </div>
</div>

{assign var = data value = []}
{if !empty($config.data_ids)}
    {assign var = data value = $config.data_ids}
{/if}

<div id="wrap-view-data">
    {if !empty($data_type)}        
        {$this->element("../TemplateBlock/load_view_data", ['data_type' => $data_type, 'block_type' => $type, 'data' => $data])}        
    {/if}
</div>