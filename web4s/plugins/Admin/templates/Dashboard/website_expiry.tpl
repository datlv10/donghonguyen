<div class="kt-portlet__head">
    <div class="kt-portlet__head-label">
        <h3 class="kt-portlet__head-title">
            {__d('admin', 'han_su_dung')}
        </h3>
    </div>
    <div class="kt-portlet__head-toolbar">
        <a href="#" class="btn btn-sm btn-label-danger btn-bold">
            {__d('admin', 'lien_he_gia_han')}
        </a>
    </div>
</div>

<div class="kt-portlet__body">
    <div class="kt-widget5">
        <div class="kt-widget5__item mb-0 pb-0">
            <div class="kt-widget5__content">
                <div class="kt-widget5__pic">
                    <i class="flaticon-calendar-with-a-clock-time-tools" style="font-size: 3rem;"></i>
                </div>
                <div class="kt-widget5__section">
                    <span class="kt-widget5__title">
                        {__d('admin', 'thoi_gian_ket_thuc')}
                    </span>
                    <p class="kt-widget5__desc">
                        {if !empty($profile_info.end_date)}
                            {$this->Utilities->convertIntgerToDateString($profile_info.end_date, 'd/m/Y')}
                        {else}
                            {__d('admin', 'chua_xac_dinh')}
                        {/if}
                    </p>
                </div>
            </div>
            <div class="kt-widget5__content">
                <div class="kt-widget5__stats pr-0">
                    <span class="kt-widget5__title">
                        {__d('admin', 'thoi_gian_con_lai')}
                    </span>
                    <span class="kt-widget5__number kt-font-danger">
                        {if !empty($duedate)}
                            {$duedate} {__d('admin', 'ngay')}
                        {elseif isset($duedate)}
                            {__d('admin', 'trong_ngay_hom_nay')}
                        {else}
                            {__d('admin', 'het_han')}
                        {/if}
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>