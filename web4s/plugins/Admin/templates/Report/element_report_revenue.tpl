{assign var = data_field value = ''}
{if !empty($report_order['sort']['field'])}
    {assign var = data_field value = $report_order['sort']['field']}
{/if}
{assign var = data_sort value = ''}
{if !empty($report_order['sort']['sort'])}
    {assign var = data_sort value = $report_order['sort']['sort']}
{/if}
<div class="kt-portlet__body">
    <div id="kt_amcharts_6" style="height: 500px;"></div>
    <input id="data-chart-order" type="hidden" value="{if !empty($report_order['chart'])}{htmlentities($report_order['chart']|@json_encode)}{/if}">
</div>
<div class="kt-portlet__body">
    <div class="table-responsive">
        <table class="table table-striped- table-bordered table-hover table-checkable dataTable no-footer dtr-inline">
            <thead class="thead-light">
                <tr>
                    <th rowspan="2" data-toggle="kt-tooltip" title='{__d('admin', 'so_thu_tu')}' style="width: 55px;">
                        {__d('admin', 'stt')}
                    </th>
                    <th rowspan="2" data-field="created" 
                        data-sort="{if !empty($data_sort) && $data_sort eq 'asc' && $data_field eq 'created'}desc{else}asc{/if}" 
                        class="{if !empty($data_field) && $data_field eq 'created'}sorted{/if}">
                        {__d('admin', 'thoi_gian')}
                    </th>
                    <th rowspan="2" data-field="number_order" 
                        data-sort="{if !empty($data_sort) && $data_sort eq 'asc' && $data_field eq 'number_order'}desc{else}asc{/if}" 
                        class="{if !empty($data_field) && $data_field eq 'number_order'}sorted{/if}">
                        {__d('admin', 'so_don')}
                    </th>
                    <th rowspan="2" data-toggle="kt-tooltip" title='{__d('admin', 'so_luong_san_pham')}' data-field="count_items"  
                        data-sort="{if !empty($data_sort) && $data_sort eq 'asc' && $data_field eq 'count_items'}desc{else}asc{/if}" 
                        class="{if !empty($data_field) && $data_field eq 'count_items'}sorted{/if}">
                        {__d('admin', 'slsp')}
                    </th>
                    <th rowspan="2" data-toggle="kt-tooltip" title='{__d('admin', 'doanh_thu_truoc_chiet_khau')}' data-field="origin" 
                        data-sort="{if !empty($data_sort) && $data_sort eq 'asc' && $data_field eq 'origin'}desc{else}asc{/if}" 
                        class="{if !empty($data_field) && $data_field eq 'origin'}sorted{/if}">
                        {__d('admin', 'doanh_thu_tck')}
                    </th>
                    <th rowspan="2">
                        {__d('admin', 'chiet_khau')}
                    </th>
                    <th rowspan="2" data-field="shipping" 
                        data-sort="{if !empty($data_sort) && $data_sort eq 'asc' && $data_field eq 'shipping'}desc{else}asc{/if}" 
                        class="{if !empty($data_field) && $data_field eq 'shipping'}sorted{/if}">
                        {__d('admin', 'phi_van_chuyen')}
                    </th>
                    <th rowspan="2" data-field="vat" 
                        data-sort="{if !empty($data_sort) && $data_sort eq 'asc' && $data_field eq 'vat'}desc{else}asc{/if}" 
                        class="{if !empty($data_field) && $data_field eq 'vat'}sorted{/if}">
                        VAT
                    </th>
                    <th rowspan="2" data-field="total"
                        data-sort="{if !empty($data_sort) && $data_sort eq 'asc' && $data_field eq 'total'}desc{else}asc{/if}" 
                        class="{if !empty($data_field) && $data_field eq 'total'}sorted{/if}">
                        {__d('admin', 'tam_tinh')}
                    </th>
                    <th rowspan="2" data-field="debt"
                        data-sort="{if !empty($data_sort) && $data_sort eq 'asc' && $data_field eq 'debt'}desc{else}asc{/if}" 
                        class="{if !empty($data_field) && $data_field eq 'debt'}sorted{/if}">
                        {__d('admin', 'con_no')}
                    </th>
                    <th rowspan="2">
                        {__d('admin', 'doanh_thu')}
                    </th>
                    <th rowspan="2">
                        {__d('admin', 'don_huy')}
                    </th>
                    <th rowspan="2" data-toggle="kt-tooltip" title='{__d('admin', 'ti_le_chuyen_doi_thanh_cong')}'>
                        {__d('admin', 'cvr')}
                    </th>
                </tr>
            </thead>
            <tbody>
                {if !empty($report_order['data'])}
                    {foreach  from = $report_order['data'].item_report item = report}
                        <tr>
                            <td>{$report@index + 1}</td> 
                            <td>
                                {if !empty($report.the_time)}
                                    {$report.the_time}
                                {/if}
                            </td>
                            <td>
                                {if !empty($report.number_order)}
                                    {$report.number_order|number_format:0:".":","}
                                {/if}
                            </td>
                            <td>
                                {if !empty($report.count_items)}
                                    {$report.count_items|number_format:0:".":","}
                                {/if}
                            </td>
                            <td>
                                {if !empty($report.origin)}
                                    {$report.origin|number_format:0:".":","}
                                {/if}
                            </td>
                            <td>
                                {if !empty($report.all_discount)}
                                    {$report.all_discount|number_format:0:".":","}
                                {/if}
                            </td>
                            <td>
                                {if !empty($report.shipping)}
                                    {$report.shipping|number_format:0:".":","}
                                {/if}
                            </td>
                            <td>
                                {if !empty($report.vat)}
                                    {$report.vat|number_format:0:".":","}
                                {/if}
                            </td>
                            <td>
                                {if !empty($report.total)}
                                    {$report.total|number_format:0:".":","}
                                {/if}
                            </td>
                            <td>
                                {if !empty($report.debt)}
                                    {$report.debt|number_format:0:".":","}
                                {/if}
                            </td>
                            <td>
                                {if !empty($report.order_done)}
                                    <strong>{$report.order_done|number_format:0:".":","}</strong>
                                {/if}
                            </td>
                            <td>
                                {if !empty($report.order_cancel)}
                                    {$report.order_cancel|number_format:0:".":","}
                                {/if}
                            </td>
                            <td>
                                {if !empty($report.cvr)}
                                    {$report.cvr}
                                {else}
                                    0
                                {/if}
                                %
                            </td>
                        </tr>
                    {/foreach}
                {else}
                    <tr>
                        <td colspan="9">
                            {__d('admin', 'chua_co_thong_tin')}
                        </td>
                    </tr>
                {/if}
            </tbody>
            <tfoot>
                <tr class="table-success">
                    <td colspan="2">{__d('admin', 'tong')}</td>
                    <td>
                        {if !empty($report_order['data'].total_number_order)}
                            {$report_order['data'].total_number_order|number_format:0:".":","}
                        {/if}
                    </td>
                    <td>
                        {if !empty($report_order['data'].total_count_items)}
                            {$report_order['data'].total_count_items|number_format:0:".":","}
                        {/if}
                    </td>
                    <td>
                        {if !empty($report_order['data'].total_origin)}
                            {$report_order['data'].total_origin|number_format:0:".":","}
                        {/if}
                    </td>
                    <td>
                        {if !empty($report_order['data'].total_all_discount)}
                            {$report_order['data'].total_all_discount|number_format:0:".":","}
                        {/if}
                    </td>

                    <td>
                        {if !empty($report_order['data'].total_shipping)}
                            {$report_order['data'].total_shipping|number_format:0:".":","}
                        {/if}
                    </td>
                    <td>
                        {if !empty($report_order['data'].total_vat)}
                            {$report_order['data'].total_vat|number_format:0:".":","}
                        {/if}
                    </td>
                    <td>
                        {if !empty($report_order['data'].total_total)}
                            {$report_order['data'].total_total|number_format:0:".":","}
                        {/if}
                    </td>
                    <td>
                        {if !empty($report_order['data'].total_debt)}
                            {$report_order['data'].total_debt|number_format:0:".":","}
                        {/if}
                    </td>
                    <td>
                        {if !empty($report_order['data'].total_order_done)}
                            {$report_order['data'].total_order_done|number_format:0:".":","}
                        {/if}
                    </td>
                    <td>
                        {if !empty($report_order['data'].total_order_cancel)}
                            {$report_order['data'].total_order_cancel|number_format:0:".":","}
                        {/if}
                    </td>
                    <td></td>
                </tr>
                <tr class="table-warning">
                    <td colspan="2">{__d('admin', 'trung_binh')}</td>
                    <td>
                        {if !empty($report_order['data'].avg_number_order)}
                            {$report_order['data'].avg_number_order|number_format:0:".":","}
                        {/if}
                    </td>
                    <td>
                        {if !empty($report_order['data'].avg_count_items)}
                            {$report_order['data'].avg_count_items|number_format:0:".":","}
                        {/if}
                    </td>
                    <td>
                        {if !empty($report_order['data'].avg_origin)}
                            {$report_order['data'].avg_origin|number_format:0:".":","}
                        {/if}
                    </td>
                    <td>
                        {if !empty($report_order['data'].avg_all_discount)}
                            {$report_order['data'].avg_all_discount|number_format:0:".":","}
                        {/if}
                    </td>

                    <td>
                        {if !empty($report_order['data'].avg_shipping)}
                            {$report_order['data'].avg_shipping|number_format:0:".":","}
                        {/if}
                    </td>
                    <td>
                        {if !empty($report_order['data'].avg_vat)}
                            {$report_order['data'].avg_vat|number_format:0:".":","}
                        {/if}
                    </td>
                    <td>
                        {if !empty($report_order['data'].avg_total)}
                            {$report_order['data'].avg_total|number_format:0:".":","}
                        {/if}
                    </td>
                    <td>
                        {if !empty($report_order['data'].avg_debt)}
                            {$report_order['data'].avg_debt|number_format:0:".":","}
                        {/if}
                    </td>
                    <td>
                        {if !empty($report_order['data'].avg_order_done)}
                            {$report_order['data'].avg_order_done|number_format:0:".":","}
                        {/if}
                    </td>
                    <td>
                        {if !empty($report_order['data'].avg_order_cancel)}
                            {$report_order['data'].avg_order_cancel|number_format:0:".":","}
                        {/if}
                    </td>
                    <td>
                        {if !empty($report_order['data'].avg_cvr)}
                            {$report_order['data'].avg_cvr}%
                        {/if}
                    </td>
                </tr>
            </tfoot>
        </table>
        <div class="kt-datatable__pager">
            {if !empty($report_order.pagination)}
                {assign var = pagination value = $report_order.pagination}
                {$this->element('../Report/pagination', ['pagination' => $pagination])}
            {/if}
        </div>
    </div>
</div>