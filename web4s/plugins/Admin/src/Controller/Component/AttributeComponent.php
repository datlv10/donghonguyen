<?php

namespace Admin\Controller\Component;

use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;
use Cake\Core\Exception\Exception;
use Cake\I18n\Date;
use Cake\I18n\Time;

class AttributeComponent extends Component
{
	public $controller = null;
    public $components = ['System', 'Utilities'];

    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->controller = $this->_registry->getController();
    }
  	

    public function formatDataAttributesBeforeSave($data = [], $lang = null, $type = null, $record_id = null)
    {
    	if(empty($data) || empty($type) || empty($lang)) return [];
    	if(!in_array($type, [ARTICLE, PRODUCT, PRODUCT_ITEM, CATEGORY])) return [];

    	$all_attributes = Hash::combine(TableRegistry::get('Attributes')->getAll($lang), '{n}.id', '{n}', '{n}.attribute_type');
        $all_attributes = !empty($all_attributes[$type]) ? $all_attributes[$type] : [];

        if(empty($all_attributes)) return [];

        $result = [];

        foreach ($all_attributes as $attribute_id => $attribute) {            
            $code = !empty($attribute['code']) ? $attribute['code'] : null;
            $input_type = !empty($attribute['input_type']) ? $attribute['input_type'] : null;
            if(empty($input_type)) continue;

            $value = !empty($data[$code]) ? $data[$code] : null;
            switch ($input_type) {
                case NUMERIC:
                    $value = !empty($value) ? floatval(str_replace(',', '', $value)) : 0;
                    break;

                case DATE:
                    if(!$this->Utilities->isDateClient($value)){
                        $value = null;
                    }
                    $value = !empty($value) ? $this->Utilities->stringDateClientToInt($value) : null;
                    break;

                case DATE_TIME:
                    if(!empty($value)){
                        $time = Time::createFromFormat('d/m/Y - H:i', $value, null);
                        $time = $time->format('Y-m-d H:i:s');
                        $value = strtotime($time);
                    }
                    break;

                case SWITCH_INPUT:
                    $value = !empty($value) ? 1 : 0;
                    break;

                case TEXT:
                case RICH_TEXT:
                case IMAGE:
                case IMAGES:
                case FILES:
                    $value = !empty($value) ? trim($value) : null;
                    break;

                case SINGLE_SELECT:
                    $value = !empty($value) ? intval($value) : null;
                    break;

                case MULTIPLE_SELECT:
                case PRODUCT_SELECT:
                case ARTICLE_SELECT:
                case CITY_DISTRICT:
                case CITY_DISTRICT_WARD:
                case VIDEO:
                case ALBUM_IMAGE:
                case ALBUM_VIDEO:
                    $value = !empty($value) ? json_encode($value) : null;
                    break;

                case CITY:
                    $value = !empty($value) ? $value : null;
                    break;

                default:
                    $value = null;
                break;
            }

            if(in_array($input_type, [TEXT, RICH_TEXT])){
                $text_value = [];
                if(!empty($record_id)){
                	switch ($type) {
                		case ARTICLE:
                			$record_attribute = TableRegistry::get('ArticlesAttribute')->find()->where([
		                        'article_id' => $record_id,
		                        'attribute_id' => $attribute_id
		                    ])->first();
                			break;
                		case PRODUCT:
                			$record_attribute = TableRegistry::get('ProductsAttribute')->find()->where([
		                        'product_id' => $record_id,
		                        'attribute_id' => $attribute_id
		                    ])->first();
                			break;
                        case CATEGORY:
                            $record_attribute = TableRegistry::get('CategoriesAttribute')->find()->where([
                                'category_id' => $record_id,
                                'attribute_id' => $attribute_id
                            ])->first();
                            break;
                	}
                    $text_value = !empty($record_attribute['value']) ? json_decode($record_attribute['value'], true) : [];
                }
                
                if(!is_array($text_value)) $text_value = [];
                $text_value[$lang] = $value;
                $value = !empty($text_value) ? json_encode($text_value) : null;
            }

            $result[] = [
                'attribute_id' => $attribute_id,
                'value' => $value
            ];
        }


        return $result;
    }

    public function getListOptionsByAttributeId($attribute_id = null)
    {
        if (empty($attribute_id)) return [];

        $table = TableRegistry::get('AttributesOptions');
        $params[FILTER]['attribute_id'] = $attribute_id;

        $options = $table->queryListAttributesOptions($params)->toArray();

        if (empty($options)) return [];

        $result = [];
        foreach ($options as $key => $option) {
            $option_id = !empty($option['id']) ? intval($option['id']) : null;
            $option_name = !empty($option['AttributesOptionsContent']['name']) ? $option['AttributesOptionsContent']['name'] : null;

            if (!empty($option_id) && !empty($option_name)) {
                $result[$option_id] = $option_name;
            }
        }

        return $result;
    }
}
