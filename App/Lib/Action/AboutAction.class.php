<?php

/**
 *
 * @author cho
 * @email admin@tvery.com 121670155@qq.com
 *
 */
class AboutAction extends Action {

    protected function _initialize()
    {
        header("Content-Type:text/html; charset=utf-8");
    }
    
    public function index()
    {
    	$this->assign('title','关于我们');
    	$this->display();
    }
    
    /**
     * artical
     */
    public function artical()
    {
    	$articalid = (int)htmlspecialchars(trim($_GET["_URL_"][2]));
    	$CmsArtical = M("CmsArtical");
    	if (!empty($articalid)) {
    		$cmsArticalRecord = $CmsArtical->where("id = $articalid")->find();
    		$this->assign('cmsArticalRecord',$cmsArticalRecord);
    		$this->assign('title',$cmsArticalRecord['title'].' 关于我们');
    		
    		/**
    		 * update hit nums
    		 */
    		$updateArticalHit = array(
	        	'id' => $articalid,
	        	'hit' => $cmsArticalRecord['hit'] + 1,
    		);
    		$CmsArtical->save($updateArticalHit);
    	} else {
    		$cmsArticalRecords = $CmsArtical->order("time DESC")->select();
    		$this->assign('cmsArticalRecords',$cmsArticalRecords);
    		$this->assign('title','文章列表 关于我们');
    	}
        $this->display();
    }

}

?>