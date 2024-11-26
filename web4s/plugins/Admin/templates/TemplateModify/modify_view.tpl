<div class="kt-subheader kt-grid__item" id="kt_subheader">
    <div class="kt-container  kt-container--fluid ">
        <div class="kt-subheader__main">
            <h3 class="kt-subheader__title">
                {if !empty($title_for_layout)}{$title_for_layout}{/if}
            </h3>
        </div>

        <div class="kt-subheader__toolbar">
            <div class="btn-group">
                <button id="btn-save-file" type="button" class="btn btn-brand btn-sm btn-save-file" shortcut="112">
                    <i class="la la-edit"></i>
                    {__d('admin', 'cap_nhat')} (F1)
                </button>
            </div>
        </div>
    </div>
</div>

<div class="kt-container kt-container--fluid kt-grid__item kt-grid__item--fluid">
    <form id="main-form" action="" method="POST" autocomplete="off">
        <div class="row">
            <div class="col-lg-3">
                <div class="kt-portlet nh-portlet kt-portlet--height-fluid">
                    <div class="kt-portlet__head">
                        <div class="kt-portlet__head-label">
                            <h3 class="kt-portlet__head-title">
                                {__d('admin', 'danh_sach_thu_muc')}
                            </h3>
                        </div>
                    </div>
                    <div class="kt-form">
                        <div class="kt-portlet__body">
                            <div id="load-folder" class="tree-demo tree-folder"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-9">
                <div nh-wrap="modify-view" class="kt-portlet nh-portlet kt-portlet--height-fluid">
                    <div class="kt-portlet__head p-10">
                        <div class="kt-portlet__head-label">
                            <h3 class="kt-portlet__head-title">
                                {__d('admin', 'noi_dung')}
                            </h3>
                        </div>

                        <div class="kt-portlet__head-toolbar">
                            <div class="kt-portlet__head-actions">
                                <span nh-btn="full-screen-editor" class="btn btn-sm btn-secondary float-right">
                                    <i class="fa fa-expand"></i>
                                    {__d('admin', 'toan_man_hinh')}
                                </span>
                                
                                <span nh-btn="view-history-change-file" data-path="" class="btn btn-sm btn-secondary mr-5 float-right">
                                    <i class="fa fa-file-alt"></i>
                                    {__d('admin', 'lich_su_thay_doi_cua_tep')}
                                </span>
                                
                            </div>
                        </div>
                    </div>

                    <div class="kt-portlet__body">
                        <input id="path-file" type="hidden" value=""/>
                        <div id="editor-template" class="nh-editor"></div>
                        <span class="form-text text-muted">{__d('admin', 'noi_dung_file_chinh_sua')}</span>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<div class="modal fade" id="upload-modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{__d('admin', 'tai_file_len')}</h5>
                <button type="button" class="close" data-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                {assign var = dropzone_params value = [
                    'id_dropzone' => 'uploadFile',
                    'title_dropzone' => "{__d('admin', 'them_file')}",
                    'slogan_dropzone' => "{__d('admin', 'chi_ho_tro_dinh_dang_file')} .tpl, .po, .jpeg, .jpg, .png, .gif, .css, .js, .ttf, .eot, .woff, .svg, .woff2"
                ]}
                {$this->element('Admin.page/dropzone_upload', ['dropzone_params' => $dropzone_params])}
            </div>
        </div>
    </div>
</div>