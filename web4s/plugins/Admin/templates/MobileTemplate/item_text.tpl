<div class="col-xl-2 col-lg-3 col-xs-12 wrap-item">
    <div class="kt-portlet kt-portlet--mobile kt-portlet--sortable mb-10 nh-template-portlet">
        <div class="kt-portlet__body p-10">
            <label>
                <span label-code>
                    {if !empty($text_code)}
                        {$text_code}
                    {/if}
                </span>
            </label>

            <label>
                <span label-text class="kt-font-bolder">
                    {if !empty($text_name)}
                        {$text_name}
                    {/if}
                </span>
            </label>
        </div>

        <div class="kt-portlet__foot p-10">
            <button btn-edit type="button" class="btn btn-secondary btn-sm">
                <i class="fa fa-edit text-info"></i>
                {__d('admin', 'sua')}
            </button>
            <button btn-delete type="button" class="btn btn-secondary btn-sm">
                <i class="fa fa-trash-alt text-danger"></i>
                {__d('admin', 'xoa')}
            </button>
        </div>
        <input input-value type="hidden" name="text[]" value="">
    </div>
</div>