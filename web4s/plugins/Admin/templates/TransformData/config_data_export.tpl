{assign var = read_database value = []}
{if !empty($migrate.read_database.data)}
    {assign var = read_database value = $migrate.read_database.data}
{/if}

{assign var = config_data value = []}
{if !empty($migrate.config_data.data)}
    {assign var = config_data value = $migrate.config_data.data}
{/if}

{assign var = config_languages value = []}
{if !empty($config_data.languages)}
    {assign var = config_languages value = $config_data.languages}
{/if}

{assign var = languages value = $this->LanguageAdmin->getList()}

<div id="config-data-modal" class="modal fade" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    {__d('admin', 'cau_hinh_du_lieu_export')}
                </h5>
                <button type="button" class="close" data-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <label class="kt-font-bold">
                    {__d('admin', 'chon_ngon_ngu_cho_du_lieu_can_export')}
                </label>

                <div class="kt-checkbox-inline list-flags mb-20">
                    {foreach from=$languages item=name key=lang}
                        <label class="kt-checkbox kt-checkbox--tick kt-checkbox--success">
                            <input type="checkbox" name="languages[]" nh-config-lang value="{$lang}" {if !empty($config_languages) && in_array($lang,$config_languages)}checked{/if}> 
                            {$name}
                            <span></span>
                        </label>
                    {/foreach}
                </div>

                <div class="row">
                    <div class="col-sm-7">
                        <form id="config-data-form" action="{ADMIN_PATH}/transform-data/export/config-data" method="POST" autocomplete="off">

                            <table class="table table-bordered table-hover nh-table f0 mb-0">
                                <thead class="thead-light">
                                    <tr>
                                        <th>
                                            <label class="kt-checkbox kt-checkbox--bold kt-checkbox-inline ml-10">
                                                <input type="checkbox" class="check-all">
                                                <span></span>
                                            </label>
                                        </th>

                                        <th class="w-50">
                                            {__d('admin', 'muc_luc')}
                                        </th>

                                        <th class="w-20 text-center">
                                            {__d('admin', 'so_ban_ghi')}
                                        </th>

                                        <th class="w-20 text-center">
                                            {__d('admin', 'so_ban_ghi_export')}
                                        </th>
                                    </tr>
                                </thead>

                                {assign var = number_category_article value = 0}
                                {if !empty($read_database['number_category_article'])}
                                    {assign var = number_category_article value = $read_database['number_category_article']}
                                {/if}

                                {assign var = number_article value = 0}
                                {if !empty($read_database['number_article'])}
                                    {assign var = number_article value = $read_database['number_article']}
                                {/if}

                                {assign var = number_tag_article value = 0}
                                {if !empty($read_database['number_tag_article'])}
                                    {assign var = number_tag_article value = $read_database['number_tag_article']}
                                {/if}

                                {assign var = number_attribute_article value = 0}
                                {if !empty($read_database['number_attribute_article'])}
                                    {assign var = number_attribute_article value = $read_database['number_attribute_article']}
                                {/if}

                                {assign var = number_category_product value = 0}
                                {if !empty($read_database['number_category_product'])}
                                    {assign var = number_category_product value = $read_database['number_category_product']}
                                {/if}

                                {assign var = number_product value = 0}
                                {if !empty($read_database['number_product'])}
                                    {assign var = number_product value = $read_database['number_product']}
                                {/if}

                                {assign var = number_brand value = 0}
                                {if !empty($read_database['number_brand'])}
                                    {assign var = number_brand value = $read_database['number_brand']}
                                {/if}

                                {assign var = number_tag_product value = 0}
                                {if !empty($read_database['number_tag_product'])}
                                    {assign var = number_tag_product value = $read_database['number_tag_product']}
                                {/if}

                                {assign var = number_attribute_product value = 0}
                                {if !empty($read_database['number_attribute_product'])}
                                    {assign var = number_attribute_product value = $read_database['number_attribute_product']}
                                {/if}

                                {assign var = number_attribute_product_item value = 0}
                                {if !empty($read_database['number_attribute_product_item'])}
                                    {assign var = number_attribute_product_item value = $read_database['number_attribute_product_item']}
                                {/if}



                                {assign var = articles value = 0}
                                {if !empty($config_data['articles'])}
                                    {assign var = articles value = $config_data['articles']}
                                {/if}

                                {assign var = tag_article value = 0}
                                {if !empty($config_data['tag_article'])}
                                    {assign var = tag_article value = $config_data['tag_article']}
                                {/if}

                                {assign var = products value = 0}
                                {if !empty($config_data['products'])}
                                    {assign var = products value = $config_data['products']}
                                {/if}

                                {assign var = brands value = 0}
                                {if !empty($config_data['brands'])}
                                    {assign var = brands value = $config_data['brands']}
                                {/if}

                                {assign var = tag_product value = 0}
                                {if !empty($config_data['tag_product'])}
                                    {assign var = tag_product value = $config_data['tag_product']}
                                {/if}

                                <tbody>
                                    <tr>
                                        <td scope="row">
                                            <label class="kt-checkbox kt-checkbox--tick kt-checkbox--danger ml-10 mb-1">
                                                <input name="categories[article_check]" class="check-single" type="checkbox" {if $number_category_article > 0}checked{/if}>
                                                <span></span>
                                            </label>
                                        </td>

                                        <td>
                                            {__d('admin', 'danh_muc_bai_viet')}
                                            <span class="form-text text-muted">
                                                {__d('admin', 'export_du_lieu_danh_muc_bai_viet')}
                                            </span>
                                        </td>

                                        <td class="align-middle text-center text-primary kt-font-bold">
                                            {$number_category_article}
                                        </td>

                                        <td class="align-middle">
                                            <span nh-show-config type="categories_article" class="btn btn-sm btn-secondary w-100">
                                                <i class="fa fa-cog fs-14"></i>
                                                {__d('admin', 'cau_hinh')}
                                            </span>
                                        </td>      
                                    </tr>

                                    <tr>
                                        <th scope="row">
                                            <label class="kt-checkbox kt-checkbox--tick kt-checkbox--danger ml-10 mb-1">
                                                <input name="articles[check]" class="check-single" type="checkbox" {if $number_article > 0}checked{/if}>
                                                <span></span>
                                            </label>
                                        </th>

                                        <td>
                                            {__d('admin', 'bai_viet')} 
                                            <span class="form-text text-muted">
                                                {__d('admin', 'export_du_lieu_bai_viet')}
                                            </span>
                                        </td>

                                        <td class="align-middle text-center text-primary kt-font-bold">
                                            {$number_article}
                                        </td>

                                        <td class="align-middle">
                                            <div class="form-group mb-0">
                                                <input class="form-control form-control-sm number-input text-left text-success kt-font-bold" nh-record-export nh-record-max="{$number_article}" type="text" value="{if !empty($migrate.config_data.status) && $migrate.config_data.status == "{SUCCESS}"}{$articles}{else}{$number_article}{/if}" name="articles[record]">
                                            </div>
                                        </td>  
                                    </tr>

                                    <tr>
                                        <th scope="row">
                                            <label class="kt-checkbox kt-checkbox--tick kt-checkbox--danger ml-10 mb-1">
                                                <input name="attributes[article_check]" class="check-single" type="checkbox" {if $number_attribute_article > 0}checked{/if}>
                                                <span></span>
                                            </label>
                                        </th>

                                        <td>
                                            {__d('admin', 'thuoc_tinh_mo_rong_bai_viet')}
                                            <span class="form-text text-muted">
                                                {__d('admin', 'export_du_lieu_thuoc_tinh_mo_rong_cua_bai_viet')}
                                            </span>
                                        </td>

                                        <td class="align-middle text-center text-primary kt-font-bold">
                                            {$number_attribute_article}
                                        </td>

                                        <td class="align-middle">
                                            <span nh-show-config type="attributes_article" class="btn btn-sm btn-secondary w-100">
                                                <i class="fa fa-cog fs-14"></i>
                                                {__d('admin', 'cau_hinh')}
                                            </span>
                                        </td>  
                                    </tr>

                                    <tr>
                                        <th scope="row">
                                            <label class="kt-checkbox kt-checkbox--tick kt-checkbox--danger ml-10 mb-1">
                                                <input name="tags[article_check]" class="check-single" type="checkbox" {if $number_tag_article > 0}checked{/if}>
                                                <span></span>
                                            </label>
                                        </th>
                                        <td>
                                            {__d('admin', 'the_tag_bai_viet')}
                                            <span class="form-text text-muted">
                                                {__d('admin', 'export_du_lieu_the_tag_cua_bai_viet')}
                                            </span>
                                        </td>
                                        <td class="align-middle text-center text-primary kt-font-bold">
                                            {$number_tag_article}
                                        </td>

                                        <td class="align-middle">
                                            <div class="form-group mb-0">
                                                <input class="form-control form-control-sm number-input text-left text-success kt-font-bold" nh-record-export nh-record-max="{$number_tag_article}" type="text" value="{if !empty($migrate.config_data.status) && $migrate.config_data.status == "{SUCCESS}"}{$tag_article}{else}{$number_tag_article}{/if}" name="tags[article_record]">
                                            </div>
                                        </td>  
                                    </tr>

                                    <tr>
                                        <th scope="row">
                                            <label class="kt-checkbox kt-checkbox--tick kt-checkbox--danger ml-10 mb-1">
                                                <input name="categories[product_check]" class="check-single" type="checkbox" {if $number_category_product > 0}checked{/if}>
                                                <span></span>
                                            </label>
                                        </th>

                                        <td>
                                            {__d('admin', 'danh_muc_san_pham')}
                                            <span class="form-text text-muted">
                                                {__d('admin', 'export_du_lieu_danh_muc_san_pham')}
                                            </span>
                                        </td>

                                        <td class="align-middle text-center text-primary kt-font-bold">
                                            {$number_category_product}
                                        </td>

                                        <td class="align-middle">
                                            <span nh-show-config type="categories_product" class="btn btn-sm btn-secondary w-100">
                                                <i class="fa fa-cog fs-14"></i>
                                                {__d('admin', 'cau_hinh')}
                                            </span>
                                        </td>
                                    </tr>

                                    <tr>
                                        <th scope="row">
                                            <label class="kt-checkbox kt-checkbox--tick kt-checkbox--danger ml-10 mb-1">
                                                <input name="products[check]" class="check-single" type="checkbox" {if $number_product > 0}checked{/if}>
                                                <span></span>
                                            </label>
                                        </th>

                                        <td>
                                            {__d('admin', 'san_pham')}
                                            <span class="form-text text-muted">
                                                {__d('admin', 'export_du_lieu_san_pham')}
                                            </span>
                                        </td>

                                        <td class="align-middle text-center text-primary kt-font-bold">
                                            {$number_product}
                                        </td>

                                        <td class="align-middle">
                                            <div class="form-group mb-0">
                                                <input class="form-control form-control-sm number-input text-left text-success kt-font-bold" nh-record-export nh-record-max="{$number_product}" type="text" value="{if !empty($migrate.config_data.status) && $migrate.config_data.status == "{SUCCESS}"}{$products}{else}{$number_product}{/if}" name="products[record]">
                                            </div>
                                        </td>   
                                    </tr>

                                    <tr>
                                        <th scope="row">
                                            <label class="kt-checkbox kt-checkbox--tick kt-checkbox--danger ml-10 mb-1">
                                                <input name="attributes[product_check]" class="check-single" type="checkbox" {if $number_attribute_product > 0}checked{/if}>
                                                <span></span>
                                            </label>
                                        </th>

                                        <td>
                                            {__d('admin', 'thuoc_tinh_mo_rong_san_pham')}
                                            <span class="form-text text-muted">
                                                {__d('admin', 'export_du_lieu_thuoc_tinh_mo_rong_cua_san_pham')}
                                            </span>
                                        </td>

                                        <td class="align-middle text-center text-primary kt-font-bold">
                                            {$number_attribute_product}
                                        </td>

                                        <td class="align-middle">
                                            <span nh-show-config type="attributes_product" class="btn btn-sm btn-secondary w-100">
                                                <i class="fa fa-cog fs-14"></i>
                                                {__d('admin', 'cau_hinh')}
                                            </span>
                                        </td>  
                                    </tr>

                                    <tr>
                                        <th scope="row">
                                            <label class="kt-checkbox kt-checkbox--tick kt-checkbox--danger ml-10 mb-1">
                                                <input name="attributes[product_check]" class="check-single" type="checkbox" {if $number_attribute_product > 0}checked{/if}>
                                                <span></span>
                                            </label>
                                        </th>

                                        <td>
                                            {__d('admin', 'thuoc_tinh_mo_rong_phien_ban_san_pham')}
                                            <span class="form-text text-muted">
                                                {__d('admin', 'export_du_lieu_thuoc_tinh_mo_rong_cua_phien_ban_san_pham')}
                                            </span>
                                        </td>

                                        <td class="align-middle text-center text-primary kt-font-bold">
                                            {$number_attribute_product_item}
                                        </td>

                                        <td class="align-middle">
                                            <span nh-show-config type="attributes_product_item" class="btn btn-sm btn-secondary w-100">
                                                <i class="fa fa-cog fs-14"></i>
                                                {__d('admin', 'cau_hinh')}
                                            </span>
                                        </td>  
                                    </tr>
                                    
                                    <tr>
                                        <th scope="row">
                                            <label class="kt-checkbox kt-checkbox--tick kt-checkbox--danger ml-10 mb-1">
                                                <input name="brands[check]" class="check-single" type="checkbox" {if $number_brand > 0}checked{/if}>
                                                <span></span>
                                            </label>
                                        </th>

                                        <td>
                                            {__d('admin', 'thuong_hieu')}
                                            <span class="form-text text-muted">
                                                {__d('admin', 'export_du_lieu_thuong_hieu')}
                                            </span>
                                        </td>

                                        <td class="align-middle text-center text-primary kt-font-bold">
                                            {$number_brand}
                                        </td>

                                        <td class="align-middle">
                                            <div class="form-group mb-0">
                                                <input class="form-control form-control-sm number-input text-left text-success kt-font-bold" nh-record-export nh-record-max="{$number_brand}" type="text" value="{if !empty($migrate.config_data.status) && $migrate.config_data.status == "{SUCCESS}"}{$brands}{else}{$number_brand}{/if}" name="brands[record]">
                                            </div>
                                        </td>  
                                    </tr>

                                    <tr>
                                        <th scope="row">
                                            <label class="kt-checkbox kt-checkbox--tick kt-checkbox--danger ml-10 mb-1">
                                                <input name="tags[product_check]" class="check-single" type="checkbox" {if $number_tag_product > 0}checked{/if}>
                                                <span></span>
                                            </label>
                                        </th>
                                        <td>
                                            {__d('admin', 'the_tag_san_pham')}
                                            <span class="form-text text-muted">
                                                {__d('admin', 'export_du_lieu_the_tag_cua_san_pham')}
                                            </span>
                                        </td>
                                        <td class="align-middle text-center text-primary kt-font-bold">
                                            {$number_tag_product}
                                        </td>

                                        <td class="align-middle">
                                            <div class="form-group mb-0">
                                                <input class="form-control form-control-sm number-input text-left text-success kt-font-bold" nh-record-export nh-record-max="{$number_tag_product}" type="text" value="{if !empty($migrate.config_data.status) && $migrate.config_data.status == "{SUCCESS}"}{$tag_product}{else}{$number_tag_product}{/if}" name="tags[product_record]">
                                            </div>
                                        </td>    
                                    </tr>
                                </tbody>
                            </table>
                        </form>
                    </div>

                    <div class="col-sm-5">
                        <div class="config-data-extend border h-100 d-none" nh-list-config>
                            <div class="listbox-title d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    <label class="kt-checkbox kt-checkbox--tick kt-checkbox--success mb-0" style="height: 18px;">
                                        <input type="checkbox" name="check_all" value="" nh-check-all-config>
                                        <span class="bg-white"></span>
                                    </label>
                                    <span class="title kt-font-bold">
                                        {__d('admin', 'cau_hinh')}
                                    </span>
                                </div>

                                <span class="btn btn-sm btn-primary" nh-save-config>
                                    {__d('admin', 'luu_cau_hinh')}
                                </span>
                            </div>

                            <div class="p-20">
                                <div class="kt-scroll listbox-content" data-scroll="true" data-height="606"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">
                    {__d('admin', 'dong')}
                </button>
                
                <button id="btn-config-data" type="button" class="btn btn-sm btn-primary">
                    {__d('admin', 'cap_nhat')}
                </button>
            </div>
        </div>
    </div>
</div>