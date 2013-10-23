<?php

/**
 * 本页仅供测试
 */
class TestAction extends Action {

    protected function _initialize() {
        header("Content-Type:text/html; charset=utf-8");
    }
    
    public function index() {
    	Vendor('Ihelpoo.Browser');
    	$browserObj = new Browser();
    	$getBrowser = $browserObj->getBrowser();
    	if ($getBrowser == "IE" || $getBrowser == "Android") {
    		$fromBrowser = $browserObj->getPlatform() . " " . $getBrowser . " " . $browserObj->getVersion();
    	} else {
    		$fromBrowser = $browserObj->getPlatform() . " " . $getBrowser;
    	}
    	echo $fromBrowser;
    }

}

?>