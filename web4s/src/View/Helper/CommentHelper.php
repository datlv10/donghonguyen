<?php
declare(strict_types=1);

namespace App\View\Helper;
use Cake\View\Helper;
use Cake\ORM\TableRegistry;

class CommentHelper extends Helper
{
    /** Lấy thông tin đánh giá
     * 
     *
     * {assign var = data value = $this->Comment->getRatingInfo()}
     * 
    */
    public function getRatingInfo()
    {
    	if(!defined('PAGE_RECORD_ID') || !defined('PAGE_TYPE')) return [];

    	$rating_info = TableRegistry::get('Comments')->getInfoRating([
    		'foreign_id' => PAGE_RECORD_ID,
    		'type' => PAGE_TYPE
    	]);

    	$number_rating = !empty($rating_info['number_rating']) ? intval($rating_info['number_rating']) : null;
    	$one_star_percent = $two_star_percent = $three_star_percent = $four_star_percent = $five_star_percent = 0;
    	$one_star = !empty($rating_info['one_star']) ? intval($rating_info['one_star']) : null;    	
    	if(!empty($one_star) && !empty($number_rating)){
    		$one_star_percent = round($one_star/$number_rating * 100);
    	}

    	$two_star = !empty($rating_info['two_star']) ? intval($rating_info['two_star']) : null;
    	if(!empty($two_star) && !empty($number_rating)){
    		$two_star_percent = round($two_star/$number_rating * 100);
    	}

    	$three_star = !empty($rating_info['three_star']) ? intval($rating_info['three_star']) : null;
    	if(!empty($three_star) && !empty($number_rating)){
    		$three_star_percent = round($three_star/$number_rating * 100);
    	}

    	$four_star = !empty($rating_info['four_star']) ? intval($rating_info['four_star']) : null;
    	if(!empty($four_star) && !empty($number_rating)){
    		$four_star_percent = round($four_star/$number_rating * 100);
    	}

    	$five_star = !empty($rating_info['five_star']) ? intval($rating_info['five_star']) : null;
    	if(!empty($five_star) && !empty($number_rating)){
    		$five_star_percent = round($five_star/$number_rating * 100);
    	}

    	$result = [
            'avg_rating' => !empty($rating_info['avg_rating']) ? round(floatval($rating_info['avg_rating']), 1) : null,
            'number_rating' => $number_rating,
            'one_star' => $one_star,
            'two_star' => $two_star,
            'three_star' => $three_star,
            'four_star' => $four_star,
            'five_star' => $five_star,
            'one_star_percent' => $one_star_percent,
            'two_star_percent' => $two_star_percent,
            'three_star_percent' => $three_star_percent,
            'four_star_percent' => $four_star_percent,
            'five_star_percent' => $five_star_percent
        ];
	    
	    return $result;
    }
}
