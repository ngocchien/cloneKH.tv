<?php

namespace Backend\Controller;

use My\General,
    My\Controller\MyController,
    Zend\View\Model\ViewModel;

class ConsoleController extends MyController
{

    protected static $_arr_worker = [
        'content', 'logs', 'mail', 'category', 'user', 'general', 'keyword', 'group', 'permission'
    ];

    public function __construct()
    {
        if (PHP_SAPI !== 'cli') {
            die('Only use this controller from command line!');
        }
        ini_set('default_socket_timeout', -1);
        ini_set('max_execution_time', -1);
        ini_set('mysql.connect_timeout', -1);
        ini_set('memory_limit', -1);
        ini_set('output_buffering', 0);
        ini_set('zlib.output_compression', 0);
        ini_set('implicit_flush', 1);
    }

    public function indexAction()
    {
        die();
    }

    private function flush()
    {
        ob_end_flush();
        ob_flush();
        flush();
    }

    public function initEsAction()
    {

    }

    public function migrateAction()
    {
        $params = $this->request->getParams();
        $intIsCreateIndex = (int)$params['createindex'];

        if (empty($params['type'])) {
            return General::getColoredString("Unknown type \n", 'light_cyan', 'red');
        }

        switch ($params['type']) {
            case 'logs':
                $this->__migrateLogs($intIsCreateIndex);
                break;

            case 'content':
                $this->__migrateContent($intIsCreateIndex);
                break;

            case 'category' :
                $this->__migrateCategory($intIsCreateIndex);
                break;

            case 'user' :
                $this->__migrateUser($intIsCreateIndex);
                break;

            case 'general' :
                $this->__migrateGeneral($intIsCreateIndex);
                break;
            case 'keyword' :
                $this->__migrateKeyword($intIsCreateIndex);
                break;
            case 'group' :
                $this->__migrateGroup($intIsCreateIndex);
                break;
            case 'permission' :
                $this->__migratePermission($intIsCreateIndex);
                break;
            case 'all-table' :
                $instanceSearch = new \My\Search\Logs();
                $instanceSearch->createIndex();
                $instanceSearch = new \My\Search\Content();
                $instanceSearch->createIndex();
                $instanceSearch = new \My\Search\Category();
                $instanceSearch->createIndex();
                $instanceSearch = new \My\Search\User();
                $instanceSearch->createIndex();
                $instanceSearch = new \My\Search\Keyword();
                $instanceSearch->createIndex();
                $instanceSearch = new \My\Search\GeneralSearch();
                $instanceSearch->createIndex();
                $instanceSearch = new \My\Search\Group();
                $instanceSearch->createIndex();
                $instanceSearch = new \My\Search\Permission();
                $instanceSearch->createIndex();
                break;
        }
        echo General::getColoredString("Index ES sucess", 'light_cyan', 'yellow');
        return true;
    }

    public function __migratePermission($intIsCreateIndex)
    {
        $service = $this->serviceLocator->get('My\Models\Permission');
        $intLimit = 1000;
        $instanceSearch = new \My\Search\Permission();

        for ($intPage = 1; $intPage < 10000; $intPage++) {
            $arrList = $service->getListLimit([], $intPage, $intLimit, 'perm_id ASC');

            if (empty($arrList)) {
                break;
            }

            if ($intPage == 1) {
                if ($intIsCreateIndex) {
                    $instanceSearch->createIndex();
                } else {
                    $result = $instanceSearch->removeAllDoc();
                    if (empty($result)) {
                        $this->flush();
                        return General::getColoredString("Cannot delete old search index \n", 'light_cyan', 'red');
                    }
                }
            }
            $arrDocument = [];
            foreach ($arrList as $arr) {
                $id = (int)$arr['perm_id'];

                $arrDocument[] = new \Elastica\Document($id, $arr);
                echo General::getColoredString("Created new document with id = " . $id . " Successfully", 'cyan');

                $this->flush();
            }

            unset($arrList); //release memory
            echo General::getColoredString("Migrating " . count($arrDocument) . " documents, please wait...", 'yellow');
            $this->flush();

            $instanceSearch->add($arrDocument);
            echo General::getColoredString("Migrated " . count($arrDocument) . " documents successfully", 'blue', 'cyan');

            unset($arrDocument);
            $this->flush();
        }

        die('done');
    }

    public function __migrateGroup($intIsCreateIndex)
    {
        $service = $this->serviceLocator->get('My\Models\Group');
        $intLimit = 1000;
        $instanceSearch = new \My\Search\Group();

        for ($intPage = 1; $intPage < 10000; $intPage++) {
            $arrList = $service->getListLimit([], $intPage, $intLimit, 'group_id ASC');

            if (empty($arrList)) {
                break;
            }

            if ($intPage == 1) {
                if ($intIsCreateIndex) {
                    $instanceSearch->createIndex();
                } else {
                    $result = $instanceSearch->removeAllDoc();
                    if (empty($result)) {
                        $this->flush();
                        return General::getColoredString("Cannot delete old search index \n", 'light_cyan', 'red');
                    }
                }
            }
            $arrDocument = [];
            foreach ($arrList as $arr) {
                $id = (int)$arr['group_id'];

                $arrDocument[] = new \Elastica\Document($id, $arr);
                echo General::getColoredString("Created new document with id = " . $id . " Successfully", 'cyan');

                $this->flush();
            }

            unset($arrList); //release memory
            echo General::getColoredString("Migrating " . count($arrDocument) . " documents, please wait...", 'yellow');
            $this->flush();

            $instanceSearch->add($arrDocument);
            echo General::getColoredString("Migrated " . count($arrDocument) . " documents successfully", 'blue', 'cyan');

            unset($arrDocument);
            $this->flush();
        }

        die('done');
    }

    public function __migrateGeneral($intIsCreateIndex)
    {
        $service = $this->serviceLocator->get('My\Models\GeneralBqn');
        $intLimit = 1000;
        $instanceSearch = new \My\Search\GeneralBqn();

        for ($intPage = 1; $intPage < 10000; $intPage++) {
            $arrList = $service->getListLimit([], $intPage, $intLimit, 'gene_id ASC');

            if (empty($arrList)) {
                break;
            }

            if ($intPage == 1) {
                if ($intIsCreateIndex) {
                    $instanceSearch->createIndex();
                } else {
                    $result = $instanceSearch->removeAllDoc();
                    if (empty($result)) {
                        $this->flush();
                        return General::getColoredString("Cannot delete old search index \n", 'light_cyan', 'red');
                    }
                }
            }
            $arrDocument = [];
            foreach ($arrList as $arr) {
                $id = (int)$arr['gene_id'];

                $arrDocument[] = new \Elastica\Document($id, $arr);
                echo General::getColoredString("Created new document with id = " . $id . " Successfully", 'cyan');

                $this->flush();
            }

            unset($arrList); //release memory
            echo General::getColoredString("Migrating " . count($arrDocument) . " documents, please wait...", 'yellow');
            $this->flush();

            $instanceSearch->add($arrDocument);
            echo General::getColoredString("Migrated " . count($arrDocument) . " documents successfully", 'blue', 'cyan');

            unset($arrDocument);
            $this->flush();
        }

        die('done');
    }

    public function __migrateUser($intIsCreateIndex)
    {
        $service = $this->serviceLocator->get('My\Models\User');
        $intLimit = 1000;
        $instanceSearch = new \My\Search\User();

        for ($intPage = 1; $intPage < 10000; $intPage++) {
            $arrList = $service->getListLimit([], $intPage, $intLimit, 'user_id ASC');

            if (empty($arrList)) {
                break;
            }

            if ($intPage == 1) {
                if ($intIsCreateIndex) {
                    $instanceSearch->createIndex();
                } else {
                    $result = $instanceSearch->removeAllDoc();
                    if (empty($result)) {
                        $this->flush();
                        return General::getColoredString("Cannot delete old search index \n", 'light_cyan', 'red');
                    }
                }
            }
            $arrDocument = [];
            foreach ($arrList as $arr) {
                $id = (int)$arr['user_id'];

                $arrDocument[] = new \Elastica\Document($id, $arr);
                echo General::getColoredString("Created new document with id = " . $id . " Successfully", 'cyan');

                $this->flush();
            }

            unset($arrList); //release memory
            echo General::getColoredString("Migrating " . count($arrDocument) . " documents, please wait...", 'yellow');
            $this->flush();

            $instanceSearch->add($arrDocument);
            echo General::getColoredString("Migrated " . count($arrDocument) . " documents successfully", 'blue', 'cyan');

            unset($arrDocument);
            $this->flush();
        }

        die('done');
    }

