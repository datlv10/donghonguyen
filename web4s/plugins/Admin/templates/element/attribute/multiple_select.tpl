{assign var = class_required value = ''}
{if !empty($required)}
	{assign var = class_required value = 'required'}
{/if}

{$this->Form->select($code, $options, ['id' => $code, 'empty' => null, 'default' => $value, multiple => 'multiple', 'input-attribute' => 'multiple-select', 'message-required' => "{if !empty($required)}{__d('admin', 'vui_long_nhap_thong_tin')}{/if}", 'class' => "form-control form-control-sm {$class_required} {$class}"])}