<?php

namespace Frontend\Controller;

use My\Controller\MyController;
use My\General;

class IndexController extends MyController
{
    /* @var $serviceCategory \My\Models\Category */
    /* @var $serviceProduct \My\Models\Product */

    public function __construct()
    {
    }

    public function indexAction()
    {
        try {
            $params = $this->params()->fromRoute();
            $params = array_merge($params, $this->params()->fromQuery());
            $intPage = (int)$params['page'] > 0 ? (int)$params['page'] : 1;
            $intLimit = 20;

            $instanceSearchContent = new \My\Search\Content();
            $arrContentList = $instanceSearchContent->getListLimit(['cont_status' => 1], $intPage, $intLimit, ['created_date' => ['order' => 'desc']]);
            return [
                'arrContentList' => $arrContentList,
                'intPage' => $intPage,
            ];
        } catch (\Exception $exc) {
            return $this->redirect()->toRoute('404', array());
            echo '<pre>';
            print_r([
                'code' => $exc->getCode(),
                'messages' => $exc->getMessage()
            ]);
            echo '</pre>';
            die();
        }
    }

}
