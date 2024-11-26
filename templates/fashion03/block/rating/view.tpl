{strip}
<div nh-rating="{htmlentities($block_config|@json_encode)}" nh-anchor="rating" class="mb-5">
	<div class="h3 mb-4">
		{__d('template', 'khach_hang_danh_gia')}
	</div>

	{$this->element('../block/rating/form')}

	<ul nh-list-rating class="rating-list"></ul>
</div>
{/strip}