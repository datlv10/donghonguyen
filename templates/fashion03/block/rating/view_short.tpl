{strip}
<div nh-short-rating="{htmlentities($block_config|@json_encode)}" nh-anchor="rating" class="mb-5">
	{if !empty(PAGE_RECORD_ID) && (PAGE_TYPE eq "{ARTICLE_DETAIL}" || PAGE_TYPE eq "{PRODUCT_DETAIL}")}
	
		{if !empty(PAGE_TYPE) && PAGE_TYPE eq "{ARTICLE_DETAIL}"}
			{assign var = detail_info value = $this->Article->getDetailArticle({PAGE_RECORD_ID}, {LANGUAGE})}
		{/if}

		{if !empty(PAGE_TYPE) && PAGE_TYPE eq "{PRODUCT_DETAIL}"}
			{assign var = detail_info value = $this->Product->getDetailProduct({PAGE_RECORD_ID}, {LANGUAGE})}
		{/if}

		{* Số lượt đánh giá *}
		{assign var = review_count value = 0}
		{if !empty($detail_info.rating_number)}
			{assign var = review_count value = $detail_info.rating_number}
		{/if}

		{* điểm đánh giá *}
		{assign var = rating value = 0}
		{if !empty($detail_info.rating)}
			{assign var = rating value = $detail_info.rating}
		{/if}
		<div>
    		<div class="d-flex align-items-center">
    			<form nh-form-rating id="rating-form" method="POST" autocomplete="off">
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
    			</form>
    			<span short-rating="{$rating}">{$rating}</span> / 5 (<span short-review-count="{$review_count}" class="mr-2">{$review_count}</span>{__d('template', 'binh_chon')})
    		</div>
    		<div rating-exits></div>
		</div>
		
	{/if}
</div>
{/strip}