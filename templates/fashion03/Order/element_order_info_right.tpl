{assign var = coupon_info value = []}
{if !empty($order_info.coupon)}
	{assign var = coupon_info value = $order_info.coupon}
{/if}

{assign var = config_point value = $this->Setting->getSettingWebsite('point')}

{assign var = point_to_money value = 1}
{if !empty($config_point.point_to_money)}
	{assign var = point_to_money value = $config_point.point_to_money}
{/if}

{assign var = point_promotion_max value = 1}
{if !empty($member_info.point_promotion)}
	{assign var = point_promotion_max value = $member_info.point_promotion}
{/if}

{assign var = point_max value = 1}
{if !empty($member_info.point)}
	{assign var = point_max value = $member_info.point}
{/if}

{assign var = status_pay_by_point value = 0}
{if !empty($config_point.pay_by_point)}
	{assign var = status_pay_by_point value = $config_point.pay_by_point}
{/if}

{assign var = plugins value = $this->Setting->getListPlugins()}

<div class="order-review">
	<div class="entry-order-review">
		<div class="cart-drop-botoom">
		   <div id="accordion-order">
		   		{if !empty($plugins.promotion)}
			        <div class="card bg-white mb-3">
			            <div class="card-header">
			                <div class="d-flex justify-content-between align-items-center cursor-pointer mb-3 py-3 px-4" data-toggle="collapse" data-target="#coupon-panel" aria-expanded="true">
			                    <span class="d-flex align-items-center">
			                    	<i class="fa-light fa-ticket color-highlight"></i>
			                        <div class="pl-2 mb-0 font-weight-bold">
			                            {__d('template', 'phieu_giam_gia')}
			                        </div>
			                    </span>

			                    <span class="d-flex align-items-center" >
			                    	{if !empty($coupon_info.coupon)}
				                        <strong class="pr-3 text-success">
				                            {$coupon_info.coupon}
				                        </strong>
				                    {/if}
			                        <span >
			                            <i class="fa-light fa-angle-down"></i>
			                        </span>
			                    </span>
			                </div>
			            </div>
			    
			            <div id="coupon-panel" class="collapse" data-parent="#accordion-order">
			                <div class="pb-4 pl-4 pr-4">
	                			<div class="form-group mb-3">
				                    <input id="order-coupon-code" value="" type="text" class="bg-white border form-control rounded " placeholder="{__d('template', 'nhap_phieu_giam_gia')}" >
			                    </div>
			                    {if !empty($coupon_info.total)}
		                            <small class="font-weight-bold text-success mb-3 d-block">
			                    		{__d('template', 'chiet_khau')}:
			                    		{$coupon_info.total|number_format:0:".":","} {CURRENCY_UNIT}
			                    	</small>
		                        {/if}

			                    <span nh-btn-action="apply-coupon" class="btn btn-submit w-100 mb-3">
			                        {__d('template', 'ap_dung')}
			                    </span>

			                    <div class="d-flex justify-content-between align-items-center mb-3">
			                    	<a class="btn btn-submit-1" href="javascript:;" nh-btn-action="list-coupon">
			                    		<i class="fa-light fa-ticket-simple"></i>
				                        {__d('template', 'lay_phieu_giam_gia')}
				                    </a>
				                    {if !empty($coupon_info.coupon)}
				                    	<a href="javascript:;" nh-btn-action="delete-coupon" class="text-dark">
					                        {__d('template', 'huy_phieu_giam_gia')}
					                    </a>
				                    {/if}
			                    </div>
			                </div>
			            </div>
			        </div>
			        <input name="coupon" value="{if !empty($coupon_info.coupon)}{$coupon_info.coupon}{/if}" type="hidden">
			        <input name="promotion_id" value="{if !empty($coupon_info.promotion_id)}{$coupon_info.promotion_id}{/if}" type="hidden">
		       	{/if}

				{if !empty($plugins.affiliate)}
			        <div class="card bg-white mb-3">
			            <div class="card-header">
			                <div class="d-flex justify-content-between align-items-center cursor-pointer mb-3 py-3 px-4" data-toggle="collapse" data-target="#affiliate-panel" aria-expanded="true">
			                    <span class="d-flex align-items-center" >
			                    	<i class="fa-light fa-barcode-read color-highlight"></i>
			                        <span class="pl-2 mb-0 font-weight-bold">
			                            {__d('template', 'ma_gioi_thieu')}
			                        </span>
			                    </span>

			                    <span class="d-flex align-items-center" >
			                    	{if !empty($order_info.affiliate.affiliate_code)}
				                        <strong class="pr-3 text-success">
				                            {$order_info.affiliate.affiliate_code}
				                        </strong>
				                    {/if}
			                        <span >
			                            <i class="fa-light fa-angle-down"></i>
			                        </span>
			                    </span>
			                </div>
			            </div>
			    
			            <div id="affiliate-panel" class="collapse" data-parent="#accordion-order">
			                <div class="pb-4 pl-4 pr-4">
	                			<div class="form-group mb-3">
				                    <input id="affiliate-code" value="" type="text" class="bg-white border form-control rounded " placeholder="{__d('template', 'nhap_ma_gioi_thieu')}" >
			                    </div>

			                    <span nh-btn-action="apply-affiliate" class="btn btn-submit w-100 mb-3">
			                        {__d('template', 'ap_dung')}
			                    </span>

			                    {if !empty($order_info.affiliate.total_affiliate)}
			                    	<a href="javascript:;" nh-btn-action="delete-affiliate" class="color-hover mt-5 d-block">
				                        {__d('template', 'huy_ap_dung_ma_gioi_thieu')}
				                    </a>
			                    {/if}
			                </div>
			            </div>
			        </div>
		        {/if}

		       	{if !empty($plugins.point) && !empty($status_pay_by_point)}
			        <div class="card bg-white mb-3">
			            <div class="card-header">
			                <div class="d-flex justify-content-between align-items-center cursor-pointer mb-3 py-3 px-4" data-toggle="collapse" data-target="#point-panel">
			                    <span class="d-flex align-items-center" >
			                    	<i class="fa-light fa-gifts color-highlight"></i>
			                        <span class="pl-2 mb-0 font-weight-bold">
			                            {__d('template', 'su_dung_diem_tang')}
			                        </span>
			                    </span>

			                    <span class="d-flex align-items-center" >
			                    	<strong class="pr-2 color-highlight">
			                            {if !empty($order_info.point.point_promotion)}
			                            	- {$order_info.point.point_promotion|number_format:0:".":","} {__d('template', 'diem')}
			                            {/if}
			                        </strong>

			                        <span >
			                            <i class="fa-light fa-angle-down"></i>
			                        </span>
			                    </span>
			                </div>
			            </div>

			            <div id="point-panel" class="collapse" data-parent="#accordion-order">
			                <div class="pb-4 pl-4 pr-4">
			                	{if empty($member_info)}
		                			<p class="mb-0">
		                				<a nh-order-login href="javascript:;" class="color-main">
		                					{__d('template', 'dang_nhap_de_su_dung_chuc_nang')}
		                				</a>
		                			</p>
		                		{else}
			                    	<div class="d-flex justify-content-between align-items-center mb-2">
				                    	<small class="text-muted">
				                    		{__d('template', 'so_diem_hien_co')}: 
				                    		{if !empty($member_info.point_promotion)}
				                    			{$member_info.point_promotion|number_format:0:".":","}
				                    		{else}
				                    			0
				                    		{/if}
				                    		{__d('template', 'diem')}
				                    	</small>

				                    	{if !empty($member_info.expiration_time)}
					                    	<small class="text-muted">
					                    		{__d('template', 'han_dung')}: {$this->Utilities->convertIntgerToDateString($member_info.expiration_time)}
					                    	</small>
				                    	{/if}
				                    </div>
				                    <div class="input-group mb-4">
					                    <input nh-point-money="{$point_to_money}" nh-point-max="{$point_promotion_max}" id="point-promotion" value="{if !empty($order_info.point.point_promotion)}{$order_info.point.point_promotion|number_format:0:".":","}{/if}" type="text" class="bg-white border form-control rounded  pr-6 number-input" placeholder="" autocomplete="off">
					                    <div class="input-group-append">
											<span class="input-group-text input-group-main border-gray text-white">
												<span class="number-input point-to-money">
													{if !empty($config_point.point_to_money) && !empty($order_info.point.point_promotion)}
														{math assign = total_point_promotion equation = 'x*y' x = $config_point.point_to_money y = $order_info.point.point_promotion} 
														{$total_point_promotion|number_format:0:".":","}
													{else}
														0
													{/if}
												</span>
												<small>{CURRENCY_UNIT_DEFAULT}</small>
											</span>
										</div>
				                    </div>

				                    <div class="row">
				                    	<div class="col-6">
				                    		<span nh-btn-action="apply-point-promotion" class="btn btn-submit w-100">
						                        {__d('template', 'ap_dung')}
						                    </span>
				                    	</div>
				                    	<div class="col-6">
				                    		<span nh-btn-action="apply-point-promotion-all" class="btn btn-submit-1 w-100">
						                        {__d('template', 'mua_ngay_bang_diem')}
						                    </span>
				                    	</div>
				                    </div>

				                    {if !empty($order_info.point.point_promotion)}
				                    	<a href="javascript:;" nh-btn-action="delete-point-promotion" class="d-inline-block mt-3 text-dark">
					                        {__d('template', 'huy_ap_dung_diem')}
					                    </a>
				                    {/if}
			                    {/if}
			                </div>
			            </div>
			        </div>

			        <div class="card bg-white mb-3">
			            <div class="card-header">
			                <div class="d-flex justify-content-between align-items-center cursor-pointer mb-3 py-3 px-4" data-toggle="collapse" data-target="#point-wallet">
			                    <span class="d-flex align-items-center" >
			                    	<i class="fa-light fa-wallet color-highlight"></i>
			                        <span class="pl-2 mb-0 font-weight-bold">
			                            {__d('template', 'su_dung_diem_vi')}
			                        </span>
			                    </span>

			                    <span class="d-flex align-items-center" >
			                    	<strong class="pr-2 color-highlight">
			                            {if !empty($order_info.point.point)}
			                            	- {$order_info.point.point|number_format:0:".":","} {__d('template', 'diem')}
			                            {/if}
			                        </strong>
			                        <span >
			                            <i class="fa-light fa-angle-down"></i>
			                        </span>
			                    </span>
			                </div>
			            </div>

			            <div id="point-wallet" class="collapse" data-parent="#accordion-order">
			                <div class="pb-4 pl-4 pr-4">
			                	{if empty($member_info)}
		                			<p class="mb-0">
		                				<a nh-order-login href="javascript:;" class="color-main">
		                					{__d('template', 'dang_nhap_de_su_dung_chuc_nang')}
		                				</a>
		                			</p>
		                		{else}
			                    	<div class="d-flex justify-content-between align-items-center mb-2">
				                    	<small class="text-muted">
				                    		{__d('template', 'so_diem_hien_co')}: 
				                    		{if !empty($member_info.point)}
				                    			{$member_info.point|number_format:0:".":","}
				                    		{else}
				                    			0
				                    		{/if}
				                    		{__d('template', 'diem')}
				                    	</small>
				                    </div>
				                    <div class="input-group mb-4">
					                    <input nh-point-money="{$point_to_money}" nh-point-max="{$point_max}" id="wallet" value="{if !empty($order_info.point.point)}{$order_info.point.point|number_format:0:".":","}{/if}" type="text" class="bg-white border form-control rounded  pr-6 number-input" placeholder="" autocomplete="off">
					                    <div class="input-group-append">
											<span class="input-group-text input-group-main border-gray text-white">
												<span class="number-input point-to-money">
													{if !empty($config_point.point_to_money) && !empty($order_info.point.point)}
														{math assign = total_point equation = 'x*y' x = $config_point.point_to_money y = $order_info.point.point} 
														{$total_point|number_format:0:".":","}
													{else}
														0
													{/if}
												</span>
												<small>{CURRENCY_UNIT_DEFAULT}</small>
											</span>
										</div>
				                    </div>
				                    <div class="row mx-n2">
				                    	<div class="col-6 px-2">
				                    		<span nh-btn-action="apply-wallet" class="btn btn-submit w-100">
						                        {__d('template', 'ap_dung')}
						                    </span>
				                    	</div>
				                    	<div class="col-6 px-2">
				                    		<span nh-btn-action="apply-wallet-all" class="btn btn-submit-1 w-100">
						                        {__d('template', 'mua_ngay_bang_diem')}
						                    </span>
				                    	</div>
				                    </div>
				                    
				                    {if !empty($order_info.point.point)}
				                    	<a href="javascript:;" nh-btn-action="delete-wallet" class="d-inline-block mt-3 text-dark">
					                        {__d('template', 'huy_ap_dung_diem')}
					                    </a>
				                    {/if}
		                		{/if}
			                </div>
			            </div>
			        </div>
		       	{/if}
		    </div>
		</div>

		{if !empty($show_shipping)}
			{$this->element('../Order/element_shipping_methods')}
        {/if}
        
		{$this->element('../Order/element_items')}
		
		<div class="checkout-payment bg-white p-4">						    
			<span nh-btn-action="create-order" class="btn btn-submit w-100 mb-3">
                {__d('template', 'thanh_toan')}
            </span>
            <a title="{__d('template', 'quay_lai_gio_hang')}" class="order-back fs-14 d-flex align-items-center color-main mt-15" href="/order/cart-info">
            	<i class="fa-light fa-arrow-left mr-2"></i>
    			{__d('template', 'quay_lai_gio_hang')}
    		</a>
		</div>
	</div>
</div>