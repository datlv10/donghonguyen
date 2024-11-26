{assign var = plugins value = $this->Setting->getListPlugins()}

{$this->element('../Member/login_modal')}
{$this->element('../block/comment/info_modal')}
{$this->element('toasts')}

{if !empty($plugins.product)}
	{$this->element('../Product/quick_view_modal')}
	{$this->element('../Product/compare_modal')}
	{$this->element('../Cart/sidebar_cart')}
{/if}

{if !empty($plugins.notification)}
	{$this->element('../Notification/sidebar')}
{/if}