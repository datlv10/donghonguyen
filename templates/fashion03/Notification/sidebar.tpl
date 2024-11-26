{strip}
	<div nh-slidebar="notification" class="sidebar-mini-cart">
	    <div class="d-flex justify-content-between align-items-center m-4">
	        <div class="font-weight-bold h3">
	            {__d('template', 'thong_bao_cua_ban')}
	        </div>
	    	<div class="sidebar-header">
	    		<a href="javascript:;" nh-slidebar-action="close" class="close-sidebar effect-rotate icon-close">
	    			<i class="fa-light fa-xmark"></i>
	    		</a>
	    	</div>
	    </div>
		<div class="box-minicart">
			<ul nh-list-notification class="cart-list list-unstyled mb-0">
				<li class="empty text-center">
					<i class="fa-light fa-brake-warning"></i>
					<div class="empty-cart">
						{__d('template', 'ban_chua_co_thong_bao_nao')}
					</div>
				</li>
			</ul>
		</div>
	</div>
{/strip}