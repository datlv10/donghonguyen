<html>
<head>
    <!--Link To CSS File-->
    <title>VNPT PAYMENT</title>
    <meta http-equiv="Content-Type" content="text/html, charset=utf-8">

    <link href="/favicon.ico" rel="icon" type="image/x-icon"/>
    <link href="{URL_TEMPLATE}assets/css/variable.css" rel="stylesheet" type="text/css" />
	<link href="{URL_TEMPLATE}assets/css/fonts.css" rel="stylesheet" type="text/css" />
	<link href="{URL_TEMPLATE}assets/lib/bootstrap/bootstrap.min.css" rel="stylesheet" type="text/css" />
	<link href="{URL_TEMPLATE}assets/css/order.css" rel="stylesheet" type="text/css" />
	<link href="{URL_TEMPLATE}assets/css/member.css" rel="stylesheet" type="text/css" />
	<link href="{URL_TEMPLATE}assets/css/utilities.css" rel="stylesheet" type="text/css" />
	<link href="{URL_TEMPLATE}assets/css/page.css" rel="stylesheet" type="text/css" />
	<link href="{URL_TEMPLATE}assets/css/custom.css" rel="stylesheet" type="text/css" />
	<link rel="stylesheet" href="{$payment_domain}/pg_was/css/payment/layer/paymentClient.css" type="text/css" media="screen">

    <style type="text/css">
		iframe {
			height: 100% !important;
		}
    </style>
