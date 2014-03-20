<?php

    require_once 'BaseController.php';

    class TestController extends BaseController {
        public function IndexAction()
        {
            require_once $this->viewPath . 'test/helper/ListTable.php';

            //Create an instance of our package class...
            $testListTable = new KWPS_Test_List_Table();
            //Fetch, prepare, sort, and filter our data...
            $testListTable->prepare_items();

            require_once $this->viewPath . 'test/list.php';
        }
    }
?>