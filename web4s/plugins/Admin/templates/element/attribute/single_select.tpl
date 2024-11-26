{assign var = select_empty value = null}
{assign var = class_required value = ''}
{if !empty($required)}
	{assign var = class_required value = 'required'}
{else}
	{assign var = select_empty value = {__d('admin', 'chon')}}	
{/if}

{$this->Form->select($code, $options, ['id' => $code, 'empty' => {$select_empty}, 'default' => $value, 'input-attribute' => 'single-select', 'class' => "form-control form-control-sm {$class_required} {$class}", 'disabled' => $disabled])}
