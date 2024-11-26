<div class="navbar navbar-fixed-top">
	<div class="navbar-inner navbar-inner-pc">
		<div class="item-navbar">
			<span nh-btn="create-folder" class="btn btn-main btn-nav-top btn-create-folder" data-tooltip="{__d('filemanager', 'tao_thu_muc')}">
				<img src="{ADMIN_PATH}/myfilemanager/assests/img/icon-navbar/Folder-plus.svg"/>
			</span>

			<span nh-btn="upload" class="btn btn-main btn-nav-top" data-tooltip="{__d('filemanager', 'tai_len')}">
				<img src="{ADMIN_PATH}/myfilemanager/assests/img/icon-navbar/Upload.svg"/>
			</span>

			<div class="btn-nav-top-sort position-relative">
				<span class="btn btn-main btn-nav-top" nh-dropdown="sort-column" data-tooltip="{__d('filemanager', 'sap_xep')}">
					<img src="{ADMIN_PATH}/myfilemanager/assests/img/icon-navbar/Sort1.svg"/>
				</span>	
				<div nh-wrap="sort-column" class="sort-column dropdown" nh-dropdown-element="sort-column" style="display:none;">
					<ul>
						<li nh-btn="sort" data-type="{NAME}" data-sort-type>
							<img src="{ADMIN_PATH}/myfilemanager/assests/img/icon-navbar/Edit-text.svg"/>
							<span >
								{__d('filemanager', 'ten_tep')}
							</span>
						</li>

						<li nh-btn="sort" data-type="{EXTENSION}" data-sort-type>
							<img src="{ADMIN_PATH}/myfilemanager/assests/img/icon-navbar/Commode1.svg"/>
							<span>
								{__d('filemanager', 'loai_tep')}
							<span>
						</li>

						<li nh-btn="sort" data-type="{SIZE}" data-sort-type>
							<img src="{ADMIN_PATH}/myfilemanager/assests/img/icon-navbar/Article.svg"/>
							<span>
								{__d('filemanager', 'dung_luong')}
							<span>
						</li>

						<li nh-btn="sort" data-type="{TIME}" data-sort-typei>
							<img src="{ADMIN_PATH}/myfilemanager/assests/img/icon-navbar/Time-schedule.svg"/>
							<span>
								{__d('filemanager', 'ngay_tao')}
							<span>
						</li>
					</ul>
				</div>
			</div>

			{if !empty($cross_domain) && !empty($multiple)}
				<span nh-btn="select-all" nh-group="select" class="btn btn-main btn-nav-top ml-20 d-none" data-tooltip="{__d('filemanager', 'chon_tat_ca')}">
					<img src="{ADMIN_PATH}/myfilemanager/assests/img/icon-navbar/Double-check.svg"/>
				</span>

				<span nh-btn="unselect-all" nh-group="select" class="btn btn-main btn-nav-top d-none" data-tooltip="{__d('filemanager', 'bo_chon_tat_ca')}">
					<img src="{ADMIN_PATH}/myfilemanager/assests/img/icon-navbar/Close.svg"/>
				</span>

				<span nh-btn="selected" nh-group="select" class="btn btn-main btn-nav-top btn-select d-none" data-tooltip="{__d('filemanager', 'chon')}">
					<img src="{ADMIN_PATH}/myfilemanager/assests/img/icon-navbar/Check.svg"/>
					{__d('filemanager', 'ap_dung')}
				</span>
			{/if}
		</div>

		<div class="item-navbar-center">
			<div nh-wrap="filter" class="filter item-navbar">
				{if empty($type_file)}
					<span nh-btn="filter" data-type="{IMAGE}" class="btn btn-main btn-nav-top" data-tooltip="{__d('filemanager', 'hinh_anh')}">
						<img src="{ADMIN_PATH}/myfilemanager/assests/img/icon-navbar/Picture.svg"/>
					</span>

					<span nh-btn="filter" data-type="{DOCUMENT}" class="btn btn-main btn-nav-top" data-tooltip="{__d('filemanager', 'tai_lieu')}">
						<img src="{ADMIN_PATH}/myfilemanager/assests/img/icon-navbar/File.svg"/>
					</span>

					<span nh-btn="filter" data-type="{VIDEO}" class="btn btn-main btn-nav-top" data-tooltip="{__d('filemanager', 'video')}">
						<img src="{ADMIN_PATH}/myfilemanager/assests/img/icon-navbar/Movie-Lane2.svg"/>
					</span>

					<span nh-btn="filter" data-type="{AUDIO}" class="btn btn-main btn-nav-top" data-tooltip="{__d('filemanager', 'am_thanh')}">
						<img src="{ADMIN_PATH}/myfilemanager/assests/img/icon-navbar/Music.svg"/>
					</span>

					<span nh-btn="filter" data-type="{ARCHIVE}" class="btn btn-main btn-nav-top" data-tooltip="{__d('filemanager', 'file_nen')}">
						<img src="{ADMIN_PATH}/myfilemanager/assests/img/icon-navbar/Mail-box.svg"/>
					</span>
				{/if}

				<input class="search-input" nh-input="filter" data-type="{KEYWORD}" type="text" name="keyword" value="" placeholder="{__d('filemanager', 'nhap_ten_tep')}">

				{if empty($type_file)}
					<span nh-btn="clear-filter" class="btn btn-main btn-nav-top" data-tooltip="{__d('filemanager', 'tai_trang')}">
						<img src="{ADMIN_PATH}/myfilemanager/assests/img/icon-navbar/Update.svg"/>
					</span>
				{/if}
			</div>
		</div>
		<div class="item-navbar item-navbar-right">
			<span nh-btn="view-detail" data-type="gird" class="btn btn-main btn-nav-top btn-nav-top-view-detail" data-tooltip="{__d('filemanager', 'xem_chi_tiet')}">
				<img src="{ADMIN_PATH}/myfilemanager/assests/img/icon-navbar/Visible.svg"/>
			</span>

			<span nh-btn="view-list" data-type="gird" class="btn btn-main btn-nav-top" data-tooltip="{__d('filemanager', 'xem_dang_luoi')}">
				<img src="{ADMIN_PATH}/myfilemanager/assests/img/icon-navbar/Dial-numbers.svg"/>
			</span>

			<span nh-btn="view-list" data-type="list" class="btn btn-main btn-nav-top" data-tooltip="{__d('filemanager', 'xem_dang_danh_sach')}">
				<img src="{ADMIN_PATH}/myfilemanager/assests/img/icon-navbar/Layout-horizontal.svg"/>
			</span>
		</div>
	</div>
