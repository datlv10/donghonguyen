{strip}
<div nh-mini-cart="sidebar" class="sidebar-mini-cart">
    <div class="title-top-cart d-flex  justify-content-between align-items-center mx-3 mx-md-5 mt-5">
        <div class="title-cart h3 font-weight-bold mb-0">
            	{__d('template', 'gio_hang_cua_ban')}
        </div>
    	<div class="sidebar-header">
    		<a href="javascript:;" nh-mini-cart="close" class="close-sidebar effect-rotate icon-close">
    			<i class="fa-light fa-xmark"></i>    		
    		</a>
    	</div>
    </div>
	<div class="content-mini-cart">
		<div class="box-minicart" nh-total-quantity-cart="0">
			<ul class="cart-list list-unstyled mb-0">
				<li class="empty text-center mt-5">
					<i class="fa-brands fa-opencart"></i>
					<div class="empty-cart">
						{__d('template', 'chua_co_san_pham_nao_trong_gio_hang')}
					</div>
				</li>
			</ul>
		</div>
	</div>
</div>
{/strip}