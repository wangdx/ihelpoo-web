<?php

xhprof_enable(XHPROF_FLAGS_CPU + XHPROF_FLAGS_MEMORY);
xhprof_enable();
/**
 * 本页仅供测试
 */
class TestAction extends Action {

    protected function _initialize() {
        header("Content-Type:text/html; charset=utf-8");
//        exit();
    }
    
    public function index() {
    }

}
$xhprof_data = xhprof_disable();
var_dump($xhprof_data);

?>