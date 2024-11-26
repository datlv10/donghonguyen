<?php
declare(strict_types=1);

namespace App\View\Helper;
use Cake\View\Helper;
use PHPHtmlParser\Dom;

class LazyLoadHelper extends Helper
{
    /** Lazy load image
     * 
     * $params['src']: Đường dẫn ảnh
     * $params['alt']: Thông tin alt
     * $params['class']: Nhập class
     * $params['ignore']: Cho phép áp dụng lazy load - ví dụ: true | false
     * 
     * {$this->LazyLoad->renderImage([
     *      'src' => 'đường dẫn ảnh', 
     *      'alt' => 'tên ảnh', 
     *      'class' => 'img-fluid'
     * ])}
     * 
    */
    public function renderImage($params = [])
    {
        $src = !empty($params['src']) ? $params['src'] : '';
        $alt = !empty($params['alt']) ? $params['alt'] : null;
        $class = !empty($params['class']) ? $params['class'] : null;
        $ignore = !empty($params['ignore']) ? true : false;
        
        $str_delay = '';
        if(!empty($params['delay'])){
            $delay = in_array($params['delay'], ['mobile', 'desktop', 'all']) ? $params['delay'] : 'all';
            $str_delay = 'delay="'. $delay .'"';
        }

        if($ignore){
            $result = '<img class="' . $class . '" src="' . $src . '" alt="' . $alt . '">';            
        }else{
            $result = '<img nh-lazy="image" '. $str_delay .' class="' . $class . '" data-src="' . $src . '" alt="' . $alt . '" src="data:image/gif;base64,R0lGODlhAQABAIAAAP///wAAACH5BAEAAAAALAAAAAABAAEAAAICRAEAOw==">';
        }

        return $result;
    }

    private function strReplaceFirst($find_string = null, $replace = null, $content = null)
    {
        $find_string = '/' . preg_quote($find_string, '/') . '/';

        return preg_replace($find_string, $replace, $content, 1);
    }

    /** Lazy load ảnh, video, iframe trong nội dung
     * 
     * $content: Nội dung văn bản
     * 
     * {$this->LazyLoad->renderContent($content)}
     * 
    */
    public function renderContent($content = null, $type = null, $record_id = null)
    {
        if(empty($content)) return $content;
        if(empty($type) && defined('PAGE_TYPE')){
            switch(PAGE_TYPE){
                case ARTICLE_DETAIL:
                    $type = ARTICLE;
                break;

                case PRODUCT_DETAIL:
                    $type = PRODUCT;
                break;
            }
        }
        
        if(empty($record_id) && defined('PAGE_RECORD_ID')) $record_id = PAGE_RECORD_ID;

        // replace tag embed attribute
        $content = str_replace('[--embed-start--][', '<div nh-page-type="'. $type .'" nh-record-id="'. $record_id .'" ', $content);
        $content = str_replace('][--embed-end--]', '></div>', $content);

        if(strpos($content, '<img') == false && strpos($content, '<iframe') == false && strpos($content, '<video') == false) return $content;     

        $dom = new Dom;
        $dom->loadStr($content);

        $images = $dom->find('img');
        foreach ($images as $image) {
            $src = $image->getAttribute('src');
            $image->setAttribute('nh-lazy', 'image');
            $image->setAttribute('data-src', $src);
            $image->removeAttribute('src');
        }


        $iframes = $dom->find('iframe');
        foreach ($iframes as $iframe) {
            $src = $iframe->getAttribute('src');
            $iframe->setAttribute('nh-lazy', 'iframe');
            $iframe->setAttribute('data-src', $src);
            $iframe->removeAttribute('src');
        }


        $videos = $dom->find('video');
        foreach ($videos as $video) {
            $source = $video->find('source')[0];
            if(empty($source)) continue;
            $src = $source->getAttribute('src');
            $type = $source->getAttribute('type');
            $source->delete();
            unset($source);
            $video->setAttribute('data-src', $src.'|'.$type);
            $video->setAttribute('nh-lazy', 'video');
        }

        return $dom->outerHtml;
    }
}
