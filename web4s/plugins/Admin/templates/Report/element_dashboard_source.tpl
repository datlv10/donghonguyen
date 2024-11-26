<div class="kt-widget14">
    <div class="kt-widget14__header mb-0">
        <h3 class="kt-widget14__title">
            {__d('admin', 'doanh_thu_theo_nguon_don_hang')}
        </h3>
    </div>
    <div class="kt-widget14__content justify-content-center">
        {if !empty($report)}
            <div class="kt-widget14__chart">
                <div id="chart-source" style="height: 147px; width: 150px;"></div>
                <input id="data-chart-source" type="hidden" value="{htmlentities($report|@json_encode)}">
            </div>
        {else}
            {__d('admin', 'chua_co_thong_tin')}
        {/if}
    </div>
</div>