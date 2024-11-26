<div class="kt-widget14">
    <div class="kt-widget14__header kt-margin-b-30">
        <h3 class="kt-widget14__title">
            {__d('admin', 'doanh_thu_theo_tinh_thanh')}
        </h3>
    </div>
    <div class="kt-widget14__chart" style="height: 355px;">
        {if !empty($chart_data)}
            <canvas id="chart-city"></canvas>
            <input id="data-chart-city" type="hidden" value="{htmlentities($chart_data|@json_encode)}">
        {else}
            {__d('admin', 'chua_co_thong_tin')}
        {/if}
    </div>
</div>