    public function __migrateCategory($intIsCreateIndex)
    {
        $service = $this->serviceLocator->get('My\Models\Category');
        $intLimit = 1000;
        $instanceSearch = new \My\Search\Category();
//        $instanceSearch->createIndex();
//        die();
        for ($intPage = 1; $intPage < 10000; $intPage++) {
            $arrList = $service->getListLimit([], $intPage, $intLimit, 'cate_id ASC');
            if (empty($arrList)) {
                break;
            }

            if ($intPage == 1) {
                if ($intIsCreateIndex) {
                    $instanceSearch->createIndex();
                } else {
                    $result = $instanceSearch->removeAllDoc();
                    if (empty($result)) {
                        $this->flush();
                        return General::getColoredString("Cannot delete old search index \n", 'light_cyan', 'red');
                    }
                }
            }
            $arrDocument = [];
            foreach ($arrList as $arr) {
                $id = (int)$arr['cate_id'];

                $arrDocument[] = new \Elastica\Document($id, $arr);
                echo General::getColoredString("Created new document with id = " . $id . " Successfully", 'cyan');

                $this->flush();
            }

            unset($arrList); //release memory
            echo General::getColoredString("Migrating " . count($arrDocument) . " documents, please wait...", 'yellow');
            $this->flush();

            $instanceSearch->add($arrDocument);
            echo General::getColoredString("Migrated " . count($arrDocument) . " documents successfully", 'blue', 'cyan');

            unset($arrDocument);
            $this->flush();
        }

        die('done');
    }

    public function __migrateLogs($intIsCreateIndex)
    {
        $serviceLogs = $this->serviceLocator->get('My\Models\Logs');
        $intLimit = 1000;
        $instanceSearchLogs = new \My\Search\Logs();
        for ($intPage = 1; $intPage < 10000; $intPage++) {
            $arrLogsList = $serviceLogs->getListLimit([], $intPage, $intLimit, 'log_id ASC');
            if (empty($arrLogsList)) {
                break;
            }

            if ($intPage == 1) {
                if ($intIsCreateIndex) {
                    $instanceSearchLogs->createIndex();
                } else {
                    $result = $instanceSearchLogs->removeAllDoc();
                    if (empty($result)) {
                        $this->flush();
                        return General::getColoredString("Cannot delete old search index \n", 'light_cyan', 'red');
                    }
                }
            }
            $arrDocument = [];
            foreach ($arrLogsList as $arrLogs) {
                $logId = (int)$arrLogs['log_id'];

                $arrDocument[] = new \Elastica\Document($logId, $arrLogs);
                echo General::getColoredString("Created new document with log_id = " . $logId . " Successfully", 'cyan');

                $this->flush();
            }

            unset($arrLogsList); //release memory
            echo General::getColoredString("Migrating " . count($arrDocument) . " documents, please wait...", 'yellow');
            $this->flush();

            $instanceSearchLogs->add($arrDocument);
            echo General::getColoredString("Migrated " . count($arrDocument) . " documents successfully", 'blue', 'cyan');

            unset($arrDocument);
            $this->flush();
        }

        die('done');
    }

    public function __migrateContent($intIsCreateIndex)
    {
        $serviceContent = $this->serviceLocator->get('My\Models\Content');
        $intLimit = 200;
        $instanceSearchContent = new \My\Search\Content();
//        $instanceSearchContent->createIndex();
//        die();

        for ($intPage = 1; $intPage < 10000; $intPage++) {
            $arrContentList = $serviceContent->getListLimit([], $intPage, $intLimit, 'cont_id ASC');
            if (empty($arrContentList)) {
                break;
            }

            if ($intPage == 1) {
                if ($intIsCreateIndex) {
                    $instanceSearchContent->createIndex();
                } else {
                    $result = $instanceSearchContent->removeAllDoc();
                    if (empty($result)) {
                        $this->flush();
                        return General::getColoredString("Cannot delete old search index \n", 'light_cyan', 'red');
                    }
                }
            }
            $arrDocument = [];
            foreach ($arrContentList as $arrContent) {
                $id = (int)$arrContent['cont_id'];

                $arrDocument[] = new \Elastica\Document($id, $arrContent);
                echo General::getColoredString("Created new document with cont_id = " . $id . " Successfully", 'cyan');

                $this->flush();
            }

            unset($arrContentList); //release memory
            echo General::getColoredString("Migrating " . count($arrDocument) . " documents, please wait...", 'yellow');
            $this->flush();

            $instanceSearchContent->add($arrDocument);
            echo General::getColoredString("Migrated " . count($arrDocument) . " documents successfully", 'blue', 'cyan');

            unset($arrDocument);
            $this->flush();
        }

        die('done');
    }

    public function __migrateKeyword($intIsCreateIndex)
    {

        $serviceKeyword = $this->serviceLocator->get('My\Models\Keyword');
        $intLimit = 200;
        $instanceSearchKeyword = new \My\Search\Keyword();
        for ($intPage = 1; $intPage < 10000; $intPage++) {
            $arrList = $serviceKeyword->getListLimit([], $intPage, $intLimit, 'key_id ASC');
            if (empty($arrList)) {
                break;
            }

            if ($intPage == 1) {
                if ($intIsCreateIndex) {
                    $instanceSearchKeyword->createIndex();
                } else {
                    $result = $instanceSearchKeyword->removeAllDoc();
                    if (empty($result)) {
                        $this->flush();
                        return General::getColoredString("Cannot delete old search index \n", 'light_cyan', 'red');
                    }
                }
            }
            $arrDocument = [];
            foreach ($arrList as $arr) {
                $id = (int)$arr['key_id'];
                $arrDocument[] = new \Elastica\Document($id, $arr);
                echo General::getColoredString("Created new document with cont_id = " . $id . " Successfully", 'cyan');

                $this->flush();
            }

            unset($arrList); //release memory
            echo General::getColoredString("Migrating " . count($arrDocument) . " documents, please wait...", 'yellow');
            $this->flush();

            $instanceSearchKeyword->add($arrDocument);
            echo General::getColoredString("Migrated " . count($arrDocument) . " documents successfully", 'blue', 'cyan');

            unset($arrDocument);
            $this->flush();
        }

        die('done');

        $instanceSearchKeyword = new \My\Search\Keyword();
//        $instanceSearchKeyword->createIndex();
//        die();
        $content = file_get_contents(PUBLIC_PATH . '/keyword.txt');
        $content = explode("\n", $content);

        foreach ($content as $keyword) {
            if (empty($keyword)) {
                continue;
            }
            $isexist = $instanceSearchKeyword->getDetail(['key_slug' => General::getSlug($keyword)]);
            if ($isexist) {
                continue;
            }
            $arr_data = [
                'key_name' => $keyword,
                'key_slug' => General::getSlug($keyword)
            ];
            $serviceKeyword = $this->serviceLocator->get('My\Models\Keyword');
            $int_result = $serviceKeyword->add($arr_data);
            unset($serviceKeyword);
            if ($int_result) {
                $arr_data['key_id'] = (int)$int_result;
                $arrDocument[] = new \Elastica\Document($arr_data['key_id'], $arr_data);
                $int_result = $instanceSearchKeyword->add($arrDocument);
                if ($int_result) {
                    echo \My\General::getColoredString("Inset success 1 row with id = {$arr_data['key_id']}, please wait...", 'yellow');
                }
            }
            $this->flush();
        }
    }

