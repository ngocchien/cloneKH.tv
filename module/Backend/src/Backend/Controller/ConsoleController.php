<?php

namespace Backend\Controller;

use My\General,
    My\Controller\MyController,
    Sunra\PhpSimple\HtmlDomParser;

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
        $instanceSearch = new \My\Search\GeneralSearch();

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
        $intLimit = 2000;
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
//        $instanceSearchCategory = new \My\Search\Category();
//        $arr_category = $instanceSearchCategory->getList(['cate_status' => 1]);
//        $instanceSearchKeyword = new \My\Search\Keyword();
//        $instanceSearchKeyword->createIndex();
//        die();
//        $content = file_get_contents(PUBLIC_PATH . '/keyword.txt');
//        $content = explode("\n", $content);

        foreach ($arr_category as $category) {

            $isexist = $instanceSearchKeyword->getDetail(['key_slug' => General::getSlug($category['cate_name'])]);

            if ($isexist) {
                continue;
            }

            $arr_data = [
                'key_name' => $category['cate_name'],
                'key_slug' => General::getSlug($category['cate_name'])
            ];

            $serviceKeyword = $this->serviceLocator->get('My\Models\Keyword');
            $int_result = $serviceKeyword->add($arr_data);
            unset($serviceKeyword);
            if ($int_result) {
                echo General::getColoredString("add keyword : {$category['cate_name']} success", 'green');
            } else {
                echo General::getColoredString("add keyword : {$category['cate_name']} error", 'red');
            }
            $this->flush();
        }
        echo General::getColoredString("add keyword complete", 'yellow', 'cyan');
        return true;
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

    public function getHotKey()
    {
        $current_date = date('Ymd');
        //https://www.google.com/trends/hottrends/hotItems?ajax=1&pn=p28&htd=20150101&htv=l
        for ($i = 0; $i <= 10000; $i++) {
            $date = date($current_date, strtotime('-' . $i . ' days'));
            $href = 'https://www.google.com/trends/hottrends/hotItems?ajax=1&pn=p28&htd=' . $date . '&htv=l';
        }

    }

    public function getKeyword()
    {
        $match = [
            '', 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'x', 'y', 'z', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9'
        ];
        $instanceSearchKeyWord = new \My\Search\Keyword();
        $arr_keyword = current($instanceSearchKeyWord->getListLimit(['is_crawler' => 0, 'key_id_greater' => 2962], 1, 1, ['key_id' => ['order' => 'asc']]));

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
            $this->flush();
        };
        $this->flush();
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
        unlink(PUBLIC_PATH . '/rss/sitemap.xml');
        $this->sitemapOther();
        $this->siteMapCategory();
        $this->siteMapContent();
        $this->siteMapSearch();

        $xml = '<?xml version="1.0" encoding="UTF-8"?><sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"></sitemapindex>';
        $xml = new \SimpleXMLElement($xml);

        $all_file = scandir(PUBLIC_PATH . '/rss/');
        sort($all_file, SORT_NATURAL | SORT_FLAG_CASE);
