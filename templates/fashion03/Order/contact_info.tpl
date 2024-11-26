<div class="entry-account-detail mb-5">
	{if !empty($contact.full_name)}
		<p class="mb-0">
			<span class="inner-full-name">
				{$contact.full_name}
			</span>
		</p>
	{/if}

	{if !empty($contact.phone)}
		<p class="mb-0">
			<span class="inner-phone">
				{$contact.phone}
			</span>
		</p>
	{/if}

	{if !empty($contact.full_address)}
		<p class="mb-0">
			<span class="inner-full-address">
			 	{$contact.full_address}
			</span>
		</p>
	{/if}

	<input type="hidden" name="callback">
</div>