<?php

/**
 * 本页仅供测试
 */
class TestAction extends Action {

    protected function _initialize() {
        header("Content-Type:text/html; charset=utf-8");
    }
    
    public function index() {
    	/**
    	 * important change old image url
    	 * i_user_album
    	 * i_school_album
    	 */
    	
    	$UserAlbum = M("UserAlbum");
    	$recordUserAlbum = $UserAlbum->where("`url` LIKE '%sinaapp%'")->select();
    	var_dump($recordUserAlbum);
    	
    }

}

?>