<div class="clearfix p-15">
    <div class="row">
        <div class="col-xl-12 pl-20">
            <div class="kt-timeline-v1 kt-timeline-v1--justified nh-timeline-v1">
                <div class="kt-timeline-v1__items pt-5 mb-0">
                    {foreach from = $order_returned item = returned key = k_returned name="returned_foreach"}
                        <div class="kt-timeline-v1__marker"></div>
                        <div class="kt-timeline-v1__item">
                            <div class="kt-timeline-v1__item-circle">
                                <div class="kt-bg-dark"></div>
                            </div>

                            <div class="kt-timeline-v1__item-content p-10">
                                <div class="kt-timeline-v1__item-title p-5">
                                    {if !empty($returned.code)}
                                        <span class="mr-10">
                                            {$returned.code}
                                        </span>
                                    {/if}

                                    {if isset($returned.status) && $returned.status == {WAITING_RECEIVING}}
                                        <span class="kt-badge kt-badge--dark kt-badge--inline kt-badge--rounded h-20">
                                            {__d('admin', 'cho_nhan_hang')}
                                        </span>
                                    {/if}

                                    {if isset($returned.status) && $returned.status == {RECEIVED}}
                                        <span class="kt-badge kt-badge--dark kt-badge--inline kt-badge--rounded h-20">
                                            {__d('admin', 'da_nhan_hang')}
                                        </span>
                                    {/if}

                                    <i data-toggle="collapse" href="#order-returned-{$returned.id}" class="fa fa-caret-right ml-10 mt-2 cursor-p h-20 float-right icon-arrow-collapse"></i>
                                </div>

                                <div id="order-returned-{$returned.id}" class="clearfix collapse">
                                    <div class="kt-separator kt-separator--space-lg kt-separator--border-soild mt-10 mb-10"></div>

                                    <div class="kt-timeline-v1__item-body mt-10 p-5">
                                        <div class="row mb-10">
                                            <div class="col-lg-4 col-xl-4">
                                                <div class="form-group form-group-xs row">
                                                    <label class="col-12">
                                                        {__d('admin', 'ngay_lap_don')}
                                                    </label>
                                                    <div class="col-12">
                                                        {if !empty($returned.date_create)}
                                                            <span class="kt-font-bolder">
                                                                {$this->UtilitiesAdmin->convertIntgerToDateTimeString($returned.date_create)}
                                                            </span>
                                                        {/if}
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-lg-4 col-xl-4">
                                                <div class="form-group form-group-xs row">
                                                    <label class="col-12">
                                                        {__d('admin', 'ngay_tra_hang')}
                                                    </label>
                                                    <div class="col-12">
                                                        {if !empty($returned.date_received)}
                                                            <span class="kt-font-bolder">
                                                                {$this->UtilitiesAdmin->convertIntgerToDateTimeString($returned.date_received)}
                                                            </span>
                                                        {/if}
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-lg-4 col-xl-4">
                                                <div class="form-group form-group-xs row">
                                                    <label class="col-12">
                                                        {__d('admin', 'ghi_chu')}
                                                    </label>
                                                    <div class="col-12">
                                                        <span class="kt-font-bolder">
                                                            {if !empty($returned.note)}
                                                                {$returned.note}
                                                            {/if}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div> 

                                        <div class="row mb-10">
                                            <div class="col-12">
                                                <div class="form-group form-group-xs row">
                                                    <label class="col-12">
                                                        {__d('admin', 'san_pham')}
                                                    </label>
                                                    {if !empty($returned.items)}
                                                        {foreach from = $returned.items item = returned_item}
                                                            <div class="col-12 mb-10">
                                                                <span class="kt-font-bolder">
                                                                    {if !empty($returned_item.quantity)}
                                                                        {$returned_item.quantity}
                                                                        x
                                                                    {/if}

                                                                    {if !empty($returned_item.name_extend)}
                                                                        {$returned_item.name_extend}
                                                                    {/if}                                          
                                                                </span>
                                                            </div>
                                                        {/foreach}
                                                    {/if}
                                                </div>
                                            </div>
                                        </div> 
                                    </div>
                                </div>
                            </div>
                        </div>
                    {/foreach}                                            
                </div>
            </div>
        </div>
    </div>
</div>