    public function workerAction()
    {
        $params = $this->request->getParams();

        //stop all job
        if ($params['stop'] === 'all') {
            if ($params['type'] || $params['background']) {
                return General::getColoredString("Invalid params \n", 'light_cyan', 'red');
            }
            exec("ps -ef | grep -v grep | grep 'type=" . WORKER_PREFIX . "-*' | awk '{ print $2 }'", $PID);

            if (empty($PID)) {
                return General::getColoredString("Cannot found PID \n", 'light_cyan', 'red');
            }

            foreach ($PID as $worker) {
                shell_exec("kill " . $worker);
                echo General::getColoredString("Kill worker with PID = {$worker} stopped running in background \n", 'green');
            }

            return true;
        }

        $arr_worker = self::$_arr_worker;
        if (in_array(trim($params['stop']), $arr_worker)) {
            if ($params['type'] || $params['background']) {
                return General::getColoredString("Invalid params \n", 'light_cyan', 'red');
            }
            $stopWorkerName = WORKER_PREFIX . '-' . trim($params['stop']);
            exec("ps -ef | grep -v grep | grep 'type={$stopWorkerName}' | awk '{ print $2 }'", $PID);
            $PID = current($PID);
            if ($PID) {
                shell_exec("kill " . $PID);
                return General::getColoredString("Job {$stopWorkerName} is stopped running in background \n", 'green');
            } else {
                return General::getColoredString("Cannot found PID \n", 'light_cyan', 'red');
            }
        }

        $worker = General::getWorkerConfig();
        switch ($params['type']) {
            case WORKER_PREFIX . '-logs':
                //start job in background
                if ($params['background'] === 'true') {
                    $PID = shell_exec("nohup php " . PUBLIC_PATH . "/index.php worker --type=" . WORKER_PREFIX . "-logs >/dev/null & echo 2>&1 & echo $!");
                    if (empty($PID)) {
                        echo General::getColoredString("Cannot deamon PHP process to run job " . WORKER_PREFIX . "-logs in background. \n", 'light_cyan', 'red');
                        return;
                    } else {
                        echo General::getColoredString("Job " . WORKER_PREFIX . "-logs is running in background ... \n", 'green');
                    }
                }

                $funcName1 = SEARCH_PREFIX . 'writeLog';
                $methodHandler1 = '\My\Job\JobLog::writeLog';
                $worker->addFunction($funcName1, $methodHandler1, $this->serviceLocator);

                break;

            case WORKER_PREFIX . '-content':
                //start job in background
                if ($params['background'] === 'true') {
                    $PID = shell_exec("nohup php " . PUBLIC_PATH . "/index.php worker --type=" . WORKER_PREFIX . "-content >/dev/null & echo 2>&1 & echo $!");
                    if (empty($PID)) {
                        echo General::getColoredString("Cannot deamon PHP process to run job " . WORKER_PREFIX . "-content in background. \n", 'light_cyan', 'red');
                        return;
                    }
                    echo General::getColoredString("Job " . WORKER_PREFIX . "-content is running in background ... \n", 'green');
                }

                $funcName1 = SEARCH_PREFIX . 'writeContent';
                $methodHandler1 = '\My\Job\JobContent::writeContent';
                $worker->addFunction($funcName1, $methodHandler1, $this->serviceLocator);

                $funcName2 = SEARCH_PREFIX . 'editContent';
                $methodHandler2 = '\My\Job\JobContent::editContent';
                $worker->addFunction($funcName2, $methodHandler2, $this->serviceLocator);

                $funcName3 = SEARCH_PREFIX . 'multiEditContent';
                $methodHandler3 = '\My\Job\JobContent::multiEditContent';
                $worker->addFunction($funcName3, $methodHandler3, $this->serviceLocator);

                break;

            case WORKER_PREFIX . '-mail':
                //start job in background
                if ($params['background'] === 'true') {
                    $PID = shell_exec("nohup php " . PUBLIC_PATH . "/index.php worker --type=" . WORKER_PREFIX . "-mail >/dev/null & echo 2>&1 & echo $!");
                    if (empty($PID)) {
                        echo General::getColoredString("Cannot deamon PHP process to run job " . WORKER_PREFIX . "-mail in background. \n", 'light_cyan', 'red');
                        return;
                    }
                    echo General::getColoredString("Job " . WORKER_PREFIX . "-mail is running in background ... \n", 'green');
                }

                $funcName1 = SEARCH_PREFIX . 'sendMail';
                $methodHandler1 = '\My\Job\JobMail::sendMail';
                $worker->addFunction($funcName1, $methodHandler1, $this->serviceLocator);

                break;

            case WORKER_PREFIX . '-category':
                //start job in background
                if ($params['background'] === 'true') {
                    $PID = shell_exec("nohup php " . PUBLIC_PATH . "/index.php worker --type=" . WORKER_PREFIX . "-category >/dev/null & echo 2>&1 & echo $!");
                    if (empty($PID)) {
                        echo General::getColoredString("Cannot deamon PHP process to run job " . WORKER_PREFIX . "-category in background. \n", 'light_cyan', 'red');
                        return;
                    }
                    echo General::getColoredString("Job " . WORKER_PREFIX . "-category is running in background ... \n", 'green');
                }

                $funcName1 = SEARCH_PREFIX . 'writeCategory';
                $methodHandler1 = '\My\Job\JobCategory::writeCategory';
                $worker->addFunction($funcName1, $methodHandler1, $this->serviceLocator);

                $funcName2 = SEARCH_PREFIX . 'editCategory';
                $methodHandler2 = '\My\Job\JobCategory::editCategory';
                $worker->addFunction($funcName2, $methodHandler2, $this->serviceLocator);

                $funcName3 = SEARCH_PREFIX . 'multiEditCategory';
                $methodHandler3 = '\My\Job\JobCategory::multiEditCategory';
                $worker->addFunction($funcName3, $methodHandler3, $this->serviceLocator);

                break;

            case WORKER_PREFIX . '-user':
                //start job in background
                if ($params['background'] === 'true') {
                    $PID = shell_exec("nohup php " . PUBLIC_PATH . "/index.php worker --type=" . WORKER_PREFIX . "-user >/dev/null & echo 2>&1 & echo $!");
                    if (empty($PID)) {
                        echo General::getColoredString("Cannot deamon PHP process to run job " . WORKER_PREFIX . "-user in background. \n", 'light_cyan', 'red');
                        return;
                    }
                    echo General::getColoredString("Job " . WORKER_PREFIX . "-user is running in background ... \n", 'green');
                }

                $funcName1 = SEARCH_PREFIX . 'writeUser';
                $methodHandler1 = '\My\Job\JobUser::writeUser';
                $worker->addFunction($funcName1, $methodHandler1, $this->serviceLocator);

                $funcName2 = SEARCH_PREFIX . 'editUser';
                $methodHandler2 = '\My\Job\JobUser::editUser';
                $worker->addFunction($funcName2, $methodHandler2, $this->serviceLocator);

                $funcName3 = SEARCH_PREFIX . 'multiEditUser';
                $methodHandler3 = '\My\Job\JobUser::multiEditUser';
                $worker->addFunction($funcName3, $methodHandler3, $this->serviceLocator);

                break;

            case WORKER_PREFIX . '-general':
                //start job in background
                if ($params['background'] === 'true') {
                    $PID = shell_exec("nohup php " . PUBLIC_PATH . "/index.php worker --type=" . WORKER_PREFIX . "-general >/dev/null & echo 2>&1 & echo $!");
                    if (empty($PID)) {
                        echo General::getColoredString("Cannot deamon PHP process to run job " . WORKER_PREFIX . "-general in background. \n", 'light_cyan', 'red');
                        return;
                    }
                    echo General::getColoredString("Job " . WORKER_PREFIX . "-general is running in background ... \n", 'green');
                }

                $funcName1 = SEARCH_PREFIX . 'writeGeneral';
                $methodHandler1 = '\My\Job\JobGeneral::writeGeneral';
                $worker->addFunction($funcName1, $methodHandler1, $this->serviceLocator);

                $funcName2 = SEARCH_PREFIX . 'editGeneral';
                $methodHandler2 = '\My\Job\JobGeneral::editGeneral';
                $worker->addFunction($funcName2, $methodHandler2, $this->serviceLocator);

                break;

            case WORKER_PREFIX . '-keyword':
                //start job in background
                if ($params['background'] === 'true') {
                    $PID = shell_exec("nohup php " . PUBLIC_PATH . "/index.php worker --type=" . WORKER_PREFIX . "-keyword >/dev/null & echo 2>&1 & echo $!");
                    if (empty($PID)) {
                        echo General::getColoredString("Cannot deamon PHP process to run job " . WORKER_PREFIX . "-keyword in background. \n", 'light_cyan', 'red');
                        return;
                    }
                    echo General::getColoredString("Job " . WORKER_PREFIX . "-keyword is running in background ... \n", 'green');
                }

                $funcName1 = SEARCH_PREFIX . 'writeKeyword';
                $methodHandler1 = '\My\Job\JobKeyword::writeKeyword';
                $worker->addFunction($funcName1, $methodHandler1, $this->serviceLocator);

                $funcName2 = SEARCH_PREFIX . 'editKeyword';
                $methodHandler2 = '\My\Job\JobKeyword::editKeyword';
                $worker->addFunction($funcName2, $methodHandler2, $this->serviceLocator);

                break;

            case WORKER_PREFIX . '-group':
                //start job group in background
                if ($params['background'] === 'true') {
                    $PID = shell_exec("nohup php " . PUBLIC_PATH . "/index.php worker --type=" . WORKER_PREFIX . "-group >/dev/null & echo 2>&1 & echo $!");
                    if (empty($PID)) {
                        echo General::getColoredString("Cannot deamon PHP process to run job " . WORKER_PREFIX . "-group in background. \n", 'light_cyan', 'red');
                        return;
                    }
                    echo General::getColoredString("Job " . WORKER_PREFIX . "-group is running in background ... \n", 'green');
                }

                $funcName1 = SEARCH_PREFIX . 'writeGroup';
                $methodHandler1 = '\My\Job\JobGroup::writeGroup';
                $worker->addFunction($funcName1, $methodHandler1, $this->serviceLocator);

                $funcName2 = SEARCH_PREFIX . 'editGroup';
                $methodHandler2 = '\My\Job\JobGroup::editGroup';
                $worker->addFunction($funcName2, $methodHandler2, $this->serviceLocator);

                break;

            case WORKER_PREFIX . '-permission':
                //start job group in background
                if ($params['background'] === 'true') {
                    $PID = shell_exec("nohup php " . PUBLIC_PATH . "/index.php worker --type=" . WORKER_PREFIX . "-permission >/dev/null & echo 2>&1 & echo $!");
                    if (empty($PID)) {
                        echo General::getColoredString("Cannot deamon PHP process to run job " . WORKER_PREFIX . "-permission in background. \n", 'light_cyan', 'red');
                        return;
                    }
                    echo General::getColoredString("Job " . WORKER_PREFIX . "-permission is running in background ... \n", 'green');
                }

                $funcName1 = SEARCH_PREFIX . 'writePermission';
                $methodHandler1 = '\My\Job\JobPermission::writePermission';
                $worker->addFunction($funcName1, $methodHandler1, $this->serviceLocator);

                $funcName2 = SEARCH_PREFIX . 'editPermission';
                $methodHandler2 = '\My\Job\JobPermission::editPermission';
                $worker->addFunction($funcName2, $methodHandler2, $this->serviceLocator);

                break;

            default:
                return General::getColoredString("Invalid or not found function \n", 'light_cyan', 'red');
        }

        if (empty($params['background'])) {
            echo General::getColoredString("Waiting for job...\n", 'green');
        } else {
            return;
        }
        $this->flush();
        while (@$worker->work() || ($worker->returnCode() == GEARMAN_IO_WAIT) || ($worker->returnCode() == GEARMAN_NO_JOBS)) {
            if ($worker->returnCode() != GEARMAN_SUCCESS) {
                echo "return_code: " . $worker->returnCode() . "\n";
                break;
            }
        }
    }

