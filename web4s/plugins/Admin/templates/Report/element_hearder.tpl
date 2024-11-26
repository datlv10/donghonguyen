<div class="kt-subheader  kt-grid__item" id="kt_subheader">
    <div class="kt-container  kt-container--fluid ">
        <div class="kt-subheader__main">
            <h3 class="kt-subheader__title">
                {if !empty($title_for_layout)}{$title_for_layout}{/if}
            </h3>
        </div>
        <div class="kt-portlet__head-toolbar align-self-center">
            <div class="kt-portlet__head-wrapper">
                <div class="kt-portlet__head-actions">
                    <div class="dropdown dropdown-inline">
                        <button type="button" class="btn btn-default btn-icon-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="la la-download"></i> {__d('admin', 'xuat_excel')}
                        </button>
                        <div class="dropdown-menu dropdown-menu-right" style="min-width: 250px;">
                            <ul class="kt-nav">
                                <li class="kt-nav__item">
                                    <a href="javascript:;" class="kt-nav__link" nh-export="current">
                                        <span class="kt-nav__link-text">{__d('admin', 'xuat_excel_trang_hien_tai')}</span>
                                    </a>
                                </li>
                                <li class="kt-nav__item">
                                    <a href="javascript:;" class="kt-nav__link" nh-export="all">
                                        <span class="kt-nav__link-text">{__d('admin', 'xuat_excel_toan_bo_cac_trang')}</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>