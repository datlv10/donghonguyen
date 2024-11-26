{$this->element('../Article/element_subheader')}

<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
    <form id="main-form" action="{ADMIN_PATH}/article/save{if !empty($id)}/{$id}{/if}" method="POST" autocomplete="off">
        <div class="d-none">
            <input type="hidden" name="draft" value="">
            <input type="hidden" name="seo_score" value="" id="seo-score">
            <input type="hidden" name="keyword_score" value="" id="keyword-score">
        </div>

        {$this->element('../Article/element_basic_info')}

        {$this->element('../Article/element_description')}

        <div id="attributes-article">
            {$this->element('../Article/element_attributes')}
        </div>

        {$this->element('../Article/element_another_info')}

        {$this->element('../Article/element_seo')}
    </form>
</div>