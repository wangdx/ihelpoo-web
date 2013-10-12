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
include_once "./xhprof_lib/utils/xhprof_lib.php";
include_once "./xhprof_lib/utils/xhprof_runs.php";
$xhprof_runs = new XHProfRuns_Default();
$run_id = $xhprof_runs->save_run($xhprof_data, 'xhprof');
echo 'http://42.62.50.238/xhprof_html/index.php?run='.$run_id.'&source=xhprof';

?>