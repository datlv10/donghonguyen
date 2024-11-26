<div id="cancel-order-modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
			<div class="modal-body p-4">
				<div class="d-flex justify-content-between align-items-center mb-4">
					<div class="h5 modal-title font-weight-bold text-uppercase">{__d('template', 'xac_nhan_huy_don_hang')}</div>
					<button type="button" class="close effect-rotate icon-close" data-dismiss="modal" aria-label="Close">
						<i class="fa-light fa-xmark"></i>
					</button>
				</div>
                <textarea name="note" placeholder="{__d('template', 'ly_do_huy_don')}" class="form-control"></textarea>
                <input type="hidden" value="" name="order_id">
                <button nh-confirm class="btn btn-submit mt-3">
					{__d('template', 'dong_y')}
				</button>
			</div>
        </div>   
    </div>
</div>