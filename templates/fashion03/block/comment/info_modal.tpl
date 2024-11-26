{strip}
<div id="info-comment-modal" class="modal fade" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-md">
    	<div class="modal-content">
	    	<div class="modal-body">
	            <h3 class="modal-comment-title text-center">
	            	<b>{__d('template', 'thong_tin_nguoi_gui')}</b>
	            </h3>

	            <div class="modal-comment-content">
	                <div class="text-center  mb-5">
		                {__d('template', 'de_gui_binh_luan_ban_vui_long_cung_cap_them_thong_tin_lien_he')}
		            </div>
	                <form id="info-comment-form" method="POST" autocomplete="off">
	                    <div class="form-group">
	                    	<label>
								{__d('template', 'ten_hien_thi')}: 
								<span class="required">*</span>
							</label>
	                        <input name="full_name" class="form-control" type="text">
	                    </div>

	                    <div class="form-group">
	                    	<label>
								{__d('template', 'email')}: 
								<span class="required">*</span>
							</label>
	                        <input name="email" class="form-control" type="text">
	                    </div>

	                    <div class="form-group">
	                    	<label>
								{__d('template', 'so_dien_thoai')}: 
								<span class="required">*</span>
							</label>
	                        <input name="phone" class="form-control" type="text">
	                    </div>
	                </form>
	            </div>
	           	<button id="btn-send-info" type="button" class="col-12 btn btn-primary">
	                {__d('template', 'cap_nhat')}
	            </button>
	        </div>
    	</div>
    </div>
</div>
{/strip}