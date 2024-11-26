<table class="table nh-table-item">
    <thead class="thead-light">
        <tr>
            <th class="text-center">
                <i class="fa fa-image"></i>
            </th>
            <th>
                {__d('admin', 'ten_phien_ban')}
            </th>
            <th>
                {__d('admin', 'ma')}
            </th>
            <th>{__d('admin', 'gia')}</th>
            <th>{__d('admin', 'gia_dac_biet')}</th>
            <th class="text-center">{__d('admin', 'so_luong')}</th>
            <th class="text-center">{__d('admin', 'trang_thai')}</th>
        </tr>
    </thead>
    <tbody>
        {if !empty($items)}
            {foreach from = $items item = item}
                <tr class="kt-font-bold">
                    <td class="text-center">
                        {if !empty($item.images[0])}
                            <img src="{CDN_URL}{$this->Utilities->getThumbs($item.images[0], 50)}" style="height: 50px;">
                        {else}
                            ...
                        {/if}
                    </td>

                    <td>
                        {if !empty($item.name)}
                            {$item.name}
                        {else}
                            ...
                        {/if}
                    </td>

                    <td>
                        {if !empty($item.code)}
                            {$item.code}
                        {else}
                            ...
                        {/if}
                    </td>

                    <td>
                        {if !empty($item.price)}
                            {$item.price|number_format:0:'.':','}
                        {else}
                            ...
                        {/if}
                    </td>

                    <td>
                        {if !empty($item.price_special)}
                            {$item.price_special|number_format:0:'.':','}
                        {else}
                            ...
                        {/if}
                    </td>

                    <td class="text-center">
                        {if !empty($item.quantity_available)}
                            {$item.quantity_available|number_format:0:'.':','}
                        {else}
                            ...
                        {/if}
                    </td>
                    <td class="text-center">
                        {if !empty($item.status)}
                            <span class="kt-badge kt-badge--success kt-badge--inline kt-badge--pill">
                                {__d('admin', 'hoat_dong')}
                            </span>
                        {else}
                            <span class="kt-badge kt-badge--danger kt-badge--inline kt-badge--pill">
                                {__d('admin', 'khong_hoat_dong')}
                            </span>
                        {/if}
                    </td>
                </tr>
            {/foreach}
        {/if}
    </tbody>
</table>