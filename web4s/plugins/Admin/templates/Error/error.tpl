<div class="kt-subheader kt-grid__item" id="kt_subheader">
    <div class="kt-container  kt-container--fluid ">
        <div class="kt-subheader__main">
            <h3 class="kt-subheader__title"></h3>
        </div>
    </div>
</div>
<div class="kt-container kt-container--fluid  kt-grid__item kt-grid__item--fluid">
    <div class="kt-portlet nh-portlet">
        <div class="kt-portlet__body">            
            <div class="alert alert-danger m-0" >
                <div class="alert-icon">
                    <i class="flaticon-warning"></i>
                </div>

                <div class="alert-text">
                    {if !empty($message)}
                        {$message}
                    {else}
                        {__d('admin', 'loi_xu_ly_du_lieu')}                    
                    {/if}
                </div>
            </div>
        </div>
    </div>
</div>
