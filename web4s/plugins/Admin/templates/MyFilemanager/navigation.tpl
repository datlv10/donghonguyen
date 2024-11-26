<div class="navigation-info">
	<div nh-wrap="navigation" class="navigation position-relative"></div>

	<div class="progress-capacity">
		<div class="progress-bar">
			{assign var = percent value = 0}
			{if !empty($total_size) && !empty($max_size)}
				{$percent = $total_size / $max_size * 100|round:0}
			{/if}

			<span class="info-progress-bar {if $percent >= 60}orange-bg{elseif $percent >= 90}red-bg{/if}" style="width: {$percent}%;"></span>
		</div>

		<div class="info-capacity">
			<span class="font-weight-bold">
				{__d('filemanager', 'su_dung')}:
			</span> 

			<i class="red">
				<span class="font-weight-bold capacity">
					{if !empty($total_size)}
						{$this->UtilitiesAdmin->parseFileSize($total_size)}
					{/if}
				</span>
				/
				<span class="font-weight-bold capacity">
					{if !empty($max_size)}
						{$this->UtilitiesAdmin->parseFileSize($max_size, 0)}
					{else}
						___
					{/if}
				</span>
			</i>
		</div>
		<div class="info-capacity info-capacity-image-file">
			<span class="font-weight-bold">
				{__d('filemanager', 'anh_tai_len_gioi_han')}:
			</span> 

			<span class="font-weight-bold capacity">
				<i class="orange">
					{$this->UtilitiesAdmin->parseFileSize(MAX_IMAGE_SIZE, 0)} / {__d('filemanager', 'anh')}
				</i>
			</span>
		</div>
		<div class="info-capacity info-capacity-image-file">
			<span class="font-weight-bold">
				{__d('filemanager', 'tep_tai_len_gioi_han')}:
			</span> 

			<span class="font-weight-bold capacity">
				<i class="orange">
					{$this->UtilitiesAdmin->parseFileSize(MAX_FILE_SIZE, 0)} / {__d('filemanager', 'tep')}
				</i>
			</span>
		</div>

		<div class="info-modal-instruct">
			<a href="#modal-instruct" rel="modal:open" class="btn btn-nav-top">
				<img src="{ADMIN_PATH}/myfilemanager/assests/img/icon-navbar/Question-circle.svg">
			    {__d('filemanager', 'luu_y')}
			</a>

		</div>
		
	</div>
</div>