    public function checkWorkerRunningAction()
    {
        $arr_worker = self::$_arr_worker;
        foreach ($arr_worker as $worker) {
            $worker_name = WORKER_PREFIX . '-' . $worker;
            exec("ps -ef | grep -v grep | grep 'type={$worker_name}' | awk '{ print $2 }'", $PID);
            $PID = current($PID);

            if (empty($PID)) {
                $command = 'nohup php ' . PUBLIC_PATH . '/index.php worker --type=' . $worker_name . ' >/dev/null & echo 2>&1 & echo $!';
                $PID = shell_exec($command);
                if (empty($PID)) {
                    echo General::getColoredString("Cannot deamon PHP process to run job {$worker_name} in background. \n", 'light_cyan', 'red');
                } else {
                    echo General::getColoredString("PHP process run job {$worker_name} in background with PID : {$PID}. \n", 'green');
                }
            }
        }
    }

    public function crontabAction()
    {
        $params = $this->request->getParams();

        if (empty($params['type'])) {
            return General::getColoredString("Unknown type or id \n", 'light_cyan', 'red');
        }

        switch ($params['type']) {

            case 'update-vip-content':
                $this->_jobUpdateVipContent();
                break;

            default:
                echo General::getColoredString("Unknown type or id \n", 'light_cyan', 'red');

                break;
        }

        return true;
    }

    public function crawlerKeywordAction()
    {
        $this->getKeyword();
        return;
    }

    public function getKeyword()
    {
        $match = [
            '', 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'x', 'y', 'z', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9'
        ];
        $instanceSearchKeyWord = new \My\Search\Keyword();
        $arr_keyword = current($instanceSearchKeyWord->getListLimit(['is_crawler' => 0], 1, 1, ['key_id' => ['order' => 'asc']]));

        unset($instanceSearchKeyWord);
        if (empty($arr_keyword)) {
            return;
        }

        $serviceKeyword = $this->serviceLocator->get('My\Models\Keyword');
        $serviceKeyword->edit(['is_crawler' => 1, 'updated_date' => time()], $arr_keyword['key_id']);
        unset($serviceKeyword);

        $keyword = $arr_keyword['key_name'];

        foreach ($match as $key => $value) {
            if ($key == 0) {
                $key_match = $keyword . $value;
                $url = 'http://www.google.com/complete/search?output=search&client=chrome&q=' . rawurlencode($key_match) . '&hl=vi&gl=vn';
                $return = General::crawler($url);
                $this->add_keyword(json_decode($return)[1]);
                continue;
            } else {
                for ($i = 0; $i < 2; $i++) {
                    if ($i == 0) {
                        $key_match = $keyword . ' ' . $value;
                    } else {
                        $key_match = $value . ' ' . $keyword;
                    }
                    $url = 'http://www.google.com/complete/search?output=search&client=chrome&q=' . rawurlencode($key_match) . '&hl=vi&gl=vn';
                    $return = General::crawler($url);
                    $this->add_keyword(json_decode($return)[1]);
                    continue;
                }
            }
        };
        sleep(3);
        $this->getKeyword();
    }

    public function add_keyword($arr_key)
    {
        if (empty($arr_key)) {
            return false;
        }

        $instanceSearchKeyWord = new \My\Search\Keyword();
        foreach ($arr_key as $key_word) {
            $is_exsit = $instanceSearchKeyWord->getDetail(['key_slug' => trim(General::getSlug($key_word))]);

            if ($is_exsit) {
                continue;
            }

            $arr_data = [
                'key_name' => $key_word,
                'key_slug' => trim(General::getSlug($key_word)),
                'created_date' => time(),
                'is_crawler' => 0
            ];

            $serviceKeyword = $this->serviceLocator->get('My\Models\Keyword');
            $int_result = $serviceKeyword->add($arr_data);
            unset($serviceKeyword);
            if ($int_result) {
                echo \My\General::getColoredString("Insert success 1 row with id = {$int_result}", 'yellow');
            }
            $this->flush();
        }
        unset($instanceSearchKeyWord);
        return true;
    }

    public function sitemapAction()
    {
        $this->siteMapCategory();
        $this->siteMapContent();
        $this->sitemapOther();

        $xml = '<?xml version="1.0" encoding="UTF-8"?><sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"></sitemapindex>';
        $xml = new \SimpleXMLElement($xml);

        if (is_file(PUBLIC_PATH . '/xml/content.xml')) {
            $sitemap = $xml->addChild('sitemap', '');
            $sitemap->addChild('loc', BASE_URL . '/xml/content.xml');
            $sitemap->addChild('lastmod', date('c', time()));
        }
        if (is_file(PUBLIC_PATH . '/xml/category.xml')) {
            $sitemap = $xml->addChild('sitemap', '');
            $sitemap->addChild('loc', BASE_URL . '/xml/category.xml');
            $sitemap->addChild('lastmod', date('c', time()));
        }
//
//        if (is_file(PUBLIC_PATH . '/xml/general.xml')) {
//            $sitemap = $xml->addChild('sitemap');
//            $sitemap->addChild('loc', BASE_URL . '/xml/general.xml');
//            $sitemap->addChild('lastmod', date('c', time()));
//        }
        if (is_file(PUBLIC_PATH . '/xml/other.xml')) {
            $sitemap = $xml->addChild('sitemap');
            $sitemap->addChild('loc', BASE_URL . '/xml/other.xml');
            $sitemap->addChild('lastmod', date('c', time()));
        }

        $result = file_put_contents(PUBLIC_PATH . '/xml/bestquynhon_sitemap.xml', $xml->asXML());
        if ($result) {
            echo General::getColoredString("Create bestquynhon_sitemap.xml completed!", 'blue', 'cyan');
            $this->flush();
        }
        echo General::getColoredString("DONE!", 'blue', 'cyan');
        die('done');
    }

