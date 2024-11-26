{assign var = class_required value = ''}
{if !empty($required)}
	{assign var = class_required value = 'required'}
{/if}

{$this->Form->select($code, $options, ['id' => $code, 'data-type' => 'special', 'empty' => null, 'default' => $value, multiple => 'multiple', 'class' => "form-control form-control-sm kt-select2 {$class_required} {$class}"])}