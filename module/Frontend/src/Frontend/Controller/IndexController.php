<?php

namespace Frontend\Controller;

use My\Controller\MyController;

class IndexController extends MyController {
    /* @var $serviceCategory \My\Models\Category */
    /* @var $serviceProduct \My\Models\Product */

    public function __construct() {
//        $this->externalCSS = [
//            STATIC_URL . '/f/v1/css/??flexslider.css',
//        ];
//        $this->externalJS = [
//            STATIC_URL . '/f/v1/js/library/??jquery.flexslider.js',
//            STATIC_URL . '/f/v1/js/library/??jquery.flexslider.init.js',
//            STATIC_URL . '/f/v1/js/library/??jquery.bxslider.js',
//            STATIC_URL . '/f/v1/js/library/??jquery.bxslider.init.js'
//        ];
    }

    public function indexAction() {
        return;
        $params = $this->params()->fromRoute();

        $arrCategoryParentList = unserialize(ARR_CATEGORY_PARENT);
        $arrCategoryByParent = unserialize(ARR_CATEGORY_BY_PARENT);
        $arrCategoryFormat = unserialize(ARR_CATEGORY);

        $arr_new_by_cate = [];
        $instanceSearchContent = new \My\Search\Content();

        $arr_new_by_cate = [];
        foreach ($arrCategoryParentList as $cate_parent) {
            if (empty($arrCategoryByParent[$cate_parent['cate_id']])) {
                $arr_new_by_cate[$cate_parent['cate_id']] = $instanceSearchContent->getListLimit(['cate_id' => $cate_parent['cate_id'], 'cont_status' => 1], 1, 3, ['updated_date' => ['order' => 'desc']]);
            } else {
                $arr_id_child = [];
                foreach ($arrCategoryByParent[$cate_parent['cate_id']] as $cate_child) {
                    $arr_id_child [] = $cate_child['cate_id'];
                }
                $arr_new_by_cate[$cate_parent['cate_id']] = $instanceSearchContent->getListLimit(['in_cate_id' => $arr_id_child, 'cont_status' => 1], 1, 3, ['updated_date' => ['order' => 'desc']]);
            }
        }

        //moi
        $arr_new_list = unserialize(ARR_NEWS_LIST);

        //top 4 in week
        $arr_top_4_week = $instanceSearchContent->getListLimit(['cont_status' => 1, 'more_created_date' => time() - (60 * 60 * 24 * 7)], 1, 4, ['cont_views' => ['order' => 'desc']]);

        //top 15 in Month
//        $arr_top_15_month = $instanceSearchContent->getListLimit(['cont_status' => 1, 'more_created_date' => time() - (60 * 60 * 24 * 30)], 1, 15, ['cont_views' => ['order' => 'desc']]);

        return [
            'param' => $params,
            'arrCategoryParentList' => $arrCategoryParentList,
            'arrCategoryByParent' => $arrCategoryByParent,
            'arrCategoryFormat' => $arrCategoryFormat,
            'arr_new_by_cate' => $arr_new_by_cate,
//            'arr_new_top' => $arr_new_top,
            'arr_top_4_week' => $arr_top_4_week,
//            'arr_top_15_month' => $arr_top_15_month,
            'arr_new_list' => $arr_new_list
        ];
    }

}
