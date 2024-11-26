{if $load_form}
    <div id="editor-template" class="nh-editor"></div>
    <input name="template_content" value="{if !empty($file_content)}{htmlentities($file_content)}{/if}" type="hidden">
{/if}