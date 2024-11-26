<div class="kt-subheader   kt-grid__item" id="kt_subheader">
    <div class="kt-container  kt-container--fluid ">
        <div class="kt-subheader__main">
            <h3 class="kt-subheader__title">
                {if !empty($title_for_layout)}{$title_for_layout}{/if}
            </h3>
        </div>

        <div class="kt-subheader__toolbar">
            <a href="{ADMIN_PATH}/setting/dashboard" class="btn btn-sm btn-secondary">
                {__d('admin', 'quay_lai')}
            </a>

            <span class="btn btn-sm btn-brand btn-save">
                {__d('admin', 'xoa_du_lieu')}
            </span>
        </div>
    </div>
</div>

<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
    <form id="main-form" action="{ADMIN_PATH}/setting/clear-data/process" method="POST" autocomplete="off">
        <div class="kt-portlet nh-portlet">
            <div class="kt-portlet__body pb-0">
                <div class="kt-section">
                    <div class="kt-section__content">
                        <table class="table table-bordered table-hover nh-table fw-400 mb-40">
                            <thead class="thead-light">
                                <tr>
                                    <th class="w-30">
                                        {__d('admin', 'muc_luc')}
                                    </th>

                                    <th>
                                        {__d('admin', 'mo_ta')}
                                    </th>
                                </tr>
                            </thead>

                            <tbody>
                                <tr>
                                    <td>
                                        <a href="/api/website/update-search-unicode/category" target="_blank">
                                            {__d('admin', 'danh_muc')}
                                        </a>                                        
                                    </td>

                                    <td>
                                        <span class="form-text text-muted">
                                            Cập nhật Search Unicode danh mục
                                        </span>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        <a href="/api/website/update-search-unicode/products" target="_blank">
                                            {__d('admin', 'san_pham')}
                                        </a>
                                    </td>

                                    <td>
                                        <span class="form-text text-muted">
                                            Cập nhật Search Unicode sản phẩm
                                        </span>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        <a href="/api/website/update-search-unicode/article" target="_blank">
                                            {__d('admin', 'bai_viet')} 
                                        </a>
                                    </td>

                                    <td>
                                        <span class="form-text text-muted">
                                            Cập nhật Search Unicode bài viết
                                        </span>
                                    </td>
                                </tr>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>