</head>
<body>
	<form id="megapayForm" name="megapayForm" method="POST">
        <input type="hidden" name="invoiceNo" value="{if !empty($params.payment_code)}{$params.payment_code}{/if}"/>
        <input type="hidden" name="description" value="{if !empty($params.order_info)}{$params.order_info}{/if}"/>
        <input type="hidden" name="goodsNm" value="{if !empty($product_name)}{$product_name}{else}{if !empty($params.order_info)}{$params.order_info}{/if}{/if}"/>
        <input type="hidden" name="currency" value="VND">
                        
        <input type="hidden" name="buyerFirstNm" value="{if !empty($first_name)}{$first_name}{/if}">
        <input type="hidden" name="buyerLastNm" value="{if !empty($last_name)}{$last_name}{/if}">
        <input type="hidden" name="buyerPhone" value="">
        <input type="hidden" name="buyerAddr" value="">
        <input type="hidden" name="buyerCity" value="">
        <input type="hidden" name="buyerState" value=""/>
        <input type="hidden" name="buyerPostCd" value=""/>
        <input type="hidden" name="buyerCountry" value="vn"/>
        <input type="hidden" name="fee" value=""/>

        <!-- Delivery Info -->
        <input type="hidden" name="receiverFirstNm" value="">
        <input type="hidden" name="receiverLastNm" value="">
        <input type="hidden" name="receiverPhone" value="">
        <input type="hidden" name="receiverAddr" value="">
        <input type="hidden" name="receiverCity" value="">
        <input type="hidden" name="receiverState" value=""/>
        <input type="hidden" name="receiverPostCd" value=""/>
        <input type="hidden" name="receiverCountry" value="VN"/>

        <!-- Call Back URL -->
        <input type="hidden" name="callBackUrl" value="{if !empty($params.redirect_url)}{$params.redirect_url}{/if}"/>
        <!-- Notify URL -->
        <input type="hidden" name="notiUrl" value="{if !empty($params.ipn_url)}{$params.ipn_url}{/if}"/>
        <!-- Merchant ID -->
        <input type="hidden" name="merId" value="{if !empty($gateway_config.merchant_id)}{$gateway_config.merchant_id}{/if}"/>
        <!-- Encode Key -->

        <input type="hidden" name="reqDomain" value="{$smarty.server['REQUEST_SCHEME']}://{$smarty.server['SERVER_NAME']}"/>
        <input type="hidden" name="userLanguage" value="VN"/>
        <input type="hidden" name="merchantToken" value="{if !empty($params.token)}{$params.token}{/if}"/>
        <input type="hidden" name="payToken" id="payToken" value=""/>
        <input type="hidden" name="timeStamp" value="{if !empty($params.time_stamp)}{$params.time_stamp}{/if}"/>
        <input type="hidden" name="merTrxId" value="{if !empty($params.mer_trx_id)}{$params.mer_trx_id}{/if}" />

        <input type="hidden" name="windowType" value=""/>
        <input type='hidden' name='windowColor' value='#0B3B39'/>
        <input type="hidden" name="vaCondition" value="03"/>
        <input type="hidden" name="subappid" id="subappid" value=""/>
        <input type="hidden" name="payType" id="payType" value="NO"/>
        <input type="hidden" name="amount" id="amount" value="{if !empty($params.amount)}{$params.amount}{/if}"/>

        <div class="container mb-60">
			<div class="row mt-50">
				<div class="col-12 col-md-12 col-lg-12">
					<div class="rounded bg-white p-15 mb-10 h-100">
						<h2 class="title-section-2 color-black font-weight-bold fs-16 mb-30 text-uppercase pb-10 border-bottom">
							<span>
								{__d('template', 'don_hang')} :
								{if !empty($order.code)}
									{$order.code}
								{/if}
							</span>
						</h2>
						<h4 class="color-black">
							<b>
								{__d('template', 'thong_tin_khach_hang')}
							</b>
						</h4>

						<div class="row">
							<div class="col-12 col-sm-6">
								{if !empty($order.contact.full_name)}
									<p class="color-black mb-5">
										<b>{__d('template', 'ho_va_ten')}:</b> {$order.contact.full_name}
									</p>
								{/if}

								{if !empty($order.contact.phone)}
									<p class="color-black mb-5">
										<b>{__d('template', 'so_dien_thoai')}:</b> {$order.contact.phone}
									</p>
								{/if}

								{if !empty($order.contact.email)}
									<p class="color-black mb-5">
										<b>{__d('template', 'email')}:</b> {$order.contact.email}
									</p>
								{/if}
							</div>	
							<div class="col-12 col-sm-6">
								{if !empty($order.contact.full_address)}
									<p class="color-black mb-5">
										<b>{__d('template', 'dia_chi')}:</b> {$order.contact.full_address}
									</p>
								{/if}

								{if !empty($order.note)}
									<p class="color-black mb-5">
									    <b>{__d('template', 'ghi_chu')}: </b>{$order.note}
								    </p>
								{/if}
							</div>		
						</div>

						<h2 class="title-section-2 mt-30 color-black font-weight-bold fs-16 mb-30 text-uppercase pb-10 border-bottom">
							<span>{__d('template', 'thong_tin_san_pham')}</span>
						</h2>

						<table class="table responsive-table mb-0">
							<thead>
						        <tr>
						            <th>{__d('template', 'san_pham')}</th>
						            <th>{__d('template', 'gia')}</th>
						            <th>{__d('template', 'so_luong')}</th>
						            <th class="text-right">{__d('template', 'tien')}</th>
						        </tr>
						    </thead>
							<tbody>
								{if !empty($order.items)}
									{foreach from = $order.items item = item}
										<tr class="cart_item">
								            <th scope="row">
								            	{if !empty($item['images'][0])}
									                {assign var = url_img value = "{CDN_URL}{$this->Utilities->getThumbs($item['images'][0], 50)}"}
									            {else}
									                {assign var = url_img value = "data:image/gif;base64,R0lGODlhAQABAIAAAMLCwgAAACH5BAAAAAAALAAAAAABAAEAAAICRAEAOw=="}
									            {/if}
								                <a href="{$this->Utilities->checkInternalUrl($item.url)}">
								                	<img class="img-fluid mr-10" src="{$url_img}" alt="{if !empty($item.name_extend)}{$item.name_extend}{/if}" />

								                	{if !empty($item.name_extend)}
								                		{$item.name_extend}
								                	{/if}
								                </a>
								            </th>

								            <td data-title="{__d('template', 'gia')}">
								            	<span class="price-amount">
								            		{if !empty($item.price)}
										                <span>
										                	{$item.price|number_format:0:".":","}
										                </span>
									                {/if}
								            		<span class="currency-symbol">{CURRENCY_UNIT_DEFAULT}</span>
								            	</span>
								            </td>

								            <td data-title="{__d('template', 'so_luong')}">
								            	{if !empty($item.quantity)}
									                <span>
									                	{$item.quantity|number_format:0:".":","}
									                </span>
								                {/if}
								            </td>

								            <td data-title="{__d('template', 'tien')}" class="text-right">
								            	<span class="price-amount">
								            		{if !empty($item.total_item)}
										                <span>
										                	{$item.total_item|number_format:0:".":","}
										                </span>
									                {/if}
													<span class="currency-symbol">{CURRENCY_UNIT_DEFAULT}</span>
								                </span>
								            </td>
								        </tr>
									{/foreach}
								{/if}
							</tbody>
							<tfoot>
								{if !empty($order.shipping_fee_customer)}
									<tr>
										<td colspan="3">
											<strong class="fs-14">
												{__d('template', 'phi_van_chuyen')}
											</strong>
										</td>
										<td class="text-right">
											<span class="price-amount">
							            		+ {$order.shipping_fee_customer|number_format:0:".":","}
								            	<span class="currency-symbol">{CURRENCY_UNIT_DEFAULT}</span>
							            	</span>
										</td>
									</tr>
								{/if}
								{if !empty($order.total_coupon)}
									<tr>
										<td colspan="3">
											<strong class="fs-14">
												{__d('template', 'phieu_giam_gia')}
											</strong>
										</td>
										<td class="text-right">
											<span class="price-amount">
							            		- {$order.total_coupon|number_format:0:".":","}
								            	<span class="currency-symbol">{CURRENCY_UNIT_DEFAULT}</span>
							            	</span>
										</td>
									</tr>
								{/if}

								{if !empty($order.point_promotion_paid)}
									<tr>
										<td colspan="3">
											<strong class="fs-14">
												{__d('template', 'diem_khuyen_mai')}
											</strong>
										</td>
										<td class="text-right">
											<span class="price-amount">
							            		- {$order.point_promotion_paid|number_format:0:".":","}
								            	<span class="currency-symbol">{CURRENCY_UNIT_DEFAULT}</span>
							            	</span>
										</td>
									</tr>
								{/if}

								{if !empty($order.point_paid)}
									<tr>
										<td colspan="3">
											<strong class="fs-14">
												{__d('template', 'diem_vi')}
											</strong>
										</td>
										<td class="text-right">
											<span class="price-amount">
							            		- {$order.point_paid|number_format:0:".":","}
								            	<span class="currency-symbol">{CURRENCY_UNIT_DEFAULT}</span>
							            	</span>
										</td>
									</tr>
								{/if}

								{if !empty($order.total_affiliate)}
									<tr>
										<td colspan="3">
											<strong class="fs-14">
												{__d('template', 'ma_gioi_thieu')}
											</strong>
										</td>
										<td class="text-right">
											<span class="price-amount">
							            		- {$order.total_affiliate|number_format:0:".":","}
								            	<span class="currency-symbol">{CURRENCY_UNIT_DEFAULT}</span>
							            	</span>
										</td>
									</tr>
								{/if}

								{if !empty($order.total_vat)}
									<tr>
										<td colspan="3">
											<strong class="fs-14">
												VAT
											</strong>
										</td>
										<td class="text-right">
											<span class="price-amount">
							            		+ {$order.total_vat|number_format:0:".":","}
								            	<span class="currency-symbol">{CURRENCY_UNIT_DEFAULT}</span>
							            	</span>
										</td>
									</tr>
								{/if}

								<tr class="color-hover font-extra-large">
									<td colspan="3"><b>{__d('template', 'tong_tien')}</b></td>
									<td class="text-right">
										<b>
											<span class="price-amount">
							            		{if !empty($order.debt)}
							            			{$order.debt|number_format:0:".":","}
							            		{/if}
								            	<span class="currency-symbol">{CURRENCY_UNIT_DEFAULT}</span>
							            	</span>
										</b>
									</td>
								</tr>
							</tfoot>
						</table>

						{if !empty($order.code)}
							<a href="/order/checkout?code={$order.code}" class="btn btn-secondary mt-30 mb-0">
								{__d('template', 'quay_lai')}
							</a>
						{/if}

						<span onclick="openPayment(1, '{$payment_domain}');" class="btn btn-primary mt-30 mb-0">
							{__d('template', 'thanh_toan_don_hang')}
						</span>
					</div>
				</div>
			</div>	
		</div>
    </form>
	<script type="text/javascript" src="http://code.jquery.com/jquery-1.11.1.min.js"></script>
	<script type="text/javascript" src="{$payment_domain}/pg_was/js/payment/layer/paymentClient.js"></script>
</body>
</html>