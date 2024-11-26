<div class="kt-portlet__head">
    <div class="kt-portlet__head-label">
        <a href="{ADMIN_PATH}/order" class="kt-portlet__head-title">
            {__d('admin', 'don_hang')}
        </a>
    </div>
</div>

<div class="kt-portlet__body kt-portlet__body--fluid">
    <div class="kt-widget12">
        <div class="kt-widget12__chart mt-auto" style="height:350px;">
            <canvas id="chart-order"></canvas>

            <input id="data-chart-order" type="hidden" value="{if !empty($chart_data)}{htmlentities($chart_data|@json_encode)}{/if}">
        </div>
    </div>
</div>