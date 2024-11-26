<?php
declare(strict_types=1);

namespace App\View\Helper;

use Cake\View\Helper;
use Cake\ORM\TableRegistry;

class PromotionHelper extends Helper
{

    /** Danh sách Promotions
     * 
     * {assign var = data value = $this->Promotion->getPublicPromotions([
     *      {LANG} => LANGUAGE
     * ])}
     * 
    */
    public function getPublicPromotions($options = []) 
    {
        $lang = !empty($options[LANG]) ? $options[LANG] : TableRegistry::get('Languages')->getDefaultLanguage();
        $params = [            
            FIELD => FULL_INFO,
            LANG => $lang,            
            FILTER => [
                STATUS => 1,
                'public' => 1
            ],
            'check_expiry_date' => 1
        ];

        $result = [];
        $promotions = TableRegistry::get('Promotions')->queryListPromotions($params)->toArray();
        if(!empty($promotions)){
            foreach($promotions as $k => $promotion){
                $result[] = TableRegistry::get('Promotions')->formatDataPromotionDetail($promotion, $lang);
            }
        }
        
        return $result;
    }
}
