{assign var = sex value = [
	'male' => __d('template', 'nam'),
	'female' => __d('template', 'nu'),
	'other' => __d('template', 'khac')
]}

{$this->element('breadcrumb', [
	'list_url' => [
		['title' => {$title_for_layout}]
	]
])}

<div class="container mt-3">
	<div class="row mx-n2">
		<div class="col-12 col-md-3 col-lg-3 px-2">
			{$this->element('../Member/element_menu')}
		</div>
		<div class="col-12 col-md-9 col-lg-9 px-2">
			<div class="bg-white h-100 p-4">
				<div class="font-weight-bold h4">
					{__d('template', 'ho_so_cua_toi')}
				</div>
				<p class="mb-5">{__d('template', 'quan_ly_thong_tin_ho_so_de_bao_mat_tai_khoan')}</p>

				<ul class="member-categories-section list-unstyled mb-0 mt-3">
					{if !empty($member.full_name)}
						<li class="d-flex justify-content-between pb-3 mb-3 border-bottom border-gray">
							<span>{__d('template', 'ten')}</span>
							<span>
							    <strong>{$member.full_name}</strong>
							</span>
						</li>
					{/if}
					
					{if !empty($member.sex)}
						<li class="d-flex justify-content-between pb-3 mb-3 border-bottom border-gray">
							<span>{__d('template', 'gioi_tinh')}</span>
							<span>
							    <strong>{$sex[$member.sex]}</strong>
							</span> 
						</li>
					{/if}
					
					{if !empty($member.birthday)}
						<li class="d-flex justify-content-between pb-3 mb-3 border-bottom border-gray">
							<span>{__d('template', 'ngay_sinh')}</span>
							<span>
							    <strong>{$member.birthday}</strong>
							</span> 
						</li>
					{/if}
					
					{if !empty($member.email)}
						<li class="d-flex justify-content-between pb-3 mb-3 border-bottom border-gray">
							<span>{__d('template', 'email')}</span>
							<span>
							    <strong>{$member.email}</strong>
							</span> 
						</li>
					{/if}
					{if !empty($member.phone)}
						<li class="d-flex justify-content-between pb-3 mb-3 border-bottom border-gray">
							<span>{__d('template', 'so_dien_thoai')}</span>
							<span>
							    <strong>{$member.phone}</strong>
							</span> 
						</li>
					{/if}

					{if !empty($member.code)}
						<li class="d-flex justify-content-between pb-3 mb-3 border-bottom border-gray">
							<span>{__d('template', 'ma_khach_hang')}</span>
							<span>
							    <strong>{$member.code}</strong>
							</span> 
						</li>
					{/if}
				</ul>
				<div class="text-right mt-5">
            		<a href="/member/change-password" class="btn btn-submit">
            			{__d('template', 'thay_doi_mat_khau')}
            		</a>
            		<a href="/member/profile" class="btn btn-submit">
            			{__d('template', 'sua_thong_tin')}
            		</a>
        		</div>
			</div>
		</div>
	</div>	
</div>