<div class="kt-grid kt-wizard-v1 kt-wizard-v1--white nh-wizard" >
    <div class="kt-wizard-v1__nav">
        <div class="kt-wizard-v1__nav-items">
            <div class="kt-wizard-v1__nav-item {if !empty($order_status) && ($order_status == {NEW_ORDER} || $order_status == {CONFIRM} || $order_status == {PACKAGE} || $order_status == {EXPORT} || $order_status == {DONE} || $order_status == {CANCEL})}text-info {/if}">
                <div class="kt-wizard-v1__nav-body">
                    <div class="kt-wizard-v1__nav-icon">
                        <i class="flaticon-file-1"></i>
                    </div>
                    <div class="kt-wizard-v1__nav-label">
                        1. {__d('admin', 'dat_hang')}
                    </div>
                </div>
            </div>

            <i class="la la-angle-right fs-20 {if !empty($order_status) && ($order_status == {CONFIRM} || $order_status == {PACKAGE} || $order_status == {EXPORT} || $order_status == {DONE} || $order_status == {CANCEL})}text-info {/if}"></i>

            <div class="kt-wizard-v1__nav-item {if !empty($order_status) && ($order_status == {CONFIRM} || $order_status == {PACKAGE} || $order_status == {EXPORT} || $order_status == {DONE} || $order_status == {CANCEL})}text-info {/if}">
                <div class="kt-wizard-v1__nav-body">
                    <div class="kt-wizard-v1__nav-icon">
                        <i class="flaticon-list"></i>
                    </div>
                    <div class="kt-wizard-v1__nav-label">
                        2. {__d('admin', 'duyet_don')}
                    </div>
                </div>
            </div>

            <i class="la la-angle-right fs-20 {if !empty($order_status) && ($order_status == {PACKAGE} || $order_status == {EXPORT} || $order_status == {DONE})}text-info {/if}"></i>

            <div class="kt-wizard-v1__nav-item {if !empty($order_status) && ($order_status == {PACKAGE} || $order_status == {EXPORT} || $order_status == {DONE})}text-info {/if}">
                <div class="kt-wizard-v1__nav-body">
                    <div class="kt-wizard-v1__nav-icon">
                        <i class="flaticon-open-box"></i>
                    </div>
                    <div class="kt-wizard-v1__nav-label">
                        3. {__d('admin', 'dong_goi')}
                    </div>
                </div>
            </div>

            <i class="la la-angle-right fs-20 {if !empty($order_status) && ($order_status == {EXPORT} || $order_status == {DONE})}text-info {/if}"></i>

            <div class="kt-wizard-v1__nav-item {if !empty($order_status) && ($order_status == {EXPORT} || $order_status == {DONE})}text-info {/if}">
                <div class="kt-wizard-v1__nav-body">
                    <div class="kt-wizard-v1__nav-icon">
                        <i class="flaticon-paper-plane"></i>
                    </div>
                    <div class="kt-wizard-v1__nav-label">
                        4. {__d('admin', 'xuat_kho')}
                    </div>
                </div>
            </div>

            <i class="la la-angle-right fs-20 {if !empty($order_status) && $order_status == {DONE}}text-info {/if}"></i>
            {if !empty($order_status) && $order_status == {CANCEL}}
                <div class="kt-wizard-v1__nav-item text-danger">
                    <div class="kt-wizard-v1__nav-body">
                        <div class="kt-wizard-v1__nav-icon">
                            <i class="flaticon-cancel"></i>
                        </div>
                        <div class="kt-wizard-v1__nav-label">
                            5. {__d('admin', 'huy_don')}
                        </div>
                    </div>
                </div>            
            {else}
                <div class="kt-wizard-v1__nav-item {if !empty($order_status) && $order_status == {DONE}}text-info {/if}">
                    <div class="kt-wizard-v1__nav-body">
                        <div class="kt-wizard-v1__nav-icon">
                            <i class="flaticon-list-3"></i>
                        </div>
                        <div class="kt-wizard-v1__nav-label">
                            5. {__d('admin', 'hoan_thanh')}
                        </div>
                    </div>
                </div>
            {/if}
        </div>
    </div>
</div>