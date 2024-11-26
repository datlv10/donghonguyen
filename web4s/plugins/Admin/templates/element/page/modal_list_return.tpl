<div id="list-return-modal" class="modal fade" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content" style="min-height: 590px;">
            <div class="modal-header">
                <h5 class="modal-title">
                    {__d('admin', 'danh_sach_don_hang_thanh_cong')}
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

                <!--begin: Search Form -->
                <div class="kt-form kt-form--label-right kt-margin-t-20 kt-margin-b-10">
                    <div class="row align-items-center">
                        <div class="col-xl-8 order-2 order-xl-1">
                            <div class="row align-items-center">
                                <div class="col-md-4 kt-margin-b-20-tablet-and-mobile">
                                    <div class="kt-input-icon kt-input-icon--left">
                                        <input id="nh-keyword-modal" name="keyword" type="text" class="form-control form-control-sm" placeholder="{__d('admin', 'tim_kiem')}...">
                                        <span class="kt-input-icon__icon kt-input-icon__icon--left">
                                            <span><i class="la la-search"></i></span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!--end: Search Form -->
            </div>
            <div class="modal-body modal-body-fit">

                <!--begin: Datatable -->
                <div class="kt-datatable-return"></div>

                <!--end: Datatable -->
            </div>
        </div>
    </div>
</div>                 