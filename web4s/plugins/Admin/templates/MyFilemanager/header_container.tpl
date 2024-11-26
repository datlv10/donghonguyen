<div class="header-container">
	<div nh-wrap="breadcrumb" class="breadcrumb"></div>
	<div nh-wrap="sort-column" class="sort-column">
		<div class="list-sort-column">
			<ul class="column-left">
				<li nh-btn="sort" data-type="{NAME}" data-sort-type>
					<span >
						{__d('filemanager', 'ten_tep')}
					</span>
				</li>
			</ul>
			
			<ul class="column-right">
				<li class="size" nh-btn="sort" data-type="{SIZE}" data-sort-type>
					<span>
						{__d('filemanager', 'dung_luong')}
					<span>
				</li>

				<li class="date" nh-btn="sort" data-type="{TIME}" data-sort-typei>
					<span>
						{__d('filemanager', 'ngay_tao')}
					<span>
				</li>
			</ul>
		</div>
	</div>
</div>