    public function siteMapCategory()
    {
        $doc = '<?xml version="1.0" encoding="UTF-8"?>';
        $doc .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">';
        $doc .= '</urlset>';
        $xml = new \SimpleXMLElement($doc);
        $this->flush();
        $instanceSearchCategory = new \My\Search\Category();
        $arrCategoryList = $instanceSearchCategory->getList(['cate_status' => 1], [], ['cate_sort' => ['order' => 'asc'], 'cate_id' => ['order' => 'asc']]);

        $arrCategoryParentList = [];
        $arrCategoryByParent = [];
        if (!empty($arrCategoryList)) {
            foreach ($arrCategoryList as $arrCategory) {
                if ($arrCategory['parent_id'] == 0) {
                    $arrCategoryParentList[$arrCategory['cate_id']] = $arrCategory;
                } else {
                    $arrCategoryByParent[$arrCategory['parent_id']][] = $arrCategory;
                }
            }
        }

        ksort($arrCategoryByParent);

        //findall district
        $instanceSearchDistrict = new \My\Search\District();
        $arrDistrict = $instanceSearchDistrict->getList(['not_dist_status' => -1], [], ['dist_sort' => ['order' => 'asc'], 'dist_id' => ['order' => 'asc']]);

        //findall properties
        $instanceSearchProperties = new \My\Search\Properties();
        $arrProperties = $instanceSearchProperties->getList(['not_prop_status' => -1, 'not_parent_id' => 0]);

        //format properties
        $arrPropertiesFormat = [];
        foreach ($arrProperties as $value) {
            $arrPropertiesFormat[$value['parent_id']][] = $value;
        }

        foreach ($arrCategoryParentList as $value) {
            $strCategoryURL = BASE_URL . '/danh-muc/' . $value['cate_slug'] . '-' . $value['cate_id'] . '.html';
            $url = $xml->addChild('url');
            $url->addChild('loc', $strCategoryURL);
            $url->addChild('lastmod', date('c', time()));
            $url->addChild('changefreq', 'daily');
            $url->addChild('priority', 0.7);

            //khu vuc tat ca
            foreach ($arrPropertiesFormat[$value['prop_id']] as $prop) {
                $href = BASE_URL . '/danh-muc/' . $value['cate_slug'] . '-' . $value['cate_id'] . '/khu-vuc/toan-tinh-0/nhu-cau/' . $prop['prop_slug'] . '-' . $prop['prop_id'] . '.html';
                $url = $xml->addChild('url');
                $url->addChild('loc', $href);
                $url->addChild('lastmod', date('c', time()));
                $url->addChild('changefreq', 'daily');
                $url->addChild('priority', 0.7);
            }

            foreach ($arrDistrict as $arrLoca) {
                $href = BASE_URL . '/danh-muc/' . $value['cate_slug'] . '-' . $value['cate_id'] . '/khu-vuc/' . $arrLoca['dist_slug'] . '-' . $arrLoca['dist_id'] . '.html';
                $url = $xml->addChild('url');
                $url->addChild('loc', $href);
                $url->addChild('lastmod', date('c', time()));
                $url->addChild('changefreq', 'daily');
                $url->addChild('priority', 0.7);

                foreach ($arrPropertiesFormat[$value['prop_id']] as $prop) {
                    $href = BASE_URL . '/danh-muc/' . $value['cate_slug'] . '-' . $value['cate_id'] . '/khu-vuc/' . $arrLoca['dist_slug'] . '-' . $arrLoca['dist_id'] . '/nhu-cau/' . $prop['prop_slug'] . '-' . $prop['prop_id'] . '.html';
                    $url = $xml->addChild('url');
                    $url->addChild('loc', $href);
                    $url->addChild('lastmod', date('c', time()));
                    $url->addChild('changefreq', 'daily');
                    $url->addChild('priority', 0.7);
                }
            }
        }
        foreach ($arrCategoryByParent as $key => $arr) {
            foreach ($arr as $value) {
                $strCategoryURL = BASE_URL . '/danh-muc/' . $value['cate_slug'] . '-' . $value['cate_id'] . '.html';
                $url = $xml->addChild('url');
                $url->addChild('loc', $strCategoryURL);
                $url->addChild('lastmod', date('c', time()));
                $url->addChild('changefreq', 'daily');
                $url->addChild('priority', 0.7);

                //khu vuc tat ca
                foreach ($arrPropertiesFormat[$arrCategoryParentList[$key]['prop_id']] as $prop) {
                    $href = BASE_URL . '/danh-muc/' . $value['cate_slug'] . '-' . $value['cate_id'] . '/khu-vuc/toan-tinh-0/nhu-cau/' . $prop['prop_slug'] . '-' . $prop['prop_id'] . '.html';
                    $url = $xml->addChild('url');
                    $url->addChild('loc', $href);
                    $url->addChild('lastmod', date('c', time()));
                    $url->addChild('changefreq', 'daily');
                    $url->addChild('priority', 0.7);
                }

                foreach ($arrDistrict as $arrLoca) {
                    $href = BASE_URL . '/danh-muc/' . $value['cate_slug'] . '-' . $value['cate_id'] . '/khu-vuc/' . $arrLoca['dist_slug'] . '-' . $arrLoca['dist_id'] . '.html';
                    $url = $xml->addChild('url');
                    $url->addChild('loc', $href);
                    $url->addChild('lastmod', date('c', time()));
                    $url->addChild('changefreq', 'daily');
                    $url->addChild('priority', 0.7);

                    foreach ($arrPropertiesFormat[$arrCategoryParentList[$key]['prop_id']] as $prop) {
                        $href = BASE_URL . '/danh-muc/' . $value['cate_slug'] . '-' . $value['cate_id'] . '/khu-vuc/' . $arrLoca['dist_slug'] . '-' . $arrLoca['dist_id'] . '/nhu-cau/' . $prop['prop_slug'] . '-' . $prop['prop_id'] . '.html';
                        $url = $xml->addChild('url');
                        $url->addChild('loc', $href);
                        $url->addChild('lastmod', date('c', time()));
                        $url->addChild('changefreq', 'daily');
                        $url->addChild('priority', 0.7);
                    }
                }
            }
        }

        unlink(PUBLIC_PATH . '/xml/category.xml');
        $result = file_put_contents(PUBLIC_PATH . '/xml/category.xml', $xml->asXML());
        if ($result) {
            echo General::getColoredString("Sitemap category done", 'blue', 'cyan');
            $this->flush();
        }

        return true;
    }

    public function siteMapContent()
    {
        $instanceSearchContent = new \My\Search\Content();
        $doc = '<?xml version="1.0" encoding="UTF-8"?>';
        $doc .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">';
        $doc .= '</urlset>';
        $xml = new \SimpleXMLElement($doc);
        $this->flush();
        $intLimit = 100;
        for ($intPage = 1; $intPage < 10000; $intPage++) {
            $arrContentList = $instanceSearchContent->getListLimit(['not_cont_status' => -1], $intPage, $intLimit, ['cont_id' => ['order' => 'desc']]);
            if (empty($arrContentList)) {
                break;
            }
            foreach ($arrContentList as $arr) {
                $href = BASE_URL . '/rao-vat/' . $arr['cont_slug'] . '-' . $arr['cont_id'] . '.html';
                $url = $xml->addChild('url');
                $url->addChild('loc', $href);
                $url->addChild('lastmod', date('c', time()));
                $url->addChild('changefreq', 'daily');
                $url->addChild('priority', 0.7);
            }
        }

        unlink(PUBLIC_PATH . '/xml/content.xml');
        $result = file_put_contents(PUBLIC_PATH . '/xml/content.xml', $xml->asXML());
        if ($result) {
            echo General::getColoredString("Sitemap content done", 'blue', 'cyan');
            $this->flush();
        }

        return true;
    }

    public function siteMapSearch()
    {

    }

    private function sitemapOther()
    {
        $doc = '<?xml version="1.0" encoding="UTF-8"?>';
        $doc .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">';
        $doc .= '</urlset>';
        $xml = new \SimpleXMLElement($doc);
        $this->flush();
        $arrData = ['http://bestquynhon.com/'];
        foreach ($arrData as $value) {
            $href = $value;
            $url = $xml->addChild('url');
            $url->addChild('loc', $href);
            $url->addChild('lastmod', date('c', time()));
            $url->addChild('changefreq', 'daily');
            $url->addChild('priority', 1);
        }

        unlink(PUBLIC_PATH . '/xml/other.xml');
        $result = file_put_contents(PUBLIC_PATH . '/xml/other.xml', $xml->asXML());
        if ($result) {
            echo General::getColoredString("Sitemap orther done", 'blue', 'cyan');
            $this->flush();
        }
    }

    public function crawlerAction()
    {
        $params = $this->request->getParams();
        $type = $params['type'];
        if (empty($type)) {
            $this->__vnexp();
            $this->__thethao247();
            $this->__congnghe();
            $this->__zingvn();
            $this->__2sao();
            return true;
        }

        if ($type == 'vnexpress') {
            $this->__vnexp();
            return true;
        }

        if ($type == 'thethao247') {
            $this->__thethao247();
            return true;
        }

        if ($type == 'congnghe') {
            $this->__congnghe();
            return true;
        }

        if ($type == 'zingvn') {
            $this->__zingvn();
            return true;
        }

        if ($type == '2sao') {
            $this->__2sao();
            return true;
        }
    }

    public function __vnexp()
    {
        include_once PUBLIC_PATH . '/simple_html_dom.php';

        $arr_cate = [
            2 => 'http://vnexpress.net/tin-tuc/thoi-su',
            3 => 'http://vnexpress.net/tin-tuc/the-gioi',
            8 => 'http://thethao.vnexpress.net/tin-tuc/cac-mon-khac'
        ];

        foreach ($arr_cate as $cate_id => $strURL) {
            for ($i = 3; $i >= 1; $i--) {
                $sourceURL = $strURL;
                if ($i != 1) {
                    $sourceURL = $strURL . '/page/' . $i . '.html';
                }

                $content = General::crawler($sourceURL);

                $dom = new \Zend\Dom\Query($content);
                unset($content);
                try {
                    $results = $dom->execute('.block_mid_new .title_news a');
                } catch (\Exception $exc) {
                    continue;
                }

                if (count($results) <= 0) {
                    echo \My\General::getColoredString("Continue 1 page {$i} cate_id {$cate_id}", 'red');
                    continue;
                }

                foreach ($results as $result) {
                    try {
                        $arr_data = [];
                        $arr_data['cont_title'] = trim($result->textContent);
                        $arr_data['cont_slug'] = General::getSlug($arr_data['cont_title']);

                        //find in db with
                        $instanceSearchContent = new \My\Search\Content();
                        $arr_content_detail = $instanceSearchContent->getDetail(['cont_slug' => $arr_data['cont_slug'], 'not_cont_status' => -1, 'cate_id' => $cate_id]);

                        if ($arr_content_detail) {
                            echo \My\General::getColoredString("Continue with exits title", 'red');

                            continue;
                        }

                        $content = General::crawler($result->getAttribute('href'));

                        preg_match('/<div class="short_intro txt_666">(.*?)<\/div>/', $content, $matches);

                        if (empty($matches)) {
                            echo \My\General::getColoredString("Continue not found content description", 'red');
                            continue;
                        }

                        $arr_data['cont_desciption'] = trim(strip_tags($matches[1]));
                        $html = str_get_html($content);
                        $cont_detail = $html->find('.fck_detail', 0)->outertext;
                        $img = $html->find(".fck_detail img");

                        if (count($img) > 0) {
                            foreach ($img as $key => $im) {
                                $extension = end(explode('.', end(explode('/', $im->src))));
                                $name = $arr_data['cont_slug'] . '-' . ($key + 1) . '.' . $extension;
                                file_put_contents(STATIC_PATH . '/uploads/content/' . $name, General::crawler($im->src));

                                $cont_detail = str_replace($im->src, STATIC_URL . '/uploads/content/' . $name, $cont_detail);
                                if ($key == 0) {
                                    $arr_data['cont_main_image'] = STATIC_URL . '/uploads/content/' . $name;
                                    $images = General::resizeImages('content', STATIC_PATH . '/uploads/content/' . $name, $name);
                                    if ($images != false) {
                                        $arr_data['cont_image'] = json_encode($images);
                                    }
                                }
                            }
                        }

                        $cont_detail = trim(strip_tags($cont_detail, '<img><b><p><br><span><br /><strong><table><td><tr><th><tbody>'));
                        $cont_detail = str_replace('class="Normal"', 'class="content"', $cont_detail);
                        $arr_data['cont_detail'] = $cont_detail;
                        unset($cont_detail);
                        unset($content);
                        unset($html);
                        unset($img);

                        if (empty($arr_data['cont_detail'])) {
                            continue;
                        }

                        $arr_data['cont_detail_text'] = trim(strip_tags($arr_data['cont_detail']));
                        $arr_data['created_date'] = time();
                        $arr_data['updated_date'] = time();
                        $arr_data['cate_id'] = $cate_id;
                        $arr_data['method'] = 'crawler';
                        $arr_data['from_source'] = 'VnExpress';
                        $arr_data['cont_views'] = 0;
                        $arr_data['meta_keyword'] = str_replace(' ', ',', $arr_data['cont_title']);
                        $arr_data['cont_status'] = 1;

                        $serviceContent = $this->serviceLocator->get('My\Models\Content');
                        $id = $serviceContent->add($arr_data);

                        if ($id) {
                            echo \My\General::getColoredString("Crawler success 1 post from VnEpress id = {$id} \n", 'green');
                        } else {
                            echo \My\General::getColoredString("Can not inset content db", 'red');
                        }
                        unset($serviceContent);
                        unset($arr_data);
                        $this->flush();

                        sleep(5);
                    } catch (\Exception $exc) {
                        continue;
                    }
                }
            }
        }
        echo \My\General::getColoredString("Crawler to VNEXPRESS success", 'green');
        return true;
    }

