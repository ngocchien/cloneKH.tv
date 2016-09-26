<?php

namespace Frontend\Controller;

use My\Controller\MyController,
    My\General,
    My\Validator\Validate,
    Zend\Validator\File\Size,
    Zend\View\Model\ViewModel,
    Zend\Session\Container;

class SearchController extends MyController {
    /* @var $serviceCategory \My\Models\Category */
    /* @var $serviceProduct \My\Models\Product */
    /* @var $serviceProperties \My\Models\Properties */
    /* @var $serviceDistrict \My\Models\District */
    /* @var $serviceComment \My\Models\Comment */

    public function __construct() {
//        $this->externalJS = [
//            STATIC_URL . '/f/v1/js/my/??search.js'
//        ];
    }

    public function indexAction() {
        $params =  array_merge($this->params()->fromRoute(), $this->params()->fromQuery());
        if(empty($params['keySlug']) && empty($params['keyword'])){
            return $this->redirect()->toRoute('404');
        }

        $intPage = (int) $params['page'] > 0 ? (int) $params['page'] : 1;
        $intLimit = 16;
        $arr_condition_content = [
            'not_cont_status' => -1
        ];

        $instanceSearchKeyword = new \My\Search\Keyword();
        $arr_keyword = [];
        if(!empty($params['keySlug'])){
            $arr_keyword = $instanceSearchKeyword->getDetail(['key_slug'=>General::clean($params['keySlug'])]);
        }

        if(!empty($arr_keyword)){
            $arr_condition_content['full_text_title'] = $arr_keyword['key_name'];
        }else{
            $arr_condition_content['full_text_title'] = General::clean($params['keyword']);
        }
        $instaceSearchContent = new \My\Search\Content();
        $arrContentList = $instaceSearchContent->getListLimit($arr_condition_content, $intPage, $intLimit, ['_score' => ['order' => 'desc']]);

        //phân trang
        $intTotal = $instaceSearchContent->getTotal($arr_condition_content);
        $helper = $this->serviceLocator->get('viewhelpermanager')->get('Paging');
        $paging = $helper($params['module'], $params['__CONTROLLER__'], $params['action'], $intTotal, $intPage, $intLimit, 'search', $params);

        $description = 'Tìm kiếm -- ';
        if(!empty($arr_keyword)){
            $description . $arr_keyword['key_name'];
        }else{
            $description . $params['keyword'];
        }

        $this->renderer = $this->serviceLocator->get('Zend\View\Renderer\PhpRenderer');
        $this->renderer->headMeta()->appendName('dc.description', $description);
        $this->renderer->headTitle($description . General::TITLE_META);
        $this->renderer->headMeta()->appendName('keywords', General::KEYWORD_DEFAULT . $description);
        $this->renderer->headMeta()->appendName('description', $description);
        $this->renderer->headMeta()->setProperty('og:url', $this->url()->fromRoute('search', ['keySlug' => $params['keySlug'],'page'=>$params['page']]));
        $this->renderer->headMeta()->setProperty('og:title', $description);
        $this->renderer->headMeta()->setProperty('og:description', $description);

        //get 50 keyword có id lớn hơn
        $arr_keyword_list = [];
        if(!empty($arr_keyword)){
            $arr_keyword_less =  $instanceSearchKeyword->getListLimit(['key_id_less' => $arr_keyword['key_id']], 1, 50, ['key_id' => ['order' => 'desc']]);
            $arr_keyword_greater =  $instanceSearchKeyword->getListLimit(['key_id_greater' => $arr_keyword['key_id']], 1, 50, ['key_id' => ['order' => 'desc']]);
            $arr_keyword_list = array_merge($arr_keyword_less,$arr_keyword_greater);
        }else{
            $arr_keyword_list = $instanceSearchKeyword->getListLimit(['full_text_keyname' => General::clean($params['keyword'])], 1, 100, ['_score' => ['order' => 'desc']]);
        }

        return [
            'paging' => $paging,
            'params' => $params,
            'arrContentList' => $arrContentList,
            'arr_keyword_list' => $arr_keyword_list,
            'intTotal' => $intTotal
        ];
    }

}
