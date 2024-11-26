<div class="row">
	<div class="col-6 col-lg-3">
		<div class="bg-light p-4 rounded-lg mb-5">
			<img src="{URL_TEMPLATE}assets/media/affilate/3d-cube-scan.svg" alt="{__d('template', 'tong_don')}" class="img-fluid image-48x48">
			<div class="mt-4">
				{__d('template', 'tong_don')}
			</div>

			<div>
				<span class="h2 font-weight-bold pr-1">
					{if !empty($statistical.month_number_order)}
						{$statistical.month_number_order|number_format:0:".":","} 
					{else}
						0
					{/if}
				</span>
				<span>
					{__d('template', 'don')}
				</span>
			</div>
		</div>
	</div>

	<div class="col-6 col-lg-3">
		<div class="bg-light p-4 rounded-lg mb-5">
			<img src="{URL_TEMPLATE}assets/media/affilate/3d-rotate.svg" alt="{__d('template', 'don_hang_that_bai')}" class="img-fluid image-48x48">
			<div class="mt-4">
				{__d('template', 'don_hang_that_bai')}
			</div>

			<div>
				<span class="h2 font-weight-bold pr-1">
					{if !empty($statistical.month_number_order_failed)}
						{$statistical.month_number_order_failed|number_format:0:".":","}
					{else}
						0
					{/if}
				</span>
				<span>
					{__d('template', 'don')}
				</span>
			</div>
		</div>
	</div>

	<div class="col-6 col-lg-3">
		<div class="bg-light p-4 rounded-lg mb-5">
			<img src="{URL_TEMPLATE}assets/media/affilate/coin-1.svg" alt="{__d('template', 'hoa_hong')}" class="img-fluid image-48x48">
			<div class="mt-4">
				{__d('template', 'hoa_hong')}
			</div>

			<div>
				<span class="h2 font-weight-bold pr-1">
					{if !empty($statistical.month_profit_point)}
						{$statistical.month_profit_point|number_format:0:".":","}
					{else}
						0
					{/if}
				</span>
				<span>
					{__d('template', 'diem')}
				</span>
			</div>
		</div>
	</div>

	<div class="col-6 col-lg-3">
		<div class="bg-light p-4 rounded-lg mb-5">
			<img src="{URL_TEMPLATE}assets/media/affilate/card-receive.svg" alt="{__d('template', 'don_hang_that_bai')}" class="img-fluid image-48x48">
			<div class="mt-4">
				{__d('template', 'tam_tinh')}
			</div>

			<div>
				<span class="h2 font-weight-bold pr-1">
					{if !empty($statistical.month_profit_money)}
						{$statistical.month_profit_money|number_format:0:".":","}
					{else}
						0
					{/if}
				</span>
				<span>vnd</span>
			</div>
		</div>
	</div>
</div>