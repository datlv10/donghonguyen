<div id="quick-add-product-modal" class="modal fade" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    {__d('admin', 'them_moi_san_pham')}
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                </button>
            </div>
            
            <div class="modal-body">
                <form id="quick-add-product-form" action="{ADMIN_PATH}/product/quick-save" method="POST" autocomplete="off">
                    <div class="form-group">
                        <label>
                            {__d('admin', 'ten_san_pham')}
                            <span class="kt-font-danger">*</span>
                        </label>
                        <input name="name" class="form-control form-control-sm" type="text" value="" >
                    </div>

                    <div class="row">                        
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>
                                    {__d('admin', 'ma_san_pham')}                                    
                                </label>
                                <input name="code" class="form-control form-control-sm" type="text" value="" >
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>
                                    {__d('admin', 'gia_ban')}
                                </label>
                                <input name="price" class="form-control form-control-sm number-input" type="text" value="" >
                            </div>
                        </div>
                    </div>                    
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">
                    {__d('admin', 'dong')}
                </button>
                
                <button id="quick-add-product-btn" type="button" class="btn btn-sm btn-primary">
                    {__d('admin', 'them_san_pham')}
                </button>
            </div>
        </div>
    </div>
</div>