<div class="kt-portlet__head kt-padding-l-0 kt-padding-r-0 kt-margin-b-20">
    <div class="kt-portlet__head-label">
        <h3 class="kt-portlet__head-title">
            {__d('admin', 'thong_tin_binh_luan')}
        </h3>
    </div>
    <div class="kt-portlet__head-toolbar">
        <a href="#" class="btn btn-primary btn-sm dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
            {__d('admin', 'hanh_dong')}
        </a>
        <div class="dropdown-menu dropdown-menu-fit dropdown-menu-md dropdown-menu-right">
            <ul class="kt-nav">
                {if !empty($comment.status) && ($comment.status eq 0 || $comment.status eq 2)}
                    <li class="kt-nav__item">
                        <a href="javascript:;" class="kt-nav__link nh-change-status" 
                        data-id="{if !empty($comment.id)}{$comment.id}{/if}" 
                        data-status="1">
                            <i class="kt-nav__link-icon flaticon2-check-mark"></i>
                            <span class="kt-nav__link-text">{__d('admin', 'duyet')}</span>
                        </a>
                    </li>
                {/if}
                {if !empty($comment.status) && ($comment.status eq 1 || $comment.status eq 2)}
                    <li class="kt-nav__item">
                        <a href="javascript:;" class="kt-nav__link nh-change-status" 
                        data-id="{if !empty($comment.id)}{$comment.id}{/if}" 
                        data-status="0">
                            <i class="kt-nav__link-icon flaticon2-cross"></i>
                            <span class="kt-nav__link-text">{__d('admin', 'khong_duyet')}</span>
                        </a>
                    </li>
                {/if}
                <li class="kt-nav__item">
                    <a href="javascript:;" class="kt-nav__link btn-list-comment" 
                    data-id="{if !empty($comment.id)}{$comment.id}{/if}" 
                    data-parent-id="{if !empty($comment.parent_id)}{$comment.parent_id}{/if}">
                        <i class="kt-nav__link-icon flaticon-chat"></i>
                        <span class="kt-nav__link-text">{__d('admin', 'binh_luan_lien_quan')}</span>
                    </a>
                </li>
                <li class="kt-nav__item">
                    <a href="javascript:;" class="kt-nav__link nh-delete" 
                    data-id="{if !empty($comment.id)}{$comment.id}{/if}">
                        <i class="kt-nav__link-icon flaticon-delete-1"></i>
                        <span class="kt-nav__link-text">{__d('admin', 'xoa_binh_luan')}</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>
