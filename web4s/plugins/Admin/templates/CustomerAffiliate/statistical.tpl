<div class="kt-subheader  kt-grid__item" id="kt_subheader">
    <div class="kt-container  kt-container--fluid ">
        <div class="kt-subheader__main">
            <h3 class="kt-subheader__title">
                {$title_for_layout}
            </h3>
        </div>
    </div>
</div>

<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
    <div class="row">
        {if !empty($plugins[{AFFILIATE}])}
            <div class="col-lg-5 col-xl-3 order-lg-1 order-xl-1">
                <div id="wrap-order-statistics" class="kt-portlet kt-portlet--fit kt-portlet--head-lg kt-portlet--head-overlay kt-portlet--skin-solid kt-portlet--height-fluid"></div>
            </div>

            <div class="col-lg-7 col-xl-9 order-lg-1 order-xl-1">
                <div id="wrap-order-chart" class="kt-portlet kt-portlet--height-fluid"></div>
            </div>

            <div class="col-lg-8 col-xl-8 order-lg-1 order-xl-1">
                <div id="wrap-partner-statistics" class="kt-portlet kt-portlet--height-fluid"></div>
            </div>

            <div class="col-lg-4 col-xl-4 order-lg-1 order-xl-1">
                <div id="wrap-setting-commissions" class="kt-portlet kt-portlet--height-fluid"></div>
            </div>  
        {/if}

    </div>
</div>