    public function __congnghe()
    {
        include_once PUBLIC_PATH . '/simple_html_dom.php';

        $arr_cate = [
            10 => 'http://genk.vn/mobile/',
            11 => 'http://ictnews.vn/internet/',
            12 => 'http://genk.vn/kham-pha/',
            13 => 'http://genk.vn/thu-thuat/',
            18 => 'http://genk.vn/do-choi-so/',
        ];

        foreach ($arr_cate as $cate_id => $strURL) {
            echo \My\General::getColoredString("Start crawler url {$strURL} \n", 'red');
            if ($cate_id == 11) {
                for ($i = 3; $i >= 1; $i--) {
                    $sourceURL = $strURL . $i;
                    echo \My\General::getColoredString("Start crawler url {$sourceURL} \n", 'red');

                    $content = General::crawler($sourceURL);

                    $dom = str_get_html($content);

                    try {
                        $results = $dom->find('.main-small .g-title');
                    } catch (\Exception $exc) {
                        continue;
                    }

                    if (count($results) <= 0) {
                        continue;
                    }

                    foreach ($results as $item) {
                        try {
                            $arr_data = [];
                            $arr_data['cont_title'] = trim($item->plaintext);
                            $arr_data['cont_slug'] = General::getSlug($arr_data['cont_title']);

                            //find in db with
                            $instanceSearchContent = new \My\Search\Content();
                            $arr_content_detail = $instanceSearchContent->getDetail(['cont_slug' => $arr_data['cont_slug'], 'not_cont_status' => -1, 'cate_id' => $cate_id]);

                            if ($arr_content_detail) {
                                echo \My\General::getColoredString("Continue with exits title", 'red');
                                continue;
                            }

                            $content = General::crawler($item->href);
                            $html = str_get_html($content);

                            $arr_data['cont_desciption'] = trim($html->find('.news-desc', 0)->plaintext);


                            $cont_detail = $html->find('.maincontent', 0)->outertext;
                            $img = $html->find(".maincontent img");

                            if (count($img) > 0) {
                                foreach ($img as $key => $im) {
                                    $extension = end(explode('.', end(explode('/', $im->src))));
                                    $name = $arr_data['cont_slug'] . '-' . ($key + 1) . '.' . $extension;
                                    file_put_contents(STATIC_PATH . '/uploads/content/' . $name, General::crawler($im->src));

                                    $cont_detail = str_replace($im->src, STATIC_URL . '/uploads/content/' . $name, $cont_detail);
                                    if ($key == 0) {
                                        $arr_data['cont_main_image'] = STATIC_URL . '/uploads/content/' . $name;
                                        $images = General::resizeImages('content', STATIC_PATH . '/uploads/content/' . $name, $name);
                                        if ($images != false) {
                                            $arr_data['cont_image'] = json_encode($images);
                                        }
                                    }
                                }
                            }
                            $cont_detail = trim(strip_tags($cont_detail, '<img><b><p><br><span><br /><strong><table><tr><th><tbody><td>'));
                            $cont_detail = str_replace('class="Normal"', 'class="content"', $cont_detail);
                            $arr_data['cont_detail'] = $cont_detail;
                            unset($cont_detail);
                            unset($content);
                            unset($html);
                            unset($img);

                            if (empty($arr_data['cont_detail'])) {
                                continue;
                            }

                            $arr_data['cont_detail_text'] = trim(strip_tags($arr_data['cont_detail']));
                            $arr_data['created_date'] = time();
                            $arr_data['updated_date'] = time();
                            $arr_data['cate_id'] = $cate_id;
                            $arr_data['method'] = 'crawler';
                            $arr_data['from_source'] = 'Itc News';
                            $arr_data['cont_views'] = 0;
                            $arr_data['meta_keyword'] = str_replace(' ', ',', $arr_data['cont_title']);
                            $arr_data['cont_status'] = 1;
                            $serviceContent = $this->serviceLocator->get('My\Models\Content');
                            $id = $serviceContent->add($arr_data);

                            if ($id) {
                                echo \My\General::getColoredString("Crawler success 1 post from ITC id = {$id} \n", 'green');
                            } else {
                                echo \My\General::getColoredString("Can not insert content db", 'red');
                            }
                            unset($serviceContent);
                            unset($arr_data);
                            $this->flush();
                        } catch (\Exception $exc) {
                            continue;
                        }
                    }
                }
                echo \My\General::getColoredString("Crawler ITC new success {$sourceURL} \n", 'red');
            } else {
                for ($i = 3; $i >= 1; $i--) {

                    $sourceURL = $strURL . 'page-' . $i . '.chn';
                    echo \My\General::getColoredString("Start crawler url {$sourceURL} \n", 'red');

                    $content = General::crawler($sourceURL);
                    $html = str_get_html($content);
                    unset($content);
                    try {
                        $results = $html->find('.news-stream h2');
                    } catch (\Exception $exc) {
                        continue;
                    }

                    foreach ($results as $eee => $dom) {
                        try {
                            $arr_data = [];
                            $html_temp = str_get_html($dom->outertext);
                            $arr_data['cont_title'] = $html_temp->find('h2', 0)->plaintext;
                            $arr_data['cont_slug'] = General::getSlug($arr_data['cont_title']);

//                        find db
                            $instanceSearchContent = new \My\Search\Content();
                            $arr_content_detail = $instanceSearchContent->getDetail(['cont_slug' => $arr_data['cont_slug'], 'not_cont_status' => -1, 'cate_id' => $cate_id]);

                            if ($arr_content_detail) {
                                echo '<pre>';
                                print_r($eee);
                                echo '</pre>';
                                echo \My\General::getColoredString("ton tai trong db", 'red');
                                echo \My\General::getColoredString("slug = {$arr_data['cont_slug']}", 'red');
                                continue;
                            }


                            $href_content = 'http://genk.vn' . $html_temp->find('a', 0)->href;
                            echo \My\General::getColoredString("href content {$href_content} \n", 'red');

                            $html_content = str_get_html(General::crawler($href_content));

                            $arr_data['cont_desciption'] = $html_content->find('.content .init_content', 0)->plaintext;

                            $cont_detail = $html_content->find('#ContentDetail', 0)->outertext;

                            $img = $html_content->find("#ContentDetail img");

                            if (count($img) > 0) {
                                foreach ($img as $key => $im) {
                                    $extension = end(explode('.', end(explode('/', $im->src))));
                                    $name = $arr_data['cont_slug'] . '-' . ($key + 1) . '.' . $extension;
                                    file_put_contents(STATIC_PATH . '/uploads/content/' . $name, General::crawler($im->src));
                                    $cont_detail = str_replace($im->src, STATIC_URL . '/uploads/content/' . $name, $cont_detail);
                                    if ($key == 0) {
                                        $arr_data['cont_main_image'] = STATIC_URL . '/uploads/content/' . $name;
                                        $images = General::resizeImages('content', STATIC_PATH . '/uploads/content/' . $name, $name);
                                        if ($images != false) {
                                            $arr_data['cont_image'] = json_encode($images);
                                        }
                                    }
                                }
                            }

                            $cont_detail = trim(strip_tags($cont_detail, '<img><b><p><br><span><br /><strong><h2><h1><h3><h4><table><tr><th><tbody><td>'));
                            $arr_data['cont_detail'] = $cont_detail;

                            unset($cont_detail);
                            unset($html_temp);
                            unset($html_content);
                            unset($img);

                            if (empty($arr_data['cont_detail'])) {
                                continue;
                            }

                            $arr_data['cont_detail_text'] = trim(strip_tags($arr_data['cont_detail']));
                            $arr_data['created_date'] = time();
                            $arr_data['updated_date'] = time();
                            $arr_data['cate_id'] = $cate_id;
                            $arr_data['method'] = 'crawler';
                            $arr_data['from_source'] = 'Genk';
                            $arr_data['cont_views'] = 0;
                            $arr_data['meta_keyword'] = str_replace(' ', ',', $arr_data['cont_title']);
                            $arr_data['cont_status'] = 1;

                            $serviceContent = $this->serviceLocator->get('My\Models\Content');
                            $id = $serviceContent->add($arr_data);

                            if ($id) {
                                echo \My\General::getColoredString("Crawler success 1 post from Genk id = {$id} \n", 'green');
                            } else {
                                echo \My\General::getColoredString("Can not insert content db", 'red');
                            }

                            unset($serviceContent);
                            unset($arr_data);
                            $this->flush();
                        } catch (\Exception $exc) {
                            continue;
                        }
                    }
                }
            }
            echo \My\General::getColoredString("Crawler success from Genk", 'green');
        }
        echo \My\General::getColoredString("Crawler success Cong Nghe", 'green');
        return true;
    }