<div class="kt-todo__head">
    <div class="kt-section__info">
        {if !empty($comment.full_name)}
            <div class="row kt-margin-b-5">
                <div class="col-3 col-lg-3 col-xl-2">{__d('admin', 'ho_va_ten')}:</div>
                <div class="col-9 col-lg-9 col-xl-10">
                    {if !empty($comment.full_name) && !empty($comment.admin_user_id)}
                        <a href="{ADMIN_PATH}/user/detail/{$comment.admin_user_id}" class="kt-font-bolder" target="_blank">
                            {$comment.full_name}
                        </a>
                    {elseif !empty($comment.full_name) && !empty($comment.customer_account_id)}
                        <a href="{ADMIN_PATH}/customer/detail/{$comment.customer_account_id}" class="kt-font-bolder" target="_blank">
                            {$comment.full_name}
                        </a>
                    {elseif !empty($comment.full_name)}
                        <div class="kt-font-bolder d-inline-block">
                            {$comment.full_name}
                        </div>
                    {/if}
                    {if (!empty($comment.is_admin))}
                        <span class="kt-badge kt-badge--danger kt-badge--inline ml-5"> {__d('admin', 'quan_tri')}</span>
                    {/if}
                </div>
            </div>
        {/if}

        {if !empty($comment.status)}
            <div class="row kt-margin-b-5">
                <div class="col-3 col-lg-3 col-xl-2">{__d('admin', 'trang_thai')}:</div>
                <div class="col-9 col-lg-9 col-xl-10">
                    {if isset($comment.status) && $comment.status eq 0}
                        <span class="kt-badge kt-badge--danger kt-badge--inline">{__d('admin', 'khong_duyet')}</span>
                    {/if}
                    {if isset($comment.status) && $comment.status eq 1}
                        <span class="kt-badge kt-badge--success kt-badge--inline">{__d('admin', 'da_duyet')}</span>
                    {/if}
                    {if isset($comment.status) && $comment.status eq 2}
                        <span class="kt-badge kt-badge--warning kt-badge--inline">{__d('admin', 'cho_duyet')}</span>
                    {/if}
                </div>
            </div>
        {/if}

        {if !empty($comment.full_time)}
            <div class="row kt-margin-b-5">
                <div class="col-3 col-lg-3 col-xl-2">{__d('admin', 'ngay_tao')}:</div>
                <div class="col-9 col-lg-9 col-xl-10">
                    <span class="kt-font-bolder">
                        {$comment.full_time}
                    </span>
                </div>
            </div>
        {/if}
        
        {if !empty($comment.email)}
            <div class="row kt-margin-b-5">
                <div class="col-3 col-lg-3 col-xl-2">{__d('admin', 'email')}:</div>
                <div class="col-9 col-lg-9 col-xl-10">
                    <span class="kt-font-bolder">
                        {$comment.email}
                    </span>
                </div>
            </div>
        {/if}

        {if !empty($comment.phone)}
            <div class="row kt-margin-b-5">
                <div class="col-3 col-lg-3 col-xl-2">{__d('admin', 'so_dien_thoai')}:</div>
                <div class="col-9 col-lg-9 col-xl-10">
                    <span class="kt-font-bolder">
                        {$comment.phone}
                    </span>
                </div>
            </div>
        {/if}

        {if !empty($comment.url)}
            <div class="row kt-margin-b-5">
                <div class="col-3 col-lg-3 col-xl-2">{__d('admin', 'duong_dan')}:</div>
                <div class="col-9 col-lg-9 col-xl-10">
                    <a target="_blank" href="/{$comment.url}">
                        <i class="fa fa-external-link-alt"></i> {__d('admin', 'xem_tai_day')}
                    </a>
                </div>
            </div>
        {/if}

        {if !empty($comment.number_like)}
            <div class="row kt-margin-b-5">
                <div class="col-3 col-lg-3 col-xl-2">{__d('admin', 'ua_thich')}:</div>
                <div class="col-9 col-lg-9 col-xl-10">
                    <span class="kt-font-bolder">
                        {$comment.number_like} like
                    </span>
                </div>
            </div>
        {/if}

        {if !empty($comment.ip)}
            <div class="row kt-margin-b-5">
                <div class="col-3 col-lg-3 col-xl-2">{__d('admin', 'dia_chi_ip')}:</div>
                <div class="col-9 col-lg-9 col-xl-10">
                    <span class="kt-font-bolder">
                        {$comment.ip}
                    </span>
                </div>
            </div>
        {/if}
        {if !empty($comment.type_comment) && $comment.type_comment eq 'rating'}
            <div class="row kt-margin-b-5">
                <div class="col-3 col-lg-3 col-xl-2">{__d('admin', 'danh_gia')}:</div>
                <div class="col-9 col-lg-9 col-xl-10">
                    <div class="star-rating">
                        <span style="width:{math equation="x * y" x=$comment.rating|intval y=20}%"></span>
                    </div>
                </div>
            </div>
        {/if}
        {if !empty($comment.type_comment)}
            <input id="type-comment" type="hidden" value="{$comment.type_comment}">
            <div class="kt-separator kt-separator--space-lg kt-separator--border-dashed kt-margin-t-15 kt-margin-b-15"></div>
        {/if}
    </div>
</div>



<div class="kt-todo__body p-0">
    <h6>{__d('admin', 'binh_luan')}:</h6>

    {if !empty($comment.content)}
        <div class="kt-todo__text scroll-comment">
            {$comment.content}
        </div>
    {/if}

    {if !empty($comment.images)}
        <div class="d-flex flex-wrap kt-margin-t-10">
            {foreach from = $comment.images item = item}
                <a class="kt-media kt-media--xl  kt-margin-r-5 kt-margin-t-5" href="{CDN_URL}{$item}" data-lightbox="view-comment">
                    <img class="img-fluid img-cover" src="{CDN_URL}{$item}" alt="image" style="width: 80px">
                </a>
            {/foreach}
        </div>
    {/if}
</div>