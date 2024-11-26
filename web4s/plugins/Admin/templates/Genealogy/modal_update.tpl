<div id="modal-add-genealogy" class="modal fade" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    Thêm mới phả đồ
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                </button>
            </div>
            
            <div class="modal-body">
                <form id="main-form" action="{ADMIN_PATH}/genealogy/save{if !empty($id)}/{$id}{/if}" method="POST" autocomplete="off">
                    {$this->element("../Genealogy/element_update")}
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">
                    {__d('admin', 'dong')}
                </button>
                
                <button id="save-genealogy" type="button" class="btn btn-sm btn-primary">
                    Cập nhật
                </button>
            </div>
        </div>
    </div>
</div>