</div>

<div class="navbar navbar-fixed-top-mobile">
	<div class="item-navbar">
		<div class="item-navbar-info">
			<span nh-btn="create-folder" class="btn btn-main btn-nav-top btn-create-folder" data-tooltip="{__d('filemanager', 'tao_thu_muc')}">
				<img src="{ADMIN_PATH}/myfilemanager/assests/img/icon-navbar/Folder-plus.svg"/>
			</span>

			<span nh-btn="upload" class="btn btn-main btn-nav-top" data-tooltip="{__d('filemanager', 'tai_len')}">
				<img src="{ADMIN_PATH}/myfilemanager/assests/img/icon-navbar/Upload.svg"/>
			</span>		
		</div>
	</div>
	<div class="item-navbar">
		<div nh-wrap="filter" class="filter item-navbar">
			<div class="item-navbar-info">
				<div class="btn-nav-top-filter position-relative">
					<span class="btn btn-main btn-nav-top" nh-dropdown="filter" data-tooltip="{__d('filemanager', 'sap_xep')}">
						<img src="{ADMIN_PATH}/myfilemanager/assests/img/icon-navbar/Sort1.svg"/>
					</span>	
					<div class="dropdown" nh-dropdown-element="filter" style="display:none;">
						<div class="item-filter">
							{if empty($type_file)}
								<span nh-btn="filter" data-type="{IMAGE}" class="btn" data-tooltip="{__d('filemanager', 'hinh_anh')}">
									<span class="btn btn-main btn-nav-top">
										<img src="{ADMIN_PATH}/myfilemanager/assests/img/icon-navbar/Picture.svg"/>
									</span>
									<span>
										{__d('filemanager', 'hinh_anh')}
									</span>
								</span>

								<span nh-btn="filter" data-type="{DOCUMENT}" class="btn" data-tooltip="{__d('filemanager', 'tep_tin')}">
									<span class="btn btn-main btn-nav-top">
										<img src="{ADMIN_PATH}/myfilemanager/assests/img/icon-navbar/File.svg"/>
									</span>
									<span>
										{__d('filemanager', 'tep_tin')}
									</span>
								</span>

								<span nh-btn="filter" data-type="{VIDEO}" class="btn" data-tooltip="{__d('filemanager', 'video')}">
									<span class="btn btn-main btn-nav-top">
										<img src="{ADMIN_PATH}/myfilemanager/assests/img/icon-navbar/Movie-Lane2.svg"/>
									</span>
									<span>
										{__d('filemanager', 'video')}
									</span>
								</span>

								<span nh-btn="filter" data-type="{AUDIO}" class="btn" data-tooltip="{__d('filemanager', 'am_thanh')}">
									<span class="btn btn-main btn-nav-top">
										<img src="{ADMIN_PATH}/myfilemanager/assests/img/icon-navbar/Music.svg"/>
									</span>
									<span>
										{__d('filemanager', 'am_thanh')}
									</span>
								</span>

								<span nh-btn="filter" data-type="{ARCHIVE}" class="btn" data-tooltip="{__d('filemanager', 'file_nen')}">
									<span class="btn btn-main btn-nav-top">
										<img src="{ADMIN_PATH}/myfilemanager/assests/img/icon-navbar/Mail-box.svg"/>
									</span>
									<span>
										{__d('filemanager', 'file_nen')}
									</span>
								</span>
							{/if}
						</div>	
					</div>
				</div>
				
				<input class="search-input" nh-input="filter" data-type="{KEYWORD}" type="text" name="keyword" value="" placeholder="{__d('filemanager', 'nhap_ten_tep')}">

				{if empty($type_file)}
					<span nh-btn="clear-filter" class="btn btn-main btn-nav-top btn-clear-filter" data-tooltip="{__d('filemanager', 'tai_trang')}">
						<img src="{ADMIN_PATH}/myfilemanager/assests/img/icon-navbar/Update.svg"/>
					</span>
				{/if}
			</div>
		</div>
	</div>
</div>