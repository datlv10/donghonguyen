{strip}
{if !empty($member.avatar)}
    {assign var = avatar value = "{CDN_URL}{$this->Utilities->getThumbs($member.avatar, 350)}"}
{else}
    {assign var = avatar value = "data:image/gif;base64,R0lGODlhAQABAIAAAMLCwgAAACH5BAAAAAAALAAAAAABAAEAAAICRAEAOw=="}
{/if}
{assign var = get_path value = "{$this->getRequest()->getPath()}"}
{assign var = plugins value = $this->Setting->getListPlugins()}

{assign var = is_partner_affiliate value = false}
{if !empty($member.is_partner_affiliate) && $member.is_partner_affiliate == 1}
    {$is_partner_affiliate = true}
{/if}

<div class="h-100 bg-white p-4">
    <div class="profile-top-left">
        <div class="img-profile text-center">
            <div class="avatar-upload">
                <div class="avatar-edit">
                    <input type="file" name="avatar" nh-avatar-upload id="imageUpload" accept="image/jpeg, image/png" />
                    <label for="imageUpload">{__d('template', 'sua')}</label>
                </div>
                <div class="avatar-preview">
                    <div nh-avatar style="background-image: url({$avatar})"></div>
                </div>
            </div>
        </div>
        <div class="text-dark font-weight-bold text-center mb-5">
            {if !empty($member.full_name)}
                {$member.full_name}
            {/if}
        </div>
    </div>

    <div class="member-categories-section">
        <div class="font-weight-bold my-3">
            {__d('template', 'tai_khoan_cua_toi')}
        </div>

        <div class="{if $get_path == '/member/dashboard' || $get_path == '/member/profile'}active{/if}">
            <a href="/member/dashboard">
                <i class="fa-light fa-user mr-2"></i>
                {__d('template', 'thong_tin_ca_nhan')}
            </a>
        </div>

        <div class="{if ($get_path == '/member/address')}active{/if}">
            <a href="/member/address">
                <i class="fa-sharp fa-light fa-map-location-dot mr-2"></i>
                {__d('template', 'dia_chi_nhan_hang')}
            </a>
        </div>

        <div class="{if $get_path == '/member/order' || strpos($get_path, '/member/order/detail/') === 0}active{/if}">
            <a href="/member/order">
                <i class="fa-light fa-clipboard-list-check mr-2"></i>
                {__d('template', 'don_hang')}
            </a>
        </div>

        {if !empty($plugins.point) || !empty($plugins.affiliate)}
            <div class="font-weight-bold my-3">
                {__d('template', 'vi_cua_toi')}
            </div>
        {/if}

        {if !empty($plugins.point)}
            <div class="{if $get_path == '/member/wallet' || $get_path == '/member/money-send' || $get_path == '/member/wallet/buy-point'|| $get_path == '/member/wallet/buy-point-success' || $get_path == '/member/wallet/give-point'}active{/if}">
                <a href="/member/wallet">
                    <i class="fa-light fa-wallet mr-2"></i>
                    {__d('template', 'vi_cua_ban')}
                </a>
            </div>
        {/if}

        {if !empty($plugins.affiliate) && $is_partner_affiliate}
            <div class="{if $get_path == '/member/bank'}active{/if}">
                <a href="/member/bank">
                    <i class="fa-light fa-building-columns mr-2"></i>
                    {__d('template', 'tai_khoan_ngan_hang')}
                </a>
            </div>
        {/if}

        {if !empty($plugins.promotion) || !empty($plugins.point)}
            <div class="font-weight-bold my-3">
                {__d('template', 'qua_tang')}
            </div>
        {/if}

        {if !empty($plugins.promotion)}
            <div class="{if $get_path == '/member/promotion'}active{/if}">
                <a href="/member/promotion">
                    <i class="fa-light fa-badge-dollar mr-2"></i>
                    {__d('template', 'phieu_giam_gia')}
                </a>
            </div>
        {/if}

        {if !empty($plugins.point)}
            <div class="{if $get_path == '/member/attendance'}active{/if}">
                <a href="/member/attendance">
                    <i class="fa-light fa-gifts mr-2"></i>
                    {__d('template', 'diem_danh_nhan_qua')}
                </a>
            </div>
        {/if}

        {if !empty($plugins.affiliate)}
            <div class="font-weight-bold my-3">
                {__d('template', 'tai_khoan_doi_tac')}
            </div>

            {if $is_partner_affiliate}
                <div class="{if $get_path == '/member/affiliate/dashboard'}active{/if}">
                    <a href="/member/affiliate/dashboard">
                        <i class="fa-light fa-handshake mr-2"></i>
                        {__d('template', 'tong_quan')}
                    </a>
                </div>

                <div class="{if $get_path == '/member/affiliate/order' || strpos($get_path, '/member/affiliate/order-info/') === 0}active{/if}">
                    <a href="/member/affiliate/order">
                        <i class="fa-light fa-users-medical mr-2"></i>
                        {__d('template', 'don_gioi_thieu')}
                    </a>
                </div>

                <div class="{if $get_path == '/member/affiliate/list-point-tomoney'}active{/if}">
                    <a href="/member/affiliate/list-point-tomoney">
                        <i class="fa-sharp fa-light fa-calendar-lines-pen mr-2"></i>
                        {__d('template', 'lich_su_rut_tien')}
                    </a>
                </div>
            {else}
                <div class="{if $get_path == '/member/affiliate/active' || $get_path == '/member/affiliate/policy'}active{/if}">
                    <a href="/member/affiliate/policy">
                        <i class="fa-light fa-briefcase mr-2"></i>
                        {__d('template', 'kich_hoat_tai_khoan')}
                    </a>
                </div>
            {/if}
        {/if}
    </div>
    <a href="/member/logout" class="btn btn-submit d-block mt-4">
        {__d('template', 'dang_xuat')}
    </a>
</div>

{/strip}