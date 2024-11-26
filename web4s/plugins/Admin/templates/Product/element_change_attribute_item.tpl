{if !empty($list_attributes_special)}
    
    {if !isset($attributes_id_selected)}
        {assign var = attributes_id_selected value = ''}
    {/if}

    {if !empty($attributes_id_selected)}
        <a id="change-attribute" class="fw-400 mb-20 d-block" href="javascript:;">
            {__d('admin', 'thay_doi_thuoc_tinh')}
        </a>
    {/if}

    <div id="wrap-select-attribute" class="{if !empty($attributes_id_selected)}collapse{/if}">
        <div class="form-group">
            <label>
                {__d('admin', 'chon_thuoc_tinh_san_pham')}
            </label>
            <div class="row">
                <div class="col-xl-8 col-lg-9">
                    {$this->Form->select('select_attribute_item', $list_attributes_special, ['id' => 'select-attribute-item', 'empty' => null, 'default' => $attributes_id_selected, 'multiple' => 'multiple', 'class' => 'form-control kt-select2'])}
                </div>

                <div class="col-lg-3 col-xl-2">
                    <button id="apply-attribute" class="col-md-12 btn btn-sm btn-brand" type="button">
                        <i class="fa fa-check"></i>
                        {__d('admin', 'ap_dung_thuoc_tinh')}
                    </button>
                </div>
            </div>
        </div>
        <div class="kt-separator kt-separator--space-lg kt-separator--border-solid mt-0 mb-20"></div>
    </div>
{/if}