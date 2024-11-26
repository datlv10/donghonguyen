<div class="kt-subheader   kt-grid__item" id="kt_subheader">
    <div class="kt-container  kt-container--fluid ">
        <div class="kt-subheader__main">
            <h3 class="kt-subheader__title">
                {if !empty($title_for_layout)}{$title_for_layout}{/if}
            </h3>
        </div>

        <div class="kt-subheader__toolbar">
            {if !empty($url_list)}
                <a href="{$url_list}" class="btn btn-sm btn-secondary">
                    {__d('admin', 'quay_lai_danh_sach')}
                </a>
            {/if}

            {if !empty($url_edit) || !empty($url_add)}
                <div class="btn-group">
                    {if empty($id)}
                        <button id="btn-save" type="button" class="btn btn-sm btn-brand btn-save" shortcut="112">
                            <i class="la la-plus"></i>
                            {__d('admin', 'them_moi')} (F1)
                        </button>
                    {else}
                        <button id="btn-save" type="button" class="btn btn-sm btn-brand btn-save" shortcut="112">
                            <i class="la la-edit"></i>
                            {__d('admin', 'cap_nhat')} (F1)
                        </button>
                    {/if}
                    
                    <button type="button" class="btn btn-brand dropdown-toggle dropdown-toggle-split" data-toggle="dropdown"></button>
                    <div class="dropdown-menu dropdown-menu-right">
                        <ul class="kt-nav p-0">
                            {if !empty($url_edit)}
                                <li class="kt-nav__item">
                                    <span data-link="{$url_edit}" data-update="1" class="kt-nav__link btn-save">
                                        <i class="kt-nav__link-icon flaticon2-writing"></i>
                                        <span class="kt-nav__link-text">
                                            {__d('admin', 'luu_&_tiep_tuc')}
                                        </span>
                                    </span>
                                </li>
                            {/if}

                            {if !empty($url_add)}
                                <li class="kt-nav__item">
                                    <span data-link="{$url_add}" class="kt-nav__link btn-save">
                                        <i class="kt-nav__link-icon flaticon2-medical-records"></i>
                                        <span class="kt-nav__link-text">
                                            {__d('admin', 'luu_&_them_moi')}
                                        </span>
                                    </span>
                                </li>
                            {/if}

                            {if !empty($url_list)}
                                <li class="kt-nav__item">
                                    <span data-link="{$url_list}" class="kt-nav__link btn-save">
                                        <i class="kt-nav__link-icon flaticon2-hourglass-1"></i>
                                        <span class="kt-nav__link-text">
                                            {__d('admin', 'luu_&_quay_lai')}
                                        </span>
                                    </span>
                                </li>
                            {/if}
                        </ul>
                    </div>
                </div>
            {/if}

            {if !empty($show_lang)}
                {$this->element('Admin.page/language')}
            {/if}
        </div>
    </div>
</div>