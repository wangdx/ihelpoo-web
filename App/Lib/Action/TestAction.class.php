<?php

/**
 * 本页仅供测试
 */
class TestAction extends Action {

    protected function _initialize() {
        header("Content-Type:text/html; charset=utf-8");
    }
    
    public function index() {
    	
    	$email = $_GET['email'];
    	echo $email;
    	if (ereg("/^[a-z]([a-z0-9]*[-_\.]?[a-z0-9]+)*@([a-z0-9]*[-_]?[a-z0-9]+)+[\.][a-z]{2,3}([\.][a-z]{2})?$/i; ", $email)){
    		echo "Your email address is correct!";
    	} else {
    		echo "Please try again!";
    	}
    	
    	/**
    	 * important change old image url
    	 * i_user_album
    	 * i_school_album
    	 */
    	exit();
    	$SchoolAlbum = M("SchoolAlbum");
    	$recordsUserAlbum = $SchoolAlbum->where("`url` LIKE '%upaiyun%'")->limit(1000)->order('id ASC')->select();
    	foreach ($recordsUserAlbum as $recordUserAlbum) {
	    	if (preg_match("/ihelpoo.b0.upaiyun.com/", $recordUserAlbum['url'])) {
	            echo $urlThumbFilename = str_ireplace("ihelpoo.b0.upaiyun.com", "img.ihelpoo.cn", $recordUserAlbum['url']);
	    		$newAlbumData = array(
	    			'id' => $recordUserAlbum['id'],
	    			'url' => $urlThumbFilename
	    		);
	    		$id = $SchoolAlbum->save($newAlbumData);
	    		echo $id."ok<br />";
	        }
    	}
    	
    }

}

?>