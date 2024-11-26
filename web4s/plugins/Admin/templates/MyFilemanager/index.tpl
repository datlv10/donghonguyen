<!DOCTYPE html>
<html>
<head>
    <title>
        FileManager - Web4s
    </title>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"/>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">

    <link href="{ADMIN_PATH}/favicon.ico" rel="icon" type="image/x-icon"/>
    
    <link href="{ADMIN_PATH}/myfilemanager/assests/lib/jquery-modal/jquery.modal.min.css" rel="stylesheet" type="text/css" />
    <link href="{ADMIN_PATH}/myfilemanager/assests/lib/jquery-toast/jquery.toast.min.css" rel="stylesheet" type="text/css" />
    <link href="{ADMIN_PATH}/myfilemanager/assests/lib/context-menu/context-menu.min.css" rel="stylesheet" type="text/css" />
    <link href="{ADMIN_PATH}/myfilemanager/assests/lib/flatpickr/flatpickr.min.css" rel="stylesheet" type="text/css" />

    <link href="{ADMIN_PATH}/myfilemanager/assests/css/main.css?v={ADMIN_VERSION_UPDATE}" rel="stylesheet" type="text/css" /> 
    <link href="{ADMIN_PATH}/myfilemanager/assests/css/main_mb.css?v={ADMIN_VERSION_UPDATE}" rel="stylesheet" type="text/css" />    
</head>
<body>

    <div id="nh-filemanager" class="filemanager">
		{$this->element('../MyFilemanager/navbar', [])}

		<div class="main">
			<div class="main-container">
				<div class="navigation-container">
					{$this->element('../MyFilemanager/navigation', [])}
				</div>

				<div class="files-container">
					<div class="info-files">
						{$this->element('../MyFilemanager/header_container', [])}
						
						<div class="files-wrap">
							<div nh-wrap="list-files" data-page="0" data-loading="0" data-next="0" class="list-files list-view-0"></div>
						</div>
					</div>
				</div>

				<div class="detail-container">
					<div nh-wrap="info" class="detail-info">
						
					</div>
				</div>
			</div>
		</div>	
	</div>

	{assign var = accept value = '*'}
	{if !empty($extensions)}
		{$accept = ','|implode:$extensions}
	{/if}

	<input nh-input="select-upload-file" type="file" multiple="true" accept="{$accept}" data-max-file-size="{if !empty($max_file_size)}{$max_file_size}{/if}" style="display: none;">

	{$this->element('../MyFilemanager/modal_instruct', [])}


    <script type="text/javascript">
        var csrfToken = '{$this->getRequest()->getAttribute('csrfToken')}';
        
        var prefixCdnUrl = "{ADMIN_PATH}/myfilemanager";
        var fileManagerToken = "{if !empty($token)}{$token}{/if}";
        var cdnCrossDomain = "{if !empty($cross_domain)}{$cross_domain}{else}0{/if}";
        var cdnFieldId = "{if !empty($field_id)}{$field_id}{/if}";
        var cdnMultiple = "{if !empty($multiple)}1{else}0{/if}";
        var cdnTypeFile = "{if !empty($type_file)}{$type_file}{/if}";
        var prefixUrlFile = "/templates/{CODE_TEMPLATE}/assets";
        var locales = {};
    </script>

    <script src="{ADMIN_PATH}/myfilemanager/assests/lib/jquery/jquery-3.6.0.min.js" type="text/javascript"></script>
    <script src="{ADMIN_PATH}/myfilemanager/assests/lib/jquery-lazy/jquery.lazy.min.js" type="text/javascript"></script>
    <script src="{ADMIN_PATH}/myfilemanager/assests/lib/jquery-lazy/jquery.lazy.plugins.min.js" type="text/javascript"></script>
    
    <script src="{ADMIN_PATH}/myfilemanager/assests/lib/jquery-modal/jquery.modal.min.js" type="text/javascript"></script>
    <script src="{ADMIN_PATH}/myfilemanager/assests/lib/jquery-toast/jquery.toast.min.js" type="text/javascript"></script>
    <script src="{ADMIN_PATH}/myfilemanager/assests/lib/context-menu/context-menu.min.js" type="text/javascript"></script>
    <script src="{ADMIN_PATH}/myfilemanager/assests/lib/flatpickr/flatpickr.min.js" type="text/javascript"></script>

    {if !empty($lang) && in_array($lang, ['vi', 'ja', 'ko', 'zh'])}
        <script src="{ADMIN_PATH}/myfilemanager/assests/lib/flatpickr/l10n/{LANGUAGE_ADMIN}.js" type="text/javascript"></script>
    {/if}
    
    <script src="{ADMIN_PATH}/myfilemanager/assests/js/constants.js?v={ADMIN_VERSION_UPDATE}" type="text/javascript"></script>
    <script src="{ADMIN_PATH}/myfilemanager/assests/js/locales/{LANGUAGE_ADMIN}.js?v={ADMIN_VERSION_UPDATE}" type="text/javascript"></script>
    <script src="{ADMIN_PATH}/myfilemanager/assests/js/main.js?v={ADMIN_VERSION_UPDATE}" type="text/javascript"></script>
    <script src="{ADMIN_PATH}/myfilemanager/assests/js/filemanager.js?v={ADMIN_VERSION_UPDATE}" type="text/javascript"></script>

</body>
</html>
