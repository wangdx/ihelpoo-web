<?php

/**
 * 本页仅供测试
 */
class TestAction extends Action {

    protected function _initialize() {
        header("Content-Type:text/html; charset=utf-8");
//        exit();
    }
    
    public function index() {
        xhprof_enable();
        xhprof_disable();
    }

}

?>