    public function __thethao247()
    {
        include_once PUBLIC_PATH . '/simple_html_dom.php';

        $arr_cate = [
            5 => 'http://thethao247.vn/bong-da-viet-nam-c1/',
            6 => 'http://thethao247.vn/bong-da-quoc-te-c2/',
            7 => 'http://thethao247.vn/quan-vot-c4/'
        ];
        $arr_continue = [
            'http://thethao247.vn/quan-vot/tin-tuc-tennis/lich-thi-dau-ket-qua-roland-garros-2015-ngay-27-5-d105535.html',
            'http://thethao247.vn/quan-vot/tin-tuc-tennis/lich-thi-dau-ket-qua-wimbledon-2015-ngay-29-6-vong-1-d107776.html',
            'http://thethao247.vn/quan-vot/tin-tuc-tennis/lich-thi-dau-ket-qua-wimbledon-2015-ngay-1-7-d107952.html',
            'http://thethao247.vn/quan-vot/tin-tuc-tennis/egypt-f27-mens-future-thang-ap-dao-hoang-nam-vao-tu-ket-d110886.html',
            'http://thethao247.vn/quan-vot/tin-tuc-tennis/lich-thi-dau-ket-qua-wimbledon-2015-ngay-30-6-d107881.html'
        ];
        foreach ($arr_cate as $cate_id => $strURL) {
            echo \My\General::getColoredString("crawler success with url {$strURL}", 'green');
            for ($i = 3; $i >= 1; $i--) {

                $sourceURL = $strURL;
                if ($i != 1) {
                    $sourceURL = $strURL . 'p' . $i;
                }
                echo \My\General::getColoredString("Start Crawler Thethao 247 -- page{$i} \n", 'green');

                $content = General::crawler($sourceURL);
                $html = str_get_html($content);
                unset($content);
                try {
                    $results = $html->find('.cat-row');
                } catch (\Exception $exc) {
                    continue;
                }

                foreach ($results as $dom) {
                    try {
                        $arr_data = [];
                        $html_temp = str_get_html($dom->outertext);
                        $arr_data['cont_title'] = $html_temp->find('.title2', 0)->plaintext;
                        $arr_data['cont_slug'] = General::getSlug($arr_data['cont_title']);

//                        find db
                        $instanceSearchContent = new \My\Search\Content();
                        $arr_content_detail = $instanceSearchContent->getDetail(['cont_slug' => $arr_data['cont_slug'], 'not_cont_status' => -1, 'cate_id' => $cate_id]);

                        if ($arr_content_detail) {
                            echo \My\General::getColoredString("da ton tai tin nay trong db \n", 'red');
                            continue;
                        }

                        echo \My\General::getColoredString("href {$html_temp->find('.info_cat_row a', 0)->href} \n", 'green');

                        if (in_array($html_temp->find('.info_cat_row a', 0)->href, $arr_continue)) {
                            continue;
                        }
                        $href_content = $html_temp->find('.info_cat_row a', 0)->href;
                        $html_content = str_get_html(General::crawler($href_content));
                        try {
                            if (count($html_content->find('.sapo_detail')) < 1) {
                                continue;
                            }
                            $arr_data['cont_desciption'] = $html_content->find('.sapo_detail', 0)->plaintext;
                        } catch (\Exception $exc) {
                            echo $exc->getMessage();
                            continue;
                        }

                        try {
                            if (count($html_content->find('#main-detail #add')) > 1) {
                                foreach ($html_content->find('#main-detail #add') as $add) {
                                    $add->outertext = '';
                                }
                            }
                        } catch (\Exception $exc) {
                            echo $exc->getMessage();
                        }

                        $cont_detail = $html_content->find('#main-detail', 0)->outertext;

                        $img = $html_content->find("#main-detail img");

                        if (count($img) > 0) {
                            foreach ($img as $key => $im) {
                                $extension = end(explode('.', end(explode('/', $im->src))));
                                $name = $arr_data['cont_slug'] . '-' . ($key + 1) . '.' . $extension;
                                file_put_contents(STATIC_PATH . '/uploads/content/' . $name, General::crawler($im->src));
                                $cont_detail = str_replace($im->src, STATIC_URL . '/uploads/content/' . $name, $cont_detail);
                                if ($key == 0) {
                                    $arr_data['cont_main_image'] = STATIC_URL . '/uploads/content/' . $name;
                                    $images = General::resizeImages('content', STATIC_PATH . '/uploads/content/' . $name, $name);
                                    if ($images != false) {
                                        $arr_data['cont_image'] = json_encode($images);
                                    }
                                }
                            }
                        }

                        $cont_detail = trim(strip_tags($cont_detail, '<img><b><p><br><span><br /><strong><h2><h1><h3><h4><table><td><tr><th><tbody>'));
                        $cont_detail = str_replace('class="Normal"', 'class="content"', $cont_detail);

                        $cont_detail = preg_replace('/<h2 style="text-align: center;">(.*?)<\/h2>/', '', $cont_detail);

                        $arr_data['cont_detail'] = $cont_detail;

                        unset($cont_detail);
                        unset($html_temp);
                        unset($html_content);
                        unset($img);

                        if (empty($arr_data['cont_detail'])) {
                            continue;
                        }

                        if (empty($arr_data['cont_detail'])) {
                            continue;
                        }

                        $arr_data['cont_detail_text'] = trim(strip_tags($arr_data['cont_detail']));
                        $arr_data['created_date'] = time();
                        $arr_data['updated_date'] = time();
                        $arr_data['cate_id'] = $cate_id;
                        $arr_data['method'] = 'crawler';
                        $arr_data['from_source'] = 'Thethao247';
                        $arr_data['cont_views'] = 0;
                        $arr_data['meta_keyword'] = str_replace(' ', ',', $arr_data['cont_title']);
                        $arr_data['cont_status'] = 1;

                        $serviceContent = $this->serviceLocator->get('My\Models\Content');
                        $id = $serviceContent->add($arr_data);

                        if ($id) {
                            echo \My\General::getColoredString("Crawler success 1 post from thethao247 id = {$id} \n", 'green');
                        } else {
                            echo \My\General::getColoredString("Can not insert content db", 'red');
                        }

                        unset($serviceContent);
                        unset($arr_data);
                        $this->flush();
                    } catch (\Exception $exc) {
                        continue;
                    }
                }
                unset($results);
            }
            echo \My\General::getColoredString("Crawler success from page {$i} from thethao247", 'green');
            echo \My\General::getColoredString("Crawler success from thethao247", 'green');
        }

        echo \My\General::getColoredString("Crawler success category Thethao", 'green');
        return true;
    }

