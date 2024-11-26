{assign var = data_field value = ''}
{if !empty($report['sort']['field'])}
    {assign var = data_field value = $report['sort']['field']}
{/if}
{assign var = data_sort value = ''}
{if !empty($report['sort']['sort'])}
    {assign var = data_sort value = $report['sort']['sort']}
{/if}
<div class="kt-portlet__body">
    <div class="table-responsive">
        <table class="table table-striped- table-bordered table-hover table-checkable dataTable no-footer dtr-inline">
            <thead class="thead-light">
                <tr>
                    <th rowspan="2" data-toggle="kt-tooltip" title='{__d('admin', 'so_thu_tu')}' style="width: 55px;">
                        {__d('admin', 'stt')}
                    </th>
                    <th rowspan="2" data-field="code" 
                        data-sort="{if !empty($data_sort) && $data_sort eq 'asc' && $data_field eq 'code'}desc{else}asc{/if}" 
                        class="{if !empty($data_field) && $data_field eq 'code'}sorted{/if}">
                        {__d('admin', 'ma')}
                    </th>
                    <th rowspan="2" data-field="name" 
                        data-sort="{if !empty($data_sort) && $data_sort eq 'asc' && $data_field eq 'name'}desc{else}asc{/if}" 
                        class="{if !empty($data_field) && $data_field eq 'name'}sorted{/if}">
                        {__d('admin', 'san_pham')}
                    </th>
                    <th rowspan="2" data-toggle="kt-tooltip" title='{__d('admin', 'gia_trung_binh')}' data-field="price" 
                        data-sort="{if !empty($data_sort) && $data_sort eq 'asc' && $data_field eq 'price'}desc{else}asc{/if}" 
                        class="{if !empty($data_field) && $data_field eq 'price'}sorted{/if}">
                        {__d('admin', 'giatb')}
                    </th>
                    <th rowspan="2" data-field="discount" 
                        data-sort="{if !empty($data_sort) && $data_sort eq 'asc' && $data_field eq 'discount'}desc{else}asc{/if}" 
                        class="{if !empty($data_field) && $data_field eq 'discount'}sorted{/if}">
                        {__d('admin', 'chiet_khau')}
                    </th>
                    <th rowspan="2" data-field="vat" 
                        data-sort="{if !empty($data_sort) && $data_sort eq 'asc' && $data_field eq 'vat'}desc{else}asc{/if}" 
                        class="{if !empty($data_field) && $data_field eq 'vat'}sorted{/if}">
                        VAT
                    </th>
                    <th rowspan="2" data-toggle="kt-tooltip" title='{__d('admin', 'so_luong_ban')}' data-field="quantity" 
                        data-sort="{if !empty($data_sort) && $data_sort eq 'asc' && $data_field eq 'quantity'}desc{else}asc{/if}" 
                        class="{if !empty($data_field) && $data_field eq 'quantity'}sorted{/if}">
                        {__d('admin', 'slb')}
                    </th>
                    <th rowspan="2" data-field="total" 
                        data-sort="{if !empty($data_sort) && $data_sort eq 'asc' && $data_field eq 'total'}desc{else}asc{/if}" 
                        class="{if !empty($data_field) && $data_field eq 'total'}sorted{/if}">
                        {__d('admin', 'tam_tinh')}
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
                {if !empty($report['data'])}
                    {foreach  from = $report['data'].item_report item = item_report}
                        <tr>
                            <td>{$item_report@index + 1}</td> 
                            <td>
                                {if !empty($item_report.code)}
                                    {$item_report.code}
                                {/if}
                            </td>
                            <td>
                                {if !empty($item_report.name_extend)}
                                    {$item_report.name_extend}
                                {/if}
                            </td>
                            <td>
                                {if !empty($item_report.price)}
                                    {$item_report.price|number_format:0:".":","}
                                {/if}
                            </td>
                            
                            <td>
                                {if !empty($item_report.discount)}
                                    {$item_report.discount|number_format:0:".":","}
                                {/if}
                            </td>
                            <td>
                                {if !empty($item_report.vat)}
                                    {$item_report.vat|number_format:0:".":","}
                                {/if}
                            </td>
                            <td>
                                {if !empty($item_report.quantity)}
                                    {$item_report.quantity|number_format:0:".":","}
                                {/if}
                            </td>
                            <td>
                                {if !empty($item_report.total)}
                                    {$item_report.total|number_format:0:".":","}
                                {/if}
                            </td>
                            <td>
                                {if !empty($item_report.product_done)}
                                    <strong>{$item_report.product_done|number_format:0:".":","}</strong>
                                {/if}
                            </td>
                            <td>
                                {if !empty($item_report.product_cancel)}
                                    {$item_report.product_cancel|number_format:0:".":","}
                                {/if}
                            </td>
                            <td>
                                {if !empty($item_report.cvr)}
                                    {$item_report.cvr}
                                {else}
                                    0
                                {/if}
                                %
                            </td>
                        </tr>
                    {/foreach}
                {else}
                    <tr>
                        <td colspan="8">{__d('admin', 'chua_co_thong_tin')}</td>
                    </tr>
                {/if}
            </tbody>
            <tfoot>
                <tr class="table-success">
                    <td colspan="6">{__d('admin', 'tong')}</td>
                    <td>
                        {if !empty($report['data'].total_quantity)}
                            {$report['data'].total_quantity|number_format:0:".":","}
                        {/if}
                    </td>
                    <td>
                        {if !empty($report['data'].total_total)}
                            {$report['data'].total_total|number_format:0:".":","}
                        {/if}
                    </td>
                    <td>
                        {if !empty($report['data'].total_product_done)}
                            {$report['data'].total_product_done|number_format:0:".":","}
                        {/if}
                    </td>
                    <td>
                        {if !empty($report['data'].total_product_cancel)}
                            {$report['data'].total_product_cancel|number_format:0:".":","}
                        {/if}
                    </td>
                    <td></td>
                </tr>

                <tr class="table-warning">
                    <td colspan="6">{__d('admin', 'trung_binh')}</td>
                    <td>
                        {if !empty($report['data'].avg_quantity)}
                            {$report['data'].avg_quantity|number_format:0:".":","}
                        {/if}
                    </td>
                    <td>
                        {if !empty($report['data'].avg_total)}
                            {$report['data'].avg_total|number_format:0:".":","}
                        {/if}
                    </td>
                    <td>
                        {if !empty($report['data'].avg_product_done)}
                            {$report['data'].avg_product_done|number_format:0:".":","}
                        {/if}
                    </td>
                    <td>
                        {if !empty($report['data'].avg_product_cancel)}
                            {$report['data'].avg_product_cancel|number_format:0:".":","}
                        {/if}
                    </td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
        <div class="kt-datatable__pager">
            {if !empty($report.pagination)}
                {assign var = pagination value = $report.pagination}
                {$this->element('../Report/pagination', ['pagination' => $pagination])}
            {/if}
        </div>
    </div>
</div>