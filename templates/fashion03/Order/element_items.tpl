{assign var = plugins value = $this->Setting->getListPlugins()}
<div class="p-4 bg-white">
    <div class="d-flex justify-content-between mb-3">
        <span>
            {__d('template', 'don_gia')}
        </span>

        <span class="text-right">
		    <strong>
				<span class="price-amount">
					{if isset($order_info.total_items)}
						{$order_info.total_items|number_format:0:".":","}
						<span class="currency-symbol">
                            {CURRENCY_UNIT}
                        </span>
	            	{/if}
		        </span>
			</strong>

			{if !empty($order_info.total_items_default)}
			    <span class="form-text text-muted fs-12 mt-0">
			    	( {$order_info.total_items_default|number_format:0:".":","}
			    	<span class="currency-symbol fs-12">
                        {CURRENCY_UNIT_DEFAULT}
                    </span>)
                </span>
            {/if}
		</span>
    </div>

    {if !empty($order_info.shipping_fee_customer)}
        <div class="d-flex justify-content-between mb-3">
            <span>
                {__d('template', 'phi_van_chuyen')}
            </span>
            <span class="color-hover text-right">
                <strong>
                    <span class="price-amount">
                        + {$order_info.shipping_fee_customer|number_format:0:".":","}
                        <span class="currency-symbol">
                            {CURRENCY_UNIT}
                        </span>
                    </span>
                </strong>

                {if !empty($order_info.shipping_fee_customer_default)}
                    <span class="form-text text-muted fs-12 mt-0">
                        ( {$order_info.shipping_fee_customer_default|number_format:0:".":","}
                        <span class="currency-symbol fs-12">
                            {CURRENCY_UNIT_DEFAULT}
                        </span>)
                    </span>
                {/if}            
            </span>
        </div>
    {/if}

    {if !empty($plugins.promotion) && !empty($order_info.total_coupon)}
        <div class="d-flex justify-content-between mb-3">
            <span>
                {__d('template', 'phieu_giam_gia')}
            </span>
            <span class="font-success text-right">
                <strong>
                    <span class="price-amount">
                        {if !empty($order_info.total_coupon)}
                            - {$order_info.total_coupon|number_format:0:".":","}
                            <span class="currency-symbol">
                                {CURRENCY_UNIT}
                            </span>
                        {else}
                            0
                        {/if}
                    </span>
                </strong>

                {if !empty($order_info.total_coupon_default)}
                    <span class="form-text text-muted fs-12 mt-0">
                        ( {$order_info.total_coupon_default|number_format:0:".":","}
                        <span class="currency-symbol fs-12">
                            {CURRENCY_UNIT_DEFAULT}
                        </span>)
                    </span>
                {/if}
            </span>
            
        </div>
    {/if}

    {if !empty($plugins.affiliate) && !empty($order_info.total_affiliate)}
        <div class="d-flex justify-content-between mb-3">
            <span>
                {__d('template', 'ma_gioi_thieu')}
            </span>
            <span class="font-success text-right">
                <strong>
                    <span class="price-amount">
                        {if !empty($order_info.total_affiliate)}
                            - {$order_info.total_affiliate|number_format:0:".":","}
                            <span class="currency-symbol">
                                {CURRENCY_UNIT}
                            </span>
                        {else}
                            0
                        {/if}
                    </span>
                </strong>

                {if !empty($order_info.total_affiliate_default)}
                    <span class="form-text text-muted fs-12 mt-0">
                        ( {$order_info.total_affiliate_default|number_format:0:".":","}
                        <span class="currency-symbol fs-12">
                            {CURRENCY_UNIT_DEFAULT}
                        </span>)
                    </span>
                {/if}
            </span>
        </div>
    {/if}

    {if !empty($order_info.total_vat)}
        <div class="d-flex justify-content-between mb-3">
            <span>
                VAT
            </span>
            <span class="font-success text-right">
                <strong>
                    <span class="price-amount">
                        - {$order_info.total_vat|number_format:0:".":","}
                        <span class="currency-symbol">
                            {CURRENCY_UNIT}
                        </span>
                    </span>
                </strong>

                {if !empty($order_info.total_vat_default)}
                    <span class="form-text text-muted fs-12 mt-0">
                        ( {$order_info.total_vat_default|number_format:0:".":","}
                        <span class="currency-symbol fs-12">
                            {CURRENCY_UNIT_DEFAULT}
                        </span>)
                    </span>
                {/if}
            </span>
        </div>
    {/if}

    <div class="separation-dash mb-3"></div>

    <div class="d-flex justify-content-between mb-3">
        <span>
            {__d('template', 'thanh_tien')}
        </span>

        <span class="color-hover text-right">
            <strong>
                <span class="price-amount">
                    {if isset($order_info.total)}
                        {$order_info.total|number_format:0:".":","}
                        <span class="currency-symbol">
                            {CURRENCY_UNIT}
                        </span>
                    {/if}
                </span>
            </strong>

            {if !empty($order_info.total_default)}
                <span class="form-text text-muted fs-12 mt-0">
                    ( {$order_info.total_default|number_format:0:".":","}
                    <span class="currency-symbol fs-12">
                        {CURRENCY_UNIT_DEFAULT}
                    </span>)
                </span>
            {/if}
        </span>
    </div>
    {if !empty($plugins.point)}
        {if !empty($order_info.point_promotion_paid) || !empty($order_info.point_paid)}    
            <div class="separation-dash mb-3"></div>
            {if !empty($order_info.point_promotion_paid)}
            	<div class="d-flex justify-content-between mb-3">
                    <span>
                        {__d('template', 'thanh_toan_bang_diem_khuyen_mai')}
                    </span>
                    <span class="text-right">
                        <strong>
                            <span class="price-amount font-success">
                                - {$order_info.point_promotion_paid|number_format:0:".":","}
                                <span class="currency-symbol">
                                    {CURRENCY_UNIT}
                                </span>
                            </span>
                        </strong>

                        {if !empty($order_info.point_promotion_paid_default)}
                            <span class="form-text text-muted fs-12 mt-0">
                                ( {$order_info.point_promotion_paid_default|number_format:0:".":","}
                                <span class="currency-symbol fs-12">
                                    {CURRENCY_UNIT_DEFAULT}
                                </span>)
                            </span>
                        {/if}
                    </span>
                </div>
            {/if}
            
            {if !empty($order_info.point_paid)}
                <div class="d-flex justify-content-between mb-3">
                    <span>
                        {__d('template', 'thanh_toan_bang_diem_vi')}
                    </span>
                    <span class="text-right">
                        <strong>
                            <span class="price-amount font-success">
                                - {$order_info.point_paid|number_format:0:".":","}
                                <span class="currency-symbol">
                                    {CURRENCY_UNIT}
                                </span>
                            </span>
                        </strong>

                        {if !empty($order_info.point_paid_default)}
                            <span class="form-text text-muted fs-12 mt-0">
                                ( {$order_info.point_paid_default|number_format:0:".":","}
                                <span class="currency-symbol fs-12">
                                    {CURRENCY_UNIT_DEFAULT}
                                </span>)
                            </span>
                        {/if}
                    </span>
                </div>
            {/if}
            {if !empty($order_info.debt)}
                <div class="separation-dash mb-3"></div>
                <div class="d-flex justify-content-between">
                    <span>
                        {__d('template', 'con_phai_thanh_toan')}
                    </span>

                    <span class="color-hover text-right">
                        <strong>
                            <span class="price-amount">
                                {$order_info.debt|number_format:0:".":","}
                                <span class="currency-symbol">
                                    {CURRENCY_UNIT}
                                </span>
                            </span>
                        </strong>

                        {if !empty($order_info.debt_default)}
                            <span class="form-text text-muted fs-12 mt-0">
                                ( {$order_info.debt_default|number_format:0:".":","}
                                <span class="currency-symbol fs-12">
                                    {CURRENCY_UNIT_DEFAULT}
                                </span>)
                            </span>
                        {/if}
                    </span>
                </div>
            {/if}
        {/if}
    {/if}
</div>