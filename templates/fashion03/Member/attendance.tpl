{$this->element('breadcrumb', [
	'list_url' => [
		['title' => {$title_for_layout}]
	]
])}

<div class="container">
	<div class="row mx-n2">
		<div class="col-12 col-md-3 col-lg-3 px-2">
			{$this->element('../Member/element_menu')}
		</div>
		<div class="col-12 col-md-9 col-lg-9 px-2">
			<div class="h-100 bg-white p-4">
				<div class="d-flex align-items-center justify-content-between mb-5">
					<div>
						<p class="mb-2">
							{__d('template', 'ban_co')}
						    <span class="fs-22 font-weight-bold color-orange">
						    	{if !empty($customer_point.point)}
						    		{$customer_point.point|number_format:0:".":","}
						    	{else}
						    		0
						    	{/if}
						    </span>
						    {__d('template', 'diem')}
						</p>
						<p class="mb-0">
							{if !empty($config_point.point_to_money)}
								{__d('template', 'moi_diem_tuong_ung_bang')}
								<span class="point_to_money font-weight-bold">
									{$config_point.point_to_money|number_format:0:".":","}
								</span>
								{__d('template', 'dong_vnd')}
							{/if}
						</p>
					</div>

					<div class="promotion-points text-right">
						<p class="mb-2">
							<span class="fs-19 font-weight-bold number_point_promition color-black">
								{if !empty($customer_point.point_promotion)}
									{$customer_point.point_promotion|number_format:0:".":","}
								{else}
						    		0
								{/if}
							</span>
							{__d('template', 'diem_khuyen_mai')}
						</p>
						<p class="mb-0">
							{__d('template', 'thoi_han_su_dung_diem_den_ngay')}
							<span class="point_to_money text-danger font-weight-bold">
								{if !empty($customer_point.expiration_time)}
									: {$this->Utilities->convertIntgerToDateString($customer_point.expiration_time)}
								{/if}
							</span>
						</p>
					</div>
				</div>

				{if !empty($attendance)}
					<div class="member-attendance">
						<div class="h5 font-weight-bold mb-3">
							{__d('template', 'diem_danh')}
						</div>
						<div nh-attendance>
							<ul class="list-date list-unstyled row align-items-center justify-content-start flex-wrap mx-n2">
								{foreach from = $attendance key = key item = item}
									<li class="col-4 col-md-2 px-2 mb-3">
										<div {if $item.check}checked="checked"{else}attendance-tick="true"{/if} class="d-flex align-items-center justify-content-center flex-column rounded-lg py-4 list-date--item" data-day="{$key + 1}" data-date="{if !empty($item.date)}{$item.date}{/if}" data-point="{if !empty($item.point)}{$item.point}{/if}">
											<span class="point font-weight-bold h4 mb-0">
												{if !empty($item.point)}
													+{$item.point}
												{/if}
											</span>
											<span class="date">
												{if !$item.is_today && !empty($item.date)}
													{date("d/m", $item.date)}
												{else}
													{__d('template', 'hom_nay')}
												{/if}
											</span>
										</div>
									</li>			
								{/foreach}
							</ul>
						</div>
					</div>
				{/if}
			</div>
		</div>
	</div>	
</div>

{$this->element('../Member/modal_attendance_sucess')}
{$this->element('../Member/modal_attendance_error')}