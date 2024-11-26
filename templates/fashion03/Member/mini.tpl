{assign member_info value = $this->Member->getMemberInfo()}
<div class="dropdown show">
    <a class="text-white" href="javascript:;" role="button" id="member-info" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        {$member_info.full_name|truncate:15:'...':true:true}
    </a>

    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="member-info">
        <a class="dropdown-item py-3" href="/member/dashboard">
            <i class="fa-light fa-user mr-3"></i>
            {__d('template', 'thong_tin_ca_nhan')}
        </a>
        
        <a class="dropdown-item py-3" href="/member/order">
            <i class="fa-light fa-clipboard-list-check mr-3"></i>
            {__d('template', 'quan_ly_don_hang')}
        </a>
        
        <a class="dropdown-item py-3" href="/member/change-password">
            <i class="fa-light fa-lock-keyhole mr-3"></i>
            {__d('template', 'thay_doi_mat_khau')}
        </a>

        <a class="dropdown-item py-3" href="/member/logout">
            <i class="fa-light fa-right-from-bracket mr-3"></i>
            {__d('template', 'thoat')}
        </a>
    </div>
</div>