<div class="kt-subheader  kt-grid__item" id="kt_subheader">
    <div class="kt-container  kt-container--fluid ">
        <div class="kt-subheader__main">
            <h3 class="kt-subheader__title">
                {__d('admin', 'tong_quan')}
            </h3>
        </div>
    </div>
</div>

<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid entire-dashboard">
    <div class="row">
        {if !empty($plugins[{PRODUCT}])}
            <div class="col-lg-6 col-xl-4 order-lg-1 order-xl-1">
                <div id="wrap-order-statistics" class="kt-portlet kt-portlet--fit kt-portlet--head-lg kt-portlet--head-overlay kt-portlet--skin-solid kt-portlet--height-fluid"></div>
            </div>

            <div class="col-lg-6 col-xl-8 order-lg-1 order-xl-1">
                <div id="wrap-order-chart" class="kt-portlet kt-portlet--height-fluid"></div>
            </div>

            <div class="col-lg-6 col-xl-4 order-lg-1 order-xl-1">
                <div id="wrap-product-statistics" class="kt-portlet kt-portlet--height-fluid"></div>
            </div>
        {/if}

        <div class="col-lg-6 col-xl-4 order-lg-1 order-xl-1">
            <div id="wrap-article-statistics" class="kt-portlet kt-portlet--height-fluid"></div>
        </div>

        <div class="col-lg-6 col-xl-4 order-lg-1 order-xl-1">
            <div id="wrap-customer" class="kt-portlet kt-portlet--height-fluid"></div>
        </div>

        <div class="col-lg-6 col-xl-4 order-lg-1 order-xl-1">
            <div id="wrap-website-info" class="kt-portlet kt-portlet--height-fluid"></div>
        </div>     

        <div class="col-lg-6 col-xl-4 order-lg-1 order-xl-1">
            <div id="wrap-website-setting" class="kt-portlet kt-portlet--height-fluid"></div>
        </div>

        <div class="col-lg-6 col-xl-4 order-lg-1 order-xl-1">
            <div id="wrap-website-seo" class="kt-portlet kt-portlet--height-fluid"></div>
        </div>

        <div class="col-lg-6 col-xl-4 order-lg-1 order-xl-1">
            <div class="kt-portlet kt-portlet--height-fluid">
                <div class="kt-portlet__body kt-portlet__body--fit">
                    <div class="kt-widget17">
                        <div class="kt-widget17__stats m-0 w-100 text-center">
                            <div class="kt-widget17__items">
                                <div id="wrap-website-expiry" class="kt-widget17__item cursor-default p-0"></div>
                            </div>
                            <div class="kt-widget17__items">
                                <div id="wrap-website-duration" class="kt-widget17__item cursor-default p-0"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>   
    </div>
</div>