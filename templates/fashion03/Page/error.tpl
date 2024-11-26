<div class="error-page text-center py-80">
	<i class="fa-light fa-brake-warning"></i>
	<p>
		{if !empty($message)}
			{$message}.
		{else}
			{__d('template', 'xin_loi_da_co_loi_he_thong_xay_ra_ban_vui_long_quay_lai_trang_chu')}.
		{/if}	
	</p>
	<a href="/">
		{__d('template', 'trang_chu')}
	</a>
</div>