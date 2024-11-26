<div class="dropzone dropzone-multi" id="{if !empty($dropzone_params.id_dropzone)}{$dropzone_params.id_dropzone}{/if}">

    <input id="path" type="hidden" value=""/>

    <div class="dropzone-panel">
        <a class="dropzone-select btn btn-label-success btn-bold btn-sm kt-margin-r-5">
            <i class="la la-plus"></i>
            {if !empty($dropzone_params.title_dropzone)}
                {$dropzone_params.title_dropzone}
            {/if}
        </a>

        <a class="dropzone-upload btn btn-label-warning btn-bold btn-sm kt-margin-r-5">
            <i class="la la-upload"></i> 
            {__d('admin', 'tai_len_tat_ca')}
        </a>

        <a class="dropzone-remove-all btn btn-label-danger btn-bold btn-sm kt-margin-r-5">
            <i class="la la-trash-o"></i> 
            {__d('admin', 'xoa')}
        </a>

        <div class="text-muted kt-margin-t-10">
            {if !empty($dropzone_params.slogan_dropzone)}
                {$dropzone_params.slogan_dropzone}
            {/if}
        </div>
    </div>

    <div class="dropzone-items">
        <div class="dropzone-item-upload dropzone-item" style="display:none">
            <div class="dropzone-file">
                <div class="dropzone-filename" title="">
                    <span data-dz-name></span> 
                    <strong>(<span  data-dz-size></span>)</strong>
                </div>
                <div class="dropzone-error" data-dz-errormessage></div>
                <div class="dropzone-complete kt-font-success"></div>
            </div>

            <div class="dropzone-progress">
                <div class="progress">
                    <div class="progress-bar kt-bg-brand" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0" data-dz-uploadprogress></div>
                </div>
            </div>

            <div class="dropzone-toolbar">
                <span class="dropzone-start">
                    <i class="fa fa-file-upload"></i>
                </span>

                <span class="dropzone-cancel" data-dz-remove style="display: none;">
                    <i class="flaticon2-cross"></i>
                </span>

                <span class="dropzone-delete" data-dz-remove>
                    <i class="fa fa-times"></i>
                </span>
            </div>
        </div>
    </div>
</div>