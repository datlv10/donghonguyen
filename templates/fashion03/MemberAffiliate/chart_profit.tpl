<div style="height:350px;">
    <canvas id="chart-profit"></canvas>
    <input id="data-chart-profit" type="hidden" value="{if !empty($chart_data)}{htmlentities($chart_data|@json_encode)}{/if}">
</div>