    public function __zingvn()
    {
        include_once PUBLIC_PATH . '/simple_html_dom.php';

        $arr_cate = [
            15 => 'http://news.zing.vn/sao-viet/trang',
            16 => 'http://news.zing.vn/sao-chau-a/trang',
            17 => 'http://news.zing.vn/sao-hollywood/trang',
            20 => 'http://news.zing.vn/thoi-trang-sao/trang',
            21 => 'http://news.zing.vn/mac-dep/trang',
            22 => 'http://news.zing.vn/lam-dep/trang',
            26 => 'http://news.zing.vn/guong-mat-tre/trang',
            27 => 'http://news.zing.vn/cong-dong-mang/trang',
            28 => 'http://news.zing.vn/su-kien/trang'
        ];

        foreach ($arr_cate as $cate_id => $strURL) {
            echo \My\General::getColoredString("Start crawler url {$strURL} \n", 'red');

            for ($i = 3; $i >= 1; $i--) {
                $sourceURL = $strURL . $i . '.html';
                echo \My\General::getColoredString("Start crawler url {$sourceURL} \n", 'red');

                $content = General::crawler($sourceURL);
                $dom = str_get_html($content);

                try {
                    $results = $dom->find('.cate_content article .title');
                } catch (\Exception $exc) {
                    continue;
                }

                if (count($results) <= 0) {
                    continue;
                }

                foreach ($results as $item) {
                    try {
                        $arr_data = [];
                        $arr_data['cont_title'] = trim($item->plaintext);
                        $arr_data['cont_slug'] = General::getSlug($arr_data['cont_title']);

                        //find in db with
                        $instanceSearchContent = new \My\Search\Content();
                        $arr_content_detail = $instanceSearchContent->getDetail(['cont_slug' => $arr_data['cont_slug'], 'not_cont_status' => -1, 'cate_id' => $cate_id]);

                        if ($arr_content_detail) {
                            echo \My\General::getColoredString("Continue with exits title", 'red');
                            continue;
                        }
                        $temp = str_get_html($item->outertext);

                        $content = General::crawler('http://news.zing.vn' . $temp->find('a', 0)->href);

                        if ($content == false) {
                            continue;
                        }
                        unset($temp);
                        $html = str_get_html($content);

                        $arr_data['cont_desciption'] = trim($html->find('.the-article-summary', 0)->plaintext);

                        $cont_detail = $html->find('.the-article-body', 0)->outertext;
                        $img = $html->find(".the-article-body img");

                        if (count($img) > 0) {
                            foreach ($img as $key => $im) {
                                $extension = end(explode('.', end(explode('/', $im->src))));
                                $name = $arr_data['cont_slug'] . '-' . ($key + 1) . '.' . $extension;
                                file_put_contents(STATIC_PATH . '/uploads/content/' . $name, General::crawler($im->src));

                                $cont_detail = str_replace($im->src, STATIC_URL . '/uploads/content/' . $name, $cont_detail);
                                if ($key == 0) {
                                    $arr_data['cont_main_image'] = STATIC_URL . '/uploads/content/' . $name;
                                    $images = General::resizeImages('content', STATIC_PATH . '/uploads/content/' . $name, $name);
                                    if ($images != false) {
                                        $arr_data['cont_image'] = json_encode($images);
                                    }
                                }
                            }
                        }
                        $cont_detail = trim(strip_tags($cont_detail, '<img><b><p><br><span><br /><strong><table><td><tr><th><tbody>'));
                        $cont_detail = str_replace('class="Normal"', 'class="content"', $cont_detail);
                        $arr_data['cont_detail'] = $cont_detail;
                        unset($cont_detail);
                        unset($content);
                        unset($html);
                        unset($img);

                        if (empty($arr_data['cont_detail'])) {
                            continue;
                        }

                        $arr_data['cont_detail_text'] = trim(strip_tags($arr_data['cont_detail']));
                        $arr_data['created_date'] = time();
                        $arr_data['updated_date'] = time();
                        $arr_data['cate_id'] = $cate_id;
                        $arr_data['method'] = 'crawler';
                        $arr_data['from_source'] = 'Zing.vn';
                        $arr_data['cont_views'] = 0;
                        $arr_data['meta_keyword'] = str_replace(' ', ',', $arr_data['cont_title']);
                        $arr_data['cont_status'] = 1;

                        $serviceContent = $this->serviceLocator->get('My\Models\Content');
                        $id = $serviceContent->add($arr_data);

                        if ($id) {
                            echo \My\General::getColoredString("Crawler success 1 post from Zind id = {$id} \n", 'green');
                        } else {
                            echo \My\General::getColoredString("Can not insert content db", 'red');
                        }
                        unset($serviceContent);
                        unset($arr_data);
                        $this->flush();
                    } catch (\Exception $exc) {
                        continue;
                    }
                }
            }
            echo \My\General::getColoredString("Crawler Zing new success {$strURL} \n", 'green');
        }
        echo \My\General::getColoredString("Crawler from Zing.vn success \n", 'green');
        return true;
    }

    public function __2sao()
    {
        include_once PUBLIC_PATH . '/simple_html_dom.php';

        $arr_cate = [
            23 => 'http://2sao.vn/p0c1052/chuyen-la/trang-',
            24 => 'http://2sao.vn/p0c1005/hoi-dap/trang-'
        ];

        foreach ($arr_cate as $cate_id => $strURL) {
            echo \My\General::getColoredString("Start crawler url {$strURL} \n", 'red');

            for ($i = 5; $i >= 1; $i--) {
                $sourceURL = $strURL . $i . '.vnn';

                echo \My\General::getColoredString("Start crawler url {$sourceURL} \n", 'green');

                $content = General::crawler($sourceURL);
                $dom = str_get_html($content);

                try {
                    $results = $dom->find('.span85 .nav1 li.lilist .divnav2 a');
                } catch (\Exception $exc) {
                    continue;
                }

                if (count($results) <= 0) {
                    continue;
                }

                foreach ($results as $item) {
                    try {
                        if (strpos($item->href, 'clip')) {
                            echo \My\General::getColoredString("Khong lay clip url {$item->href} \n", 'red');
                            continue;
                        }

                        $arr_data = [];
                        $arr_data['cont_title'] = trim($item->plaintext);
                        $arr_data['cont_slug'] = General::getSlug($arr_data['cont_title']);

                        //find in db with
                        $instanceSearchContent = new \My\Search\Content();
                        $arr_content_detail = $instanceSearchContent->getDetail(['cont_slug' => $arr_data['cont_slug'], 'not_cont_status' => -1, 'cate_id' => $cate_id]);

                        if ($arr_content_detail) {
                            echo \My\General::getColoredString("Continue with exits title", 'red');
                            continue;
                        }

                        $content = General::crawler('http://2sao.vn' . $item->href);

                        if ($content == false) {
                            continue;
                        }

                        $html = str_get_html($content);
                        try {
                            $arr_data['cont_desciption'] = trim($html->find('.fixfont', 0)->plaintext);
                        } catch (\Exception $exc) {
                            echo '<pre>';
                            print_r($exc->getMessage());
                            echo '</pre>';
                            continue;
                        }

                        $cont_detail = $html->find('.2saodetial', 0)->outertext;
                        $img = $html->find(".2saodetial img");

                        if (count($img) > 0) {
                            $arr_data['cont_image'] = [];
                            foreach ($img as $key => $im) {
                                $extension = end(explode('.', end(explode('/', $im->src))));
                                $name = $arr_data['cont_slug'] . '-' . ($key + 1) . '.' . $extension;
                                file_put_contents(STATIC_PATH . '/uploads/content/' . $name, General::crawler($im->src));
                                $cont_detail = str_replace($im->src, STATIC_URL . '/uploads/content/' . $name, $cont_detail);
                                if ($key == 0) {
                                    $arr_data['cont_main_image'] = STATIC_URL . '/uploads/content/' . $name;
                                    $images = General::resizeImages('content', STATIC_PATH . '/uploads/content/' . $name, $name);
                                    if ($images != false) {
                                        $arr_data['cont_image'] = json_encode($images);
                                    }
                                }
                            }
                        }

                        $cont_detail = trim(strip_tags($cont_detail, '<img><b><p><br><span><br /><strong><table><td><tr><th><tbody>'));
                        $cont_detail = str_replace('class="Normal"', 'class="content"', $cont_detail);
                        $arr_data['cont_detail'] = $cont_detail;

                        unset($cont_detail);
                        unset($content);
                        unset($html);
                        unset($img);

                        if (empty($arr_data['cont_detail'])) {
                            continue;
                        }

                        $arr_data['cont_detail_text'] = trim(strip_tags($arr_data['cont_detail']));
                        $arr_data['created_date'] = time();
                        $arr_data['updated_date'] = time();
                        $arr_data['cate_id'] = $cate_id;
                        $arr_data['method'] = 'crawler';
                        $arr_data['from_source'] = '2sao.vn';
                        $arr_data['cont_views'] = 0;
                        $arr_data['meta_keyword'] = str_replace(' ', ',', $arr_data['cont_title']);
                        $arr_data['cont_status'] = 1;

                        $serviceContent = $this->serviceLocator->get('My\Models\Content');
                        $id = $serviceContent->add($arr_data);

                        if ($id) {
                            echo \My\General::getColoredString("Crawler success 1 post from 2sao.vn id = {$id} \n", 'green');
                        } else {
                            echo \My\General::getColoredString("Can not insert content db", 'red');
                        }
                        unset($serviceContent);
                        unset($arr_data);

                        $this->flush();
                    } catch (\Exception $exc) {
                        continue;
                    }
                }
            }
            echo \My\General::getColoredString("Crawler 2SAO.VN success {$strURL} \n", 'green');
        }
        echo \My\General::getColoredString("Crawler success 2SAO.VN", 'green');
        return true;
    }

}
