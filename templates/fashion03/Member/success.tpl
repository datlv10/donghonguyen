{$this->element('breadcrumb', [
	'list_url' => [
		['title' => {$title_for_layout}]
	]
])}
{assign var = email value = $this->Utilities->getParamsByKey('email')}
<div class="container">
	<div class="row justify-content-center">
		<div class="col-md-8 col-12">
			<div class="text-center py-80">
				<i class="fa-light fa-check text-success fa-6x"></i>
				<h1 class="mt-10">
					<b>{__d('template', 'dang_ky_thanh_cong')}</b>
				</h1>
				<div class="alert alert-success" role="alert">
					{__d('template', 'chuc_mung_ban_da_dang_ky_thanh_cong_vui_long_kiem_tra_email_de_nhan_thong_tin_kich_hoat')}<br>
					{__d('template', 'nhan_vao')} <a href="{$this->Utilities->getUrlWebsite()}/member/verify-email{if !empty($email)}?email={$email}{/if}"><b>{__d('template', 'duong_dan')}</b></a> {__d('template', 'nay_de_chuyen_toi_trang_kich_hoat_tai_khoan')}
					
				</div>
			</div>
		</div>
	</div>
</div>