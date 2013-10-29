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
    	$recordsUserAlbum = $UserAlbum->where("`url` LIKE '%sinaapp%'")->limit(100)->select();
    	foreach ($recordsUserAlbum as $recordUserAlbum) {
	    	if (preg_match("/ihelpoo-public.stor.sinaapp.com/", $recordUserAlbum['url'])) {
	            $urlThumbFilename = str_ireplace("ihelpoo-public.stor.sinaapp.com", "img.ihelpoo.cn", $recordUserAlbum['url']);
	    		echo $urlThumbFilename."<br />";
	        }
    	}
    	
    }

}

?>