//        sort($all_file);
        foreach ($all_file as $file_name) {
            if (strpos($file_name, 'xml') !== false) {
                $sitemap = $xml->addChild('sitemap', '');
                $sitemap->addChild('loc', BASE_URL . '/rss/' . $file_name);
//                $sitemap->addChild('lastmod', date('c', time()));
            }
        }

        $result = file_put_contents(PUBLIC_PATH . '/rss/sitemap.xml', $xml->asXML());
        if ($result) {
            echo General::getColoredString("Create sitemap.xml completed!", 'blue', 'cyan');
            $this->flush();
        }
        echo General::getColoredString("DONE!", 'blue', 'cyan');
        return true;
    }

    public function siteMapCategory()
    {
        $doc = '<?xml version="1.0" encoding="UTF-8"?>';
        $doc .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
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

        foreach ($arrCategoryParentList as $value) {
            $strCategoryURL = BASE_URL . '/danh-muc/' . $value['cate_slug'] . '-' . $value['cate_id'] . '.html';
            $url = $xml->addChild('url');
            $url->addChild('loc', $strCategoryURL);
//            $url->addChild('lastmod', date('c', time()));
            $url->addChild('changefreq', 'daily');
//            $url->addChild('priority', 0.9);

            if (!empty($value['cate_img_url'])) {
                $image = $url->addChild('image:image', null, 'http://www.google.com/schemas/sitemap-image/1.1');
                $image->addChild('image:loc', STATIC_URL . $value['cate_img_url'], 'http://www.google.com/schemas/sitemap-image/1.1');
                $image->addChild('image:caption', $value['cate_name'] . General::TITLE_META, 'http://www.google.com/schemas/sitemap-image/1.1');
            }
        }
        foreach ($arrCategoryByParent as $key => $arr) {
            foreach ($arr as $value) {
                $strCategoryURL = BASE_URL . '/danh-muc/' . $value['cate_slug'] . '-' . $value['cate_id'] . '.html';
                $url = $xml->addChild('url');
                $url->addChild('loc', $strCategoryURL);
//                $url->addChild('lastmod', date('c', time()));
                $url->addChild('changefreq', 'daily');
//                $url->addChild('priority', 0.9);
                if (!empty($value['cate_img_url'])) {
                    $image = $url->addChild('image:image', null, 'http://www.google.com/schemas/sitemap-image/1.1');
                    $image->addChild('image:loc', STATIC_URL . $value['cate_img_url'], 'http://www.google.com/schemas/sitemap-image/1.1');
                    $image->addChild('image:caption', $value['cate_name'] . General::TITLE_META, 'http://www.google.com/schemas/sitemap-image/1.1');
                }
            }
        }

        unlink(PUBLIC_PATH . '/rss/category.xml');
        $result = file_put_contents(PUBLIC_PATH . '/rss/category.xml', $xml->asXML());
        if ($result) {
            echo General::getColoredString("Sitemap category done", 'blue', 'cyan');
            $this->flush();
        }

        return true;
    }

    public function siteMapContent()
    {
        $instanceSearchContent = new \My\Search\Content();
        $intLimit = 4000;
        for ($intPage = 1; $intPage < 10000; $intPage++) {

            $file = PUBLIC_PATH . '/rss/content-' . $intPage . '.xml';
            $arrContentList = $instanceSearchContent->getListLimit(['not_cont_status' => -1], $intPage, $intLimit, ['cont_id' => ['order' => 'desc']]);

            if (empty($arrContentList)) {
                break;
            }

            $doc = '<?xml version="1.0" encoding="UTF-8"?>';
            $doc .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
            $doc .= '</urlset>';
            $xml = new \SimpleXMLElement($doc);
            $this->flush();

            foreach ($arrContentList as $arr) {
                $href = BASE_URL . '/bai-viet/' . $arr['cont_slug'] . '-' . $arr['cont_id'] . '.html';
                $url = $xml->addChild('url');
                $url->addChild('loc', $href);
//                $url->addChild('title', $arr['cont_title']);
//                $url->addChild('lastmod', date('c', time()));
                $url->addChild('changefreq', 'daily');
//                $url->addChild('priority', 0.7);

//                if (!empty($arr['cont_main_image'])) {
//                    $image = $url->addChild('image:image', null, 'http://www.google.com/schemas/sitemap-image/1.1');
//                    $image->addChild('image:loc', $arr['cont_main_image'], 'http://www.google.com/schemas/sitemap-image/1.1');
//                    $image->addChild('image:caption', $arr['cont_title'], 'http://www.google.com/schemas/sitemap-image/1.1');
//                }
            }

            unlink($file);
            $result = file_put_contents($file, $xml->asXML());

            if ($result) {
                echo General::getColoredString("Site map complete content page {$intPage}", 'yellow', 'cyan');
                $this->flush();
            }

        }

        return true;
    }

    public function siteMapSearch()
    {
        $instanceSearchKeyword = new \My\Search\Keyword();
        $intLimit = 4000;
        for ($intPage = 1; $intPage < 10000; $intPage++) {
            $file = PUBLIC_PATH . '/rss/keyword-' . $intPage . '.xml';
            $arrKeyList = $instanceSearchKeyword->getListLimit(['full' => 1], $intPage, $intLimit, ['key_id' => ['order' => 'desc']]);

            if (empty($arrKeyList)) {
                break;
            }

            $doc = '<?xml version="1.0" encoding="UTF-8"?>';
            $doc .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
            $doc .= '</urlset>';
            $xml = new \SimpleXMLElement($doc);
            $this->flush();

            foreach ($arrKeyList as $arr) {
                $href = BASE_URL . '/tu-khoa/' . $arr['key_slug'] . '-' . $arr['key_id'] . '.html';
                $url = $xml->addChild('url');
                $url->addChild('loc', $href);
//                $url->addChild('lastmod', date('c', time()));
                $url->addChild('changefreq', 'daily');
//                $url->addChild('priority', 0.7);
            }

            unlink($file);
            $result = file_put_contents($file, $xml->asXML());

            if ($result) {
                echo General::getColoredString("Site map complete keyword page {$intPage}", 'yellow', 'cyan');
                $this->flush();
            }
        }
        return true;
    }

    private function sitemapOther()
    {
        $doc = '<?xml version="1.0" encoding="UTF-8"?>';
        $doc .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
        $doc .= '</urlset>';
        $xml = new \SimpleXMLElement($doc);
        $this->flush();
        $arrData = ['http://khampha.tech/'];
        foreach ($arrData as $value) {
            $href = $value;
            $url = $xml->addChild('url');
            $url->addChild('loc', $href);
//            $url->addChild('lastmod', date('c', time()));
            $url->addChild('changefreq', 'daily');
            $url->addChild('priority', 1);
        }

        unlink(PUBLIC_PATH . '/rss/other.xml');
        $result = file_put_contents(PUBLIC_PATH . '/rss/other.xml', $xml->asXML());
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
            $this->__khoahocTV();
        }

        if ($type == 'khoahocTV') {
            $this->__khoahocTV();
//            shell_exec("nohup php " . PUBLIC_PATH . "/index.php sitemap >/dev/null & echo 2>&1 & echo $!");
            return true;
        }

        //crawler xong thì tạo sitemap
//        shell_exec("nohup php " . PUBLIC_PATH . "/index.php sitemap >/dev/null & echo 2>&1 & echo $!");
        return true;
    }

    public function __khoahocTV()
    {
        $instanceSearchCategory = new \My\Search\Category();
        $arr_category = $instanceSearchCategory->getList(['cate_status' => 1], [], ['cate_sort' => ['order' => 'asc'], 'cate_id' => ['order' => 'asc']]);
        unset($instanceSearchCategory);
        $instanceSearchContent = new \My\Search\Content();

        $arr_pass = [
            'http://khoahoc.tv/chua-du-co-so-de-xac-dinh-nien-dai-thoc-thanh-den-29433',
            'http://khoahoc.tv/phat-hien-dia-bay-kim-loai-bay-gan-trai-dat-69779'
        ];

        $arr_pass_cate = [
            'http://khoahoc.tv/yhoc?p=223'
        ];
        foreach ($arr_category as $category) {
            try {
                if (empty($category['cate_crawler_url'])) {
                    continue;
                }
                for ($i = 1; $i >= 1; $i--) {
                    $source_url = $category['cate_crawler_url'] . '?p=' . $i;

                    if (in_array($source_url, $arr_pass_cate)) {
                        echo \My\General::getColoredString("Continue page cate = {$source_url} \n", 'red');
                        continue;
                    }

                    echo \My\General::getColoredString("Crawler page cate = {$source_url} \n", 'green');

                    $page_cate_content = General::crawler($source_url);
                    $page_cate_dom = HtmlDomParser::str_get_html($page_cate_content);

                    try {
                        $item_content_in_cate = $page_cate_dom->find('.listitem');
                    } catch (\Exception $exc) {
                        echo \My\General::getColoredString("Exception url = {$source_url} \n", 'red');
                        continue;
                    }

                    if (empty($item_content_in_cate)) {
                        continue;
                    }

                    foreach ($item_content_in_cate as $item_content) {
                        $arr_data_content = [];
                        $item_content_dom = HtmlDomParser::str_get_html($item_content->outertext);

                        try {
                            $item_content_source = 'http://khoahoc.tv' . $item_content_dom->find('a', 0)->href;
                        } catch (\Exception $exc) {
                            echo \My\General::getColoredString("Exception item cate url = {$source_url} \n", 'red');
                            continue;
                        }

                        if (in_array($item_content_source, $arr_pass)) {
                            echo \My\General::getColoredString("Pass url = {$item_content_source} \n", 'red');
                            continue;
                        }


                        echo \My\General::getColoredString("get url = {$item_content_source} \n", 'green');

                        try {
                            $item_content_title = trim($item_content_dom->find('.title', 0)->plaintext);
                        } catch (\Exception $exc) {
                            echo \My\General::getColoredString("Exception cannot get title url = {$item_content_source} \n", 'red');
                            continue;
                        }

                        $arr_data_content['cont_title'] = html_entity_decode($item_content_title);
                        $arr_data_content['cont_slug'] = General::getSlug(html_entity_decode($item_content_title));

                        try {
                            $item_content_description = html_entity_decode(trim($item_content_dom->find('.desc', 0)->plaintext));
                        } catch (\Exception $exc) {
                            echo \My\General::getColoredString("Exception cannot get description", 'red');
//                            continue;
                        }

                        try {
                            $img_avatar_url = $item_content_dom->find('img', 0)->src;
                        } catch (\Exception $exc) {
                            echo \My\General::getColoredString("Exception image title = {$item_content_title} \n", 'red');
//                            continue;
                        }

                        $arr_detail = $instanceSearchContent->getDetail(['cont_slug' => $arr_data_content['cont_slug'], 'not_cont_status' => -1]);

                        if (!empty($arr_detail)) {
                            continue;
                        }

                        //lấy hình đại diện
                        if (empty($img_avatar_url) || $img_avatar_url == 'http://img.khoahoc.tv/photos/image/blank.png') {
                            $arr_data_content['cont_main_image'] = STATIC_URL . '/f/v1/img/black.png';
                        } else {
                            $extension = end(explode('.', end(explode('/', $img_avatar_url))));
                            $name = $arr_data_content['cont_slug'] . '.' . $extension;
                            file_put_contents(STATIC_PATH . '/uploads/content/' . $name, General::crawler($img_avatar_url));
                            $arr_data_content['cont_main_image'] = STATIC_URL . '/uploads/content/' . $name;
                        }

                        //crawler nội dung bài đọc
                        $content_detail_page_dom = HtmlDomParser::str_get_html(General::crawler($item_content_source));

                        try {
                            $script = $content_detail_page_dom->find('script');
                        } catch (\Exception $exc) {
                            echo $exc->getMessage();
                            $script = null;
                            echo \My\General::getColoredString("Empty Script", 'red');
                        }
                        if (!empty($script)) {
                            foreach ($content_detail_page_dom->find('script') as $item) {
                                $item->outertext = '';
                            }
                            unset($script);
                        }

                        try {
                            $adbox = $content_detail_page_dom->find('.adbox');
                        } catch (\Exception $exc) {
                            $adbox = null;
                            echo \My\General::getColoredString("Empty adbox", 'red');
                        }

                        if (!empty($adbox)) {
                            foreach ($content_detail_page_dom->find('.adbox') as $item) {
                                $item->outertext = '';
                            }
                            unset($adbox);
                        }

                        try {
                            $content_detail_html = $content_detail_page_dom->find('.content-detail', 0);
                        } catch (\Exception $exc) {
                            echo \My\General::getColoredString("Empty .adbox", 'red');
                            continue;
                        }

                        try {
                            $content_detail_outertext = $content_detail_page_dom->find('.content-detail', 0)->outertext;
                        } catch (\Exception $exc) {
                            echo \My\General::getColoredString("Empty content-detail", 'red');
                            continue;
                        }

                        try {
                            $img_all = $content_detail_html->find("img");
                        } catch (\Exception $exc) {
                            $img_all = [];
                            echo \My\General::getColoredString("Empty images", 'red');
//                            continue;
                        }

                        //lấy hình ảnh trong bài
                        if (count($img_all) > 0) {
                            foreach ($img_all as $key => $im) {
                                $extension = end(explode('.', end(explode('/', $im->src))));
                                $name = $arr_data_content['cont_slug'] . '-' . ($key + 1) . '.' . $extension;
                                file_put_contents(STATIC_PATH . '/uploads/content/' . $name, General::crawler($im->src));
                                $content_detail_outertext = str_replace($im->src, STATIC_URL . '/uploads/content/' . $name, $content_detail_outertext);
                            }
                        }

                        //REPLACE ALL HREF TAG  A
                        $content_detail_outertext = str_replace('http://khoahoc.tv', BASE_URL, $content_detail_outertext);
                        $content_detail_outertext = str_replace('khoahoc.tv', 'khampha.tech', $content_detail_outertext);

                        $content_detail_outertext = trim(strip_tags($content_detail_outertext, '<a><div><img><b><p><br><span><br /><strong><h2><h1><h3><h4><table><td><tr><th><tbody><iframe>'));
                        $arr_data_content['cont_detail'] = html_entity_decode($content_detail_outertext);
                        $arr_data_content['created_date'] = time();
                        $arr_data_content['user_created'] = 1;
                        $arr_data_content['cate_id'] = $category['cate_id'];
                        $arr_data_content['cont_description'] = $item_content_description;
                        $arr_data_content['cont_status'] = 1;
                        $arr_data_content['cont_views'] = rand(1, rand(100, 1000));
                        $arr_data_content['method'] = 'crawler';
                        $arr_data_content['from_source'] = $item_content_source;
                        $arr_data_content['meta_keyword'] = str_replace(' ', ',', $arr_data_content['cont_title']);
                        $arr_data_content['updated_date'] = time();
                        unset($content_detail_outertext);
                        unset($img_all);
                        unset($img_avatar_url);
                        unset($content_detail_html);
                        unset($content_detail_page_dom);
                        unset($item_content_dom);

                        $serviceContent = $this->serviceLocator->get('My\Models\Content');
                        $id = $serviceContent->add($arr_data_content);

                        if ($id) {
                            $arr_data_content['cont_id'] = $id;
                            $this->postToFb($arr_data_content);
                            echo \My\General::getColoredString("Crawler success 1 post id = {$id} \n", 'green');
                        } else {
                            echo \My\General::getColoredString("Can not insert content db", 'red');
                        }

                        unset($serviceContent);
                        unset($arr_data_content);
                        $this->flush();
                        continue;
                    }
                }
            } catch (\Exception $exc) {
                continue;
            }

        }
        echo \My\General::getColoredString("Crawler to success", 'green');
        return true;
    }

    public function postToFb($arrParams)
    {
        $config_fb = General::$config_fb;
        $url_content = 'http://khampha.tech/bai-viet/' . $arrParams['cont_slug'] . '-' . $arrParams['cont_id'] . '.html';
        $data = array(
            "access_token" => $config_fb['access_token'],
            "message" => $arrParams['cont_description'],
            "link" => $url_content,
            "picture" => $arrParams['cont_main_image'],
            "name" => $arrParams['cont_title'],
            "caption" => "khampha.tech",
            "description" => $arrParams['cont_description']
        );
        $post_url = 'https://graph.facebook.com/' . $config_fb['fb_id'] . '/feed';

        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $post_url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $return = curl_exec($ch);
            curl_close($ch);
            echo \My\General::getColoredString($return, 'green');
            unset($ch);
            if (!empty($return)) {
                $post_id = explode('_', json_decode($return, true)['id'])[1];
                foreach (General::$face_traffic as $key => $value) {
                    $this->shareFb([
                        'post_id' => $post_id,
                        'access_token' => $value,
                        'name' => $key
                    ]);
                }
            }
            echo \My\General::getColoredString("Post 1 content to facebook success cont_id = {$arrParams['cont_id']}", 'green');
            unset($ch, $return, $post_id, $data, $post_url, $url_content, $config_fb, $arrParams);
            $this->flush();
            return true;
        } catch (Exception $e) {
            echo \My\General::getColoredString($e->getMessage(), 'red');
            return true;
        }
    }

    public function shareFb($arrParams)
    {
        $config_fb = General::$config_fb;
        try {
            $fb = new \Facebook\Facebook([
                'app_id' => $config_fb['appId'],
                'app_secret' => $config_fb['secret']
            ]);
            $fb->setDefaultAccessToken($arrParams['access_token']);
            $rp = $fb->post('/me/feed', ['link' => 'https://web.facebook.com/khampha.tech/posts/' . $arrParams['post_id']]);
            echo \My\General::getColoredString(json_decode($rp->getBody(), true), 'green');
            echo \My\General::getColoredString('Share post id ' . $arrParams['post_id'] . ' to facebook ' . $arrParams['name'] . ' SUCCESS', 'green');
            unset($data, $return, $arrParams, $rp, $config_fb);
            return true;
        } catch (\Exception $exc) {
            echo \My\General::getColoredString($exc->getMessage(), 'red');
            echo \My\General::getColoredString('Share post id ' . $arrParams['post_id'] . ' to facebook ' . $arrParams['name'] . ' ERROR', 'red');
            return true;
        }

    }

    public function testAction()
    {
        $current_date = '2016-03-01';
        for ($i = 0; $i <= 10000; $i++) {
            $date = strtotime('-' . $i . ' day', strtotime($current_date));
            $date = date('Ymd', $date);
            $href = 'https://www.google.com/trends/hottrends/hotItems?ajax=1&pn=p28&htd=' . $date . '&htv=l';
            $responseCurl = General::crawler($href);
            $arrData = json_decode($responseCurl, true);
            foreach ($arrData['trendsByDateList'] as $data) {

                foreach ($data['trendsList'] as $data1) {
                    echo '<pre>';
                    print_r($data1['title']);
                    echo '</pre>';
                    die();
                    foreach ($data1 as $data2) {
                        echo '<pre>';
                        print_r($data2);
                        echo '</pre>';
                        die();
                    }
                    echo '<pre>';
                    print_r($data2);
                    echo '</pre>';
                    die();
                }
                echo '<pre>';
                print_r($data['trendsList']);
                echo '</pre>';
                die();
            }
            echo '<pre>';
            print_r($arrData['trendsByDateList']);
            echo '</pre>';
            die();
            foreach ($arrData as $value) {
                echo '<pre>';
                print_r($value['trendsByDateList']);
                echo '</pre>';
                die();
            }
            echo '<pre>';
            print_r($arrData);
            echo '</pre>';
            die();

        }
        return;
        $instanceSearchKeyWord = new \My\Search\Keyword();
        $file = PUBLIC_PATH . '/migrate/keyword.txt';
        $arrList = explode(',', file_get_contents($file));
        foreach ($arrList as $name) {
            //find in DB có tồn tại hay ko?
            $is_exits = $instanceSearchKeyWord->getDetail(['key_slug' => trim(General::getSlug($name))]);

            if ($is_exits) {
                continue;
            }

            $arr_data = [
                'key_name' => $name,
                'key_slug' => trim(General::getSlug($name)),
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

        unset($instanceSearchKeyWord, $arrList);
        echo \My\General::getColoredString("DONE", 'yellow');
        $this->flush();
        return true;


        for ($intPage = 1; $intPage < 10000; $intPage++) {
            echo \My\General::getColoredString("page : {$intPage}\n", 'yellow');
            $arrList = $instanceSearchKeyWord->getListLimit(['full' => 1], $intPage, $intLimit, ['key_id' => ['order' => 'asc']]);

            if (empty($arrList)) {
                break;
            }

            foreach ($arrList as $arr) {
                file_put_contents($file, $arr['key_name'] . ',', FILE_APPEND);
            }
            unset($instanceSearchKeyWord);
            unset($arrList); //release memory
            $this->flush();
        }
        unset($instanceSearchKeyWord);
        echo \My\General::getColoredString("DONE", 'yellow');
        $this->flush();
        return true;


        $file = PUBLIC_PATH . '/tl/keyword.txt';

        $intLimit = 2000;
        for ($intPage = 1; $intPage < 10000; $intPage++) {
            $instanceSearchKeyWord = new \My\Search\Keyword();
            echo \My\General::getColoredString("page : {$intPage}\n", 'yellow');
            $arrList = $instanceSearchKeyWord->getListLimit(['full' => 1], $intPage, $intLimit, ['key_id' => ['order' => 'asc']]);

            if (empty($arrList)) {
                break;
            }

            foreach ($arrList as $arr) {
                file_put_contents($file, $arr['key_name'] . ',', FILE_APPEND);
            }
            unset($instanceSearchKeyWord);
            unset($arrList); //release memory
            $this->flush();
        }
        unset($instanceSearchKeyWord);
        echo \My\General::getColoredString("DONE", 'yellow');
        $this->flush();
        return true;


        $instanceSearchContent = new \My\Search\Content();
        $arr_content = $instanceSearchContent->getDetail([
            'cont_id' => 59339
        ]);
        $this->postToFb($arr_content);
        return;
    }

    public function initKeywordOldAction()
    {
        $instanceSearchKeyWord = new \My\Search\Keyword();
        $instanceSearchKeyWordOld = new \My\Search\KeywordOld();

        $intLimit = 2000;
        for ($intPage = 1; $intPage < 10000; $intPage++) {
            $arrList = $instanceSearchKeyWordOld->getListLimit(['full' => 1], $intPage, $intLimit, ['key_id' => ['order' => 'asc']]);

            if (empty($arrList)) {
                break;
            }

            foreach ($arrList as $arr) {
                //find in DB có tồn tại hay ko?
                $is_exits = $instanceSearchKeyWord->getDetail(['key_slug' => trim(General::getSlug($arr['key_name']))]);

                if ($is_exits) {
                    continue;
                }

                $arr_data = [
                    'key_name' => $arr['key_name'],
                    'key_slug' => trim(General::getSlug($arr['key_name'])),
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

            unset($arrList); //release memory
            $this->flush();
        }

        $instanceSearchKeyWord = new \My\Search\Keyword();
        $instanceSearchKeyWordOld = new \My\Search\KeywordOld();
        unset($instanceSearchKeyWord);
        unset($instanceSearchKeyWordOld);

        return true;
    }


    public function keywordHotAction()
    {
        $arr_key = [
            'phụ nữ',
            'phái đẹp',
            'nam giới',
            'phai mạnh',
            'phim hay',
            'ca nhạc',
            'nghệ sỹ',
            'xe máy',
            'ô tô',
            'soai ca',
            'kpop',
            'show biz',
            'thơ văn',
            'ngôn tình',
            'viễn tưởng',
            'du lịch',
            'văn hoá',
            'trào lưu',
            'tha thu',
            'ném đá',
            'hài vl',
            'voz',
            'ngáo đá',
            'phồng tôm',
            'cdsht',
            'tuổi trẻ',
            'yêu đời',
            'tình yêu',
            'sống thử',
            'chịch',
            'ngôn lù',
            'minh hằng',
            'lý hải',
            'ca sỹ',
            'hải ngoại',
            'quang lê',
            'hoài linh',
            'trường giang',
            'trấn thành',
            'cười',
            'chúng ta không thuộc về nhau',
            'sơn tùng',
            'sky',
            'đạo nhạc',
            'khởi my',
            'chém gió',
            'hôi của',
            'sống ảo',
            'co thủ',
            'kiếm hiệp',
            'kim dung',
            'phim',
            'chưởng',
            'tiêu thuyết',
            'vỡ mồm',
            'yêu là cưới',
            'yêu',
            'deploy',
            'pro',
            'campaign',
            'bóng đá',
            'đau bụng',
            '5 sao',
            'thất thủ',
            'sài gòn',
            'An Giang',
            'Bà Rịa - Vũng Tàu',
            'Bắc Giang',
            'Bắc Kạn',
            'Bạc Liêu',
            'Bắc Ninh',
            'Bến Tre',
            'Bình Định',
            'Bình Dương',
            'Bình Phước',
            'Bình Thuận',
            'Cà Mau',
            'Cao Bằng',
            'Đắk Lắk',
            'Đắk Nông',
            'Điện Biên',
            'Đồng Nai',
            'Đồng Tháp',
            'Gia Lai',
            'Hà Giang',
            'Hà Nam',
            'Hà Tĩnh',
            'Hải Dương',
            'Hậu Giang',
            'Hòa Bình',
            'Hưng Yên',
            'Khánh Hòa',
            'Kiên Giang',
            'Kon Tum',
            'Lai Châu',
            'Lâm Đồng',
            'Lạng Sơn',
            'Lào Cai',
            'Long An',
            'Nam Định',
            'Nghệ An',
            'Ninh Bình',
            'Ninh Thuận',
            'Phú Thọ',
            'Quảng Bình',
            'Quảng Nam',
            'Quảng Ngãi',
            'Quảng Ninh',
            'Quảng Trị',
            'Sóc Trăng',
            'Sơn La',
            'Tây Ninh',
            'Thái Bình',
            'Thái Nguyên',
            'Thanh Hóa',
            'Thừa Thiên Huế',
            'Tiền Giang',
            'Trà Vinh',
            'Tuyên Quang',
            'Vĩnh Long',
            'Vĩnh Phúc',
            'Yên Bái',
            'Phú Yên',
            'Cần Thơ',
            'Đà Nẵng',
            'Hải Phòng',
            'Hà Nội',
            'TP HCM',
            'miền trung',
            'miền nam',
            'miền bắc',
            'tỉnh thành',
            'việc làm',
            'nhân sự',
            'tuyển dụng',
            'ý tưởng',
            'kinh doanh',
            'khởi nghiệp',
            'nữ sinh',
            'nam sinh',
            'hot girl',
            'Vợ Người Ta',
            'Âm Thầm Bên Em',
            'Không Phải Dạng Vừa Đâu',
            'How Old net',
            'Furious 7',
            'Khuôn Mặt Đáng Thương',
            'Em Của Quá Khứ',
            'Cười Xuyên Việt',
            'Cô Dâu 8 Tuổi',
            'Chàng Trai Năm Ấy',
            'Maldives',
            'IS',
            'Paris',
            'Lý Quang Diệu',
            'Pluto',
            'Inge Lehmann',
            'Nga không kích IS',
            'Tình hình Syria',
            'Tin Ukraina',
            'Nepal',
            'Điểm thi đại học quốc gia 2015',
            'Tai nạn giao thông mới nhất',
            'Giá xăng hôm nay',
            'Tin bão số 1',
            'Hang Sơn Đoòng',
            'Đứt cáp quang',
            'Tin bão mới nhất',
            'Tân Hiệp Phát',
            'Tin pháp luật mới nhất',
            'Giá iPhone',
            'Duy Nhân',
            'DJ Trang Moon',
            'Midu',
            'Paul Walker',
            'DJ Soda',
            'MC Quang Minh',
            'Ánh Viên',
            'Kang Tae Oh',
            'Thanh Duy',
            'Angelababy',
            'Cách làm mứt dừa',
            'Cách làm kem chuối',
            'Cách làm mứt cà rốt',
            'Mâm ngũ quả ngày Tết',
            'Cách làm mứt bí',
            'Cách làm mứt khoai lang',
            'Cách làm mứt cam',
            'Cách gói bánh chưng',
            'Cách làm dưa món',
            'Cách làm mứt khoai tây',
            'Vợ Người Ta',
            'Âm Thầm Bên Em',
            'Không Phải Dạng Vừa Đâu',
            'Khuôn Mặt Đáng Thương',
            'Em Của Quá Khứ',
            'Say You Do',
            'Chắc Ai Đó Sẽ Về',
            'Em Là Của Anh',
            'Thất Tình',
            'Có Không Giữ Mất Đừng Tìm',
            'Cười Xuyên Việt',
            'Táo Quân',
            'Thách Thức Danh Hài',
            'Gương Mặt Thân Quen',
            'Ơn Giời Cậu Đây Rồi',
            'Bí Mật Đêm Chủ Nhật',
            'Giọng Hát Việt Nhí',
            'Giọng Hát Việt',
            'Ca Sĩ Giấu Mặt',
            'Người Bí Ẩn',
            'Furious 7',
            'Cô Dâu 8 Tuổi',
            'Chàng Trai Năm ấy',
            'School',
            'Võ Tắc Thiên',
            'Tôi Thấy Hoa Vàng Trên Cỏ Xanh',
            'Hoa Thiên Cốt',
            'Bên Nhau Trọn Đời',
            '50 Sắc Thái',
            'She Was Pretty',
            'Copa America',
            'SEA Games',
            'Hoa hậu Hoàn vũ Việt Nam',
            'Wimbledon',
            'Hoa hậu Việt Nam',
            'Zing Music Awards',
            'Australian Open',
            'US Open',
            'Giờ Trái đất',
            'Đêm hội chân dài',
            'TPP là gì',
            'IS là gì',
            'Deep web là gì',
            'IPU là gì',
            'Thứ 6 ngày 13 là gì',
            'Pray for Paris là gì',
            'Tiamo là gì',
            'Phơi nhiễm HIV là gì',
            'Dub là gì',
            'Marimo là gì'
        ];

//        $instanceSearchKeyWord = new \My\Search\Keyword();
//        //remove all doc
////        $rm = $instanceSearchKeyWord->removeAllDoc();
////        echo '<pre>';
////        print_r($rm);
////        echo '</pre>';
////        die();
//
////        $arr_keyword = $instanceSearchKeyWord->getList(['is_crawler' => 1, 'key_id_greater' => 1077764]);
//        foreach ($arr_key as $arr_key) {
//            $serviceKeyword = $this->serviceLocator->get('My\Models\Keyword');
//            $serviceKeyword->edit(['is_crawler' => 0], $arr_key['key_id']);
//            unset($serviceKeyword);
//        }
//        echo General::getColoredString("update all keyword complete", 'yellow', 'cyan');
//        return true;

        $instanceSearchKeyWord = new \My\Search\Keyword();
        foreach ($arr_key as $name) {
            $isexist = $instanceSearchKeyWord->getDetail(['key_slug' => General::getSlug($name)]);

            if ($isexist) {
                continue;
            }
            $arr_data = [
                'key_name' => $name,
                'key_slug' => trim(General::getSlug($name)),
                'created_date' => time(),
                'is_crawler' => 0
            ];
            $serviceKeyword = $this->serviceLocator->get('My\Models\Keyword');
            $int_result = $serviceKeyword->add($arr_data);
            unset($serviceKeyword);
            if ($int_result) {
                echo General::getColoredString("add keyword : {$name} success id : {$int_result} ", 'green');
            } else {
                echo General::getColoredString("add keyword : {$name} error", 'red');
            }
            $this->flush();
        }
        echo General::getColoredString("add keyword complete", 'yellow', 'cyan');
        return true;
    }

    public function hotTrendAction()
    {
        $current_date = date('Y-m-d');
        $instanceSearchKeyWord = new \My\Search\Keyword();
        for ($i = 0; $i <= 1000; $i++) {
            $date = strtotime('-' . $i . ' days', strtotime($current_date));
            $date = date('Ymd', $date);
            echo \My\General::getColoredString("Date = {$date}", 'cyan');
            $href = 'https://www.google.com/trends/hottrends/hotItems?ajax=1&pn=p28&htd=' . $date . '&htv=l';
            $responseCurl = General::crawler($href);
            $arrData = json_decode($responseCurl, true);

//            if($i == 0){
//
//                continue;
//            }
//            print_r($arrData);
//            die();
            foreach ($arrData['trendsByDateList'] as $data) {
                foreach ($data['trendsList'] as $data1) {
                    $arr_key[] = $data1['title'];
                    if (!empty($data1['relatedSearchesList'])) {
                        $arr_key = array_merge($arr_key, $data1['relatedSearchesList']);
                    }
                    $strDescription = '';
                    if (!empty($data1['newsArticlesList'])) {
                        foreach ($data1['newsArticlesList'] as $val) {
                            $strDescription .= $val['snippet'] . ',';
                        }

                    }

                    foreach ($arr_key as $val) {
                        $is_exits = $instanceSearchKeyWord->getDetail(['key_slug' => trim(General::getSlug($val))]);

                        if ($is_exits) {
                            echo \My\General::getColoredString("exist {$val}", 'red') . '<br/>';
                            continue;
                        }

                        $arr_data = [
                            'key_name' => $val,
                            'key_slug' => General::getSlug($val),
                            'is_crawler' => 0,
                            'created_date' => time()
                        ];

                        $serviceKeyword = $this->serviceLocator->get('My\Models\Keyword');
                        $int_result = $serviceKeyword->add($arr_data);
                        unset($serviceKeyword);
                        if ($int_result) {
                            echo \My\General::getColoredString("Insert success 1 row with id = {$int_result} key name = {$arr_data['key_slug']}", 'yellow');
                        }
                        $this->flush();
                    }
                    $this->flush();
                }
                $this->flush();
            }
            $this->flush();
        }
        die('eee');
    }

    public function videosYoutubeAction()
    {
        $instanceSearchContent = new \My\Search\Content();
        $google_config = General::$google_config;
        $client = new \Google_Client();
        $client->setDeveloperKey($google_config['key']);

        // Define an object that will be used to make all API requests.
        $youtube = new \Google_Service_YouTube($client);
        try {
//            https://www.googleapis.com/youtube/v3/videos?part=contentDetails&chart=mostPopular&regionCode=IN&maxResults=25&key=API_KEY
            //get channel of user
//            $searchResponse = $youtube->channels->listChannels(
//                'snippet', array(
//                    'forUsername' => 'HongAnEntertainment',
//                    'maxResults' => 50
//                )
//            );

            $arr_channel = [
                '28' => [ //Videos Hài Hước
                    'UCwmurIyZ6FHyVPtKppe_2_A', //https://www.youtube.com/channel/UCwmurIyZ6FHyVPtKppe_2_A/videos -- DIEN QUAN Comedy / Hài
                    'UCFMEYTv6N64hIL9FlQ_hxBw', //https://www.youtube.com/channel/UCFMEYTv6N64hIL9FlQ_hxBw -- ĐÔNG TÂY PROMOTION OFFICIAL
                    'UCXarSb1YYXKAtcPQJECVH2Q', //https://www.youtube.com/channel/UCXarSb1YYXKAtcPQJECVH2Q -- Fan Nhã Phương Trường Giang
                    'UCruaM4824Rr_ry7fsD5Jwag', //https://www.youtube.com/channel/UCruaM4824Rr_ry7fsD5Jwag -- THVL Giải Trí
                    'UCQGd-eIAxQV7zMvTT4UmjZA', //https://www.youtube.com/channel/UCQGd-eIAxQV7zMvTT4UmjZA --Bánh Bao Bự
                    'UCPu7cX9LrVOlCDK905T9tVw', //https://www.youtube.com/channel/UCPu7cX9LrVOlCDK905T9tVw -- Kem Xôi TV
                    'UCq6ApdQI0roaprMAY1gZTgw', //https://www.youtube.com/channel/UCq6ApdQI0roaprMAY1gZTgw -- Ghiền Mì Gõ
                    'UCZ72vrOkYZmvs9c0XYQM8cA', //https://www.youtube.com/channel/UCZ72vrOkYZmvs9c0XYQM8cA -- TRƯỜNG GIANG FAN
                    'UC0jDoh3tVXCaqJ6oTve8ebA', //https://www.youtube.com/channel/UC0jDoh3tVXCaqJ6oTve8ebA -- FAP TV
                    'UCsluIbpgt14y6KUcwqCxXbg', //https://www.youtube.com/channel/UCsluIbpgt14y6KUcwqCxXbg -- MCVMedia
                    'UCp-yY0F1wgZ1CUnh3upZLBQ', //https://www.youtube.com/channel/UCp-yY0F1wgZ1CUnh3upZLBQ -- Trắng
                    'UC6K3k5O0Dogk1v00beoGMTw', //https://www.youtube.com/channel/UC6K3k5O0Dogk1v00beoGMTw -- POPSTVVIETNAM
                    'UCmTroavJBDcWhwptGyKkERA' //https://www.youtube.com/channel/UCmTroavJBDcWhwptGyKkERA -- PHIM CẤP 3
                ],
                '29' => [ //videos Trẻ Thơ
                    'UCBzZ9lJmcsPRbiyHPbLYvOA', //Elsa and Spiderman Compilations
                    'UC_lA07JiUMe-aNh-u-TxjHg', // https://www.youtube.com/channel/UC_lA07JiUMe-aNh-u-TxjHg -- TuLi TV
                    'UCMYCx114VbdhQtU_Tz9dndA', //https://www.youtube.com/channel/UCMYCx114VbdhQtU_Tz9dndA -- Come And Play
                    'UCAeYn3nppkt7469wYynZJCg', //https://www.youtube.com/channel/UCAeYn3nppkt7469wYynZJCg -- Spiderman Frozen Elsa & Friends
                    'UC0jJKKA_CD4q4QE5UBMPMrA', //https://www.youtube.com/channel/UC0jJKKA_CD4q4QE5UBMPMrA --ElsaSpidermanIRL
                    'UCIl8mU7DwcDZVgsVPbQ5U8A', //https://www.youtube.com/channel/UCIl8mU7DwcDZVgsVPbQ5U8A -- AnAn ToysReview TV
                    'UCXF15hEQI7y1hpoZK4cZDEQ', //https://www.youtube.com/channel/UCXF15hEQI7y1hpoZK4cZDEQ -- KN Channel
                    'UCKcQ7Jo2VAGHiPMfDwzeRUw', //https://www.youtube.com/channel/UCKcQ7Jo2VAGHiPMfDwzeRUw --ChuChuTV Surprise Eggs Toys
                    'UCCF-1DYSb2deBcsdypD4b-Q', //https://www.youtube.com/channel/UCCF-1DYSb2deBcsdypD4b-Q -- Spiderman Real Life Superhero
                    'UCSJsjCiTl2lourZXnigVCoA', //https://www.youtube.com/channel/UCSJsjCiTl2lourZXnigVCoA -- Thơ Nguyễn
                    'UC5ezaYrzZpyItPSRG27MLpg', //https://www.youtube.com/channel/UC5ezaYrzZpyItPSRG27MLpg -- POPS Kids
                    'UCm4tFSgINIb4yGty_R2by_Q' //https://www.youtube.com/channel/UCm4tFSgINIb4yGty_R2by_Q --- Bi Bi Bi
                ],
                '30' => [ //Videos Khoa Học - Khám Phá
                    'UCiZJtnTQunvoY0xgv-Ce_rg',
                    'UCrZU-Qjgjs75BvKrLIX5pXg', //https://www.youtube.com/channel/UCrZU-Qjgjs75BvKrLIX5pXg -- Làm Gì Đây
                    'UCVC7AzvC0r1iruRyOL5X3JA', //https://www.youtube.com/channel/UCVC7AzvC0r1iruRyOL5X3JA -- Bí Ẩn Lạ Kỳ
                    'UCIBhtklwSSnghOTi4AAtj-g', // https://www.youtube.com/channel/UCIBhtklwSSnghOTi4AAtj-g --CỖ MÁY
                    'UCoOxtz4PmB7AOP3nwPM_bEg', //https://www.youtube.com/channel/UCoOxtz4PmB7AOP3nwPM_bEg -- Săn bắt và hái lượm
                    'UChp7WB42sPXbFtr3e_g9CaA', //https://www.youtube.com/channel/UChp7WB42sPXbFtr3e_g9CaA -- Thế Giới Huyền Bí
                    'UCM4srUfoYLk0n21HqAa5bCA', //https://www.youtube.com/channel/UCM4srUfoYLk0n21HqAa5bCA -- Lê Thành Chung
                    'UC1a6kSwY-zOLpFqxMLImpZw' //https://www.youtube.com/channel/UC1a6kSwY-zOLpFqxMLImpZw -- Thiên nhiên kỳ thú

                ],
                '31' => [ //Videos Hot
                    'UCuSp9h4f73lXjiBE2J85i8g', //https://www.youtube.com/channel/UCuSp9h4f73lXjiBE2J85i8g -- Tin Nóng Trong Ngày
                    'UCID3GTmfIFmydRiCdH_Fjow', //https://www.youtube.com/channel/UCID3GTmfIFmydRiCdH_Fjow --Tổng Hợp
                    'UC92MnB1eOC47kaMPoHckBhA', //https://www.youtube.com/channel/UC92MnB1eOC47kaMPoHckBhA -- Tieu Phong
                    'UCrszDMN3snNmbqu8XHuOmIg', //https://www.youtube.com/channel/UCrszDMN3snNmbqu8XHuOmIg -- Tin Hot 19+
//                    'UCedhIhWwTYnoPvR-o62Oa8g' //https://www.youtube.com/channel/UCedhIhWwTYnoPvR-o62Oa8g -- VẹmTV News

                ],
                '32' => [ // Videos Ca Nhạc
                    'UCZq4u4hadohQDXO6ra8aubQ', //https://www.youtube.com/channel/UCZq4u4hadohQDXO6ra8aubQ -- VIVA Music
                    'UCF5RuEuoGrqGtscvLGLOMew', //https://www.youtube.com/channel/UCF5RuEuoGrqGtscvLGLOMew -- VIVA Shows
                    'UCUgXK2UjZ8G_EM438aYkGrw', //https://www.youtube.com/channel/UCUgXK2UjZ8G_EM438aYkGrw -POPS MUSIC
                    'UCFtat3KL0Z29ATKiYVrnBxw', //https://www.youtube.com/channel/UCFtat3KL0Z29ATKiYVrnBxw -- Hồng Ân Entertainment
                    'UCPtEZo-8wDgZlXIRg1jOoJA' //https://www.youtube.com/channel/UCPtEZo-8wDgZlXIRg1jOoJA -- MP3 Zing Official
                ],
                '33' => [ //Videos Phim
                    'UC5rqnUOQ6tm915MjlwO4G0g', //--Nam Việt TV
                    'UC_VPydo78uJetT1OYk4xbpw', //https://www.youtube.com/channel/UC_VPydo78uJetT1OYk4xbpw/playlists -- Hoàng Dương AOK
                    'UCOjHXJD_ZJbh8nsJIuNcxHw', // https://www.youtube.com/channel/UCOjHXJD_ZJbh8nsJIuNcxHw -- Phim Sắp Ra
                    'UCGk3yw5k_xQUS_KSDCC6Nhw', //https://www.youtube.com/channel/UCGk3yw5k_xQUS_KSDCC6Nhw/videos -- VTV - SUNRISE
                    'UCF3TM1yxDMdFm2p3h11j52Q' //https://www.youtube.com/channel/UCF3TM1yxDMdFm2p3h11j52Q -- Khoảnh khắc kỳ diệu
                ],
                '34' => [ //Videos Thể Thao
                    'UCmHs51bYwsNGMMDH6oMoF_A', //https://www.youtube.com/channel/UCmHs51bYwsNGMMDH6oMoF_A -- Football VN
                    'UCndcERoL9eG-XNljgUk1Gag', //https://www.youtube.com/channel/UCndcERoL9eG-XNljgUk1Gag -- VFF Channel
                    'UCXVmFKJdknhsl-Co6gUAhOA', //https://www.youtube.com/channel/UCXVmFKJdknhsl-Co6gUAhOA -- Captain Football VN
                    'UCZNoTFTsrWXA-dXElRm90bA' //https://www.youtube.com/channel/UCZNoTFTsrWXA-dXElRm90bA -- Hài Bóng Đá
                ],
//                '27' => [//gamming
//                    'UU2l8G7UE41Vaby59Dfg6r3w' //gamming
//                ]
            ];
            foreach ($arr_channel as $cate_id => $channels) {
                foreach ($channels as $channel_id) {
                    $token_page = null;
                    for ($i = 0; $i <= 1000; $i++) {
                        echo \My\General::getColoredString("Start channel Id = {$channel_id} page {$i} , token page {$token_page} \n", 'green');
                        if ($i == 0) {
                            $searchResponse = $youtube->search->listSearch(
                                'snippet', array(
                                    'channelId' => $channel_id,
                                    'maxResults' => 50
                                )
                            );
                        } else {
                            if (empty($token_page)) {
                                break;
                            }
                            $searchResponse = $youtube->search->listSearch(
                                'snippet', array(
                                    'channelId' => $channel_id,
                                    'maxResults' => 50,
                                    'pageToken' => $token_page
                                )
                            );
//                                $youtube->playlistItems->listPlaylistItems('snippet', array(
//                                'playlistId' => $channel_id,
//                                'maxResults' => 50,
//                                'pageToken' => $token_page
//                            ));
                        }

                        if (empty($searchResponse) || empty($searchResponse->getItems())) {
                            break;
                        }

                        $token_page = $searchResponse->getNextPageToken();

                        foreach ($searchResponse->getItems() as $key => $item) {
                            try {
                                $id = $item->getId()->getVideoId();
                                if (empty($id)) {
                                    continue;
                                }
                                $title = $item->getSnippet()->getTitle();
                                $description = $item->getSnippet()->getDescription();

                                if (empty($item->getSnippet()->getThumbnails())) {
                                    continue;
                                }

                                $main_image = $item->getSnippet()->getThumbnails()->getMedium()->getUrl();
                            } catch (\Exception $exc) {
                                echo \My\General::getColoredString("ERRRORR :    {$key}", 'red');
                            }

                            //
                            $is_exits = $instanceSearchContent->getDetail([
                                'cont_slug' => General::getSlug($title),
                                'status' => 1
                            ]);
                            if (!empty($is_exits)) {
                                echo \My\General::getColoredString("content title = {$title} is exits \n", 'red');
                                continue;
                            }

                            $arr_data_content = [
                                'cont_title' => $title,
                                'cont_slug' => General::getSlug($title),
                                'cont_main_image' => $main_image,
                                'cont_detail' => html_entity_decode($description),
                                'created_date' => time(),
                                'user_created' => 1,
                                'cate_id' => $cate_id,
                                'cont_description' => $title,
                                'cont_status' => 1,
                                'cont_views' => 0,
                                'method' => 'crawler',
                                'from_source' => $id,
                                'meta_keyword' => str_replace(' ', ',', $title),
                                'updated_date' => time()
                            ];

                            $serviceContent = $this->serviceLocator->get('My\Models\Content');
                            $id = $serviceContent->add($arr_data_content);
                            if ($id) {
                                $arr_data_content['cont_id'] = $id;
                                //$this->postToFb($arr_data_content);
                                echo \My\General::getColoredString("Crawler success 1 post id = {$id} \n", 'green');
                            } else {
                                echo \My\General::getColoredString("Can not insert content db", 'red');
                            }
                            unset($serviceContent);
                            unset($arr_data_content);
                            $this->flush();
                            continue;
                        }
                    }
                }
            }

        } catch (\Exception $exc) {
            echo '<pre>';
            print_r($exc->getMessage());
            echo '</pre>';
            die();
        }
        die('DONE');
    }

}
