{assign var = url_list value = "{ADMIN_PATH}/role"}
{assign var = url_add value = "{ADMIN_PATH}/role/add"}
{assign var = url_edit value = "{ADMIN_PATH}/role/update"}

<!--begin:: Content Head -->
{$this->element('Admin.page/content_head', [
    'url_list' => $url_list,
    'url_add' => $url_add,
    'url_edit' => $url_edit
])}
<!-- end:: Content Head -->
<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
    <form id="main-form" action="{ADMIN_PATH}/role/save{if !empty($id)}/{$id}{/if}" method="POST" autocomplete="off">
        <div class="row">
            <div class="col-md-12">
                <div class="kt-portlet">
                    <div class="kt-portlet__head">
                        <div class="kt-portlet__head-label">
                            <h3 class="kt-portlet__head-title">                        
                                {__d('admin', 'thong_tin_nhom_quyen')}
                            </h3>
                        </div>
                    </div>
                    <div class="kt-portlet__body">
                        <div class="form-group row">
                            <label class="col-lg-2 col-xl-2 col-form-label">
                                {__d('admin', 'nhom_quyen')}
                                <span class="kt-font-danger">*</span>
                            </label>
                            <div class="col-lg-10 col-xl-8">
                                <input name="name" value="{if !empty($role.name)}{$role.name}{/if}" class="form-control form-control-sm" type="text">
                            </div>
                        </div>   
                        <div class="form-group row">
                            <label class="col-lg-2 col-xl-2 col-form-label">
                                {__d('admin', 'mo_ta_ngan')}
                            </label>
                            <div class="col-lg-10 col-xl-8">
                                <input name="short_description" value="{if !empty($role.short_description)}{$role.short_description}{/if}" class="form-control form-control-sm" type="text">
                            </div>
                        </div>           
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>


<!-- end:: Content -->