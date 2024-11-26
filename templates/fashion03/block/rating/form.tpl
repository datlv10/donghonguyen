{strip}
	{assign var = rating_info value = $this->Comment->getRatingInfo()}
	<div class="overall-rating ">
		<div class="row">
			<div class="rating-average col-lg-3 col-5">
				<div class="point-comment">
					<span>
						{if !empty($rating_info.avg_rating)}
							{$rating_info.avg_rating}
						{else}
							5.0
						{/if}
					</span>
				</div>

				<div class="star-rating">
					<span class="star-lg" style="width:100%"></span>
				</div>
			</div>

			<div class="col-lg-6 col-7">
			    <div class="percent-bar">
			        <div class="rating-num">
			        	5 <i class="fa-solid fa-star"></i>
			        </div>

			        {assign var = five_star_percent value = 0}
			        {if !empty($rating_info.five_star_percent)}
			        	{$five_star_percent = $rating_info.five_star_percent}
					{/if}

			        <div class="progress">
			            <div style="width: {$five_star_percent}%;" class="progress-bar progress-bar-warning"></div>
			        </div>

			        <div class="star-percent">
			        	<span>
				        	{$five_star_percent}
				        </span>%
			        </div>
			    </div>

			    <div class="percent-bar">
			        <div class="rating-num">
			        	4 <i class="fa-solid fa-star"></i>
			        </div>

			        {assign var = four_star_percent value = 0}
			        {if !empty($rating_info.four_star_percent)}
			        	{$four_star_percent = $rating_info.four_star_percent}
					{/if}

			        <div class="progress">
			            <div style="width: {$four_star_percent}%;" class="progress-bar progress-bar-warning"></div>
			        </div>

			        <div class="star-percent">
			        	<span>
			        		{$four_star_percent}
			        	</span>%
			        </div>
			    </div>

			    <div class="percent-bar">
			        <div class="rating-num">
			        	3 <i class="fa-solid fa-star"></i>
			        </div>

			        {assign var = three_star_percent value = 0}
			        {if !empty($rating_info.three_star_percent)}
			        	{$three_star_percent = $rating_info.three_star_percent}
					{/if}

			        <div class="progress">
			            <div style="width: {$three_star_percent}%;" class="progress-bar progress-bar-warning"></div>
			        </div>
			        <div class="star-percent">
			        	<span>
			        		{$three_star_percent}
			        	</span>%
			        </div>
			    </div>

			    <div class="percent-bar">
			        <div class="rating-num">
			        	2 <i class="fa-solid fa-star"></i>
			        </div>

			        {assign var = two_star_percent value = 0}
			        {if !empty($rating_info.two_star_percent)}
			        	{$two_star_percent = $rating_info.two_star_percent}
					{/if}

			        <div class="progress">
			            <div  style="width: {$two_star_percent}%;" class="progress-bar progress-bar-warning"></div>
			        </div>

			        <div class="star-percent">
			        	<span>
			        		{$two_star_percent}
			        	</span>%
			        </div>
			    </div>

			    <div class="percent-bar">
			        <div class="rating-num">
			        	1 <i class="fa-solid fa-star"></i>
			        </div>

			        {assign var = one_star_percent value = 0}
			        {if !empty($rating_info.one_star_percent)}
			        	{$one_star_percent = $rating_info.one_star_percent}
					{/if}

			        <div class="progress">
			            <div style="width: {$one_star_percent}%;" class="progress-bar progress-bar-warning"></div>
			        </div>

			        <div class="star-percent">
			        	<span>
			        		{$one_star_percent}
			        	</span>%
			        </div>
			    </div>
			</div>

			<div class="col-lg-3 col-12">
			    <p class="d-none d-lg-block mb-0">
			    	{__d('template', 'chia_se_nhan_xet_ve_san_pham')}
			    </p>
		    	<div nh-btn-show-rating class="btn btn-submit mt-3 w-100">
			    	{__d('template', 'danh_gia_va_nhan_xet')}
			    </div>
			</div>
		</div>
	</div>

	<form nh-form-rating id="rating-form" method="POST" class="rating-form collapse" autocomplete="off">
	    <div class="form-group">
	        <h3>
	        	<b>
	        		{__d('template', 'gui_nhan_xet_cua_ban')}
	        	</b>
	        </h3>	        
	        
			<label>
				1. {__d('template', 'danh_gia_cua_ban_ve_san_pham_nay')}:
			</label>

			<div nh-review-star class="review-star">
				<input id="star5" name="rating" value="5" type="radio" />
				<label for="star5" title="{__d('template', 'tuyet_voi')}"></label>

				<input id="star4" name="rating" value="4" type="radio" />
				<label for="star4" title="{__d('template', 'kha_tot')}"></label>

				<input id="star3" name="rating" value="3" type="radio" />
				<label for="star3" title="{__d('template', 'kha')}"></label>

				<input id="star2" name="rating" value="2" type="radio" />
				<label for="star2" title="{__d('template', 'hoi_te')}"></label>

				<input id="star1" name="rating" value="1" type="radio"  />
				<label for="star1" title="{__d('template', 'that_te')}"></label>
			</div>
	    </div>

		<div class="form-group">
			<label>
				2. {__d('template', 'viet_nhan_xet_cua_ban_vao_ben_duoi')}:
			</label>
			<textarea nh-input-rating name="content" cols="45" rows="8" placeholder="{__d('template', 'nhan_xet_cua_ban_ve_san_pham_nay')}" class="required form-control"></textarea>
		</div>

		<label>
			3. {__d('template', 'thong_tin_ca_nhan_cua_ban')}:
		</label>

		<div class="form-group">
			<label>
				{__d('template', 'ho_va_ten')}:
				<span class="required">*</span>
			</label>
			<input name="full_name" type="text" value="" class="form-control required" autocomplete="off">
		</div>

		<div class="row">
			<div class="col-12 col-sm-6">
				<div class="form-group">
					<label>
						{__d('template', 'so_dien_thoai')}:
						<span class="required">*</span>
					</label>
					<input name="phone" type="text" value="" class="form-control required" autocomplete="off">
				</div>
			</div>

			<div class="col-12 col-sm-6">
				<div class="form-group">
					<label>
						{__d('template', 'email')}
					</label>
					<input name="email" type="text" value="" class="form-control" autocomplete="off">
				</div>
			</div>
		</div>

		<ul class="comment-images"></ul>

		<input nh-input-rating-images name="files[]" type="file" class="d-none" accept="image/*" multiple="multiple">
	    <span class="mb-0 btn btn-submit" nh-trigger-upload>
			<i class="fa-light fa-camera"></i>
		</span>		

	    <button nh-btn-send-rating class="btn btn-submit ml-3">
	        {__d('template', 'gui_danh_gia')}
	    </button>        
	</form>
{/strip}