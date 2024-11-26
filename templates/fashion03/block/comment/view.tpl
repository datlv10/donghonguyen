{strip}
<div nh-comment="{htmlentities($block_config|@json_encode)}" nh-anchor="comment" class="comment-section mb-5">
	<div class="h3 mb-4">
		{__d('template', 'binh_luan')}
	</div>

	<div nh-comment-info class="customer-info d-none">
		<span nh-comment-change-info>
			<i class="fa-light fa-pen-to-square mr-2"></i>
			<span nh-comment-fullname></span>
		</span>
	</div>

	<div class="edit-comment">
		<textarea class="form-control" nh-input-comment placeholder="{__d('template', 'moi_ban_de_lai_binh_luan')}"></textarea>	
		<div class="box-comment">			
			<label>
				<span nh-trigger-upload>
					<i class="fa-light fa-camera"></i>
				</span>
			</label>
			<input nh-input-comment-images name="files[]" type="file" class="d-none" accept="image/jpeg, image/png" multiple="multiple">
		</div>

		<span nh-btn-send-comment class="btn btn-submit mt-3">
			{__d('template', 'gui_binh_luan')}
		</span>
	</div>

	<b class="total-comment">
    	<span nh-total-comment></span> 
    	{__d('template', 'binh_luan')}
    </b>

    <ul nh-list-comment class="list-comment"></ul>
</div>
{/strip}