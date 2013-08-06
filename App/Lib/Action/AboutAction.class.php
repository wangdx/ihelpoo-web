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
    	$recordSchoolInfo = i_school_domain();
    	$title = "关于我们 ".$recordSchoolInfo['school'];
    	$this->assign('title', $title);
    	
    	$SchoolSystem = M("SchoolSystem");
    	$recordSchoolSystem = $SchoolSystem->where("sid = $recordSchoolInfo[id]")->order("time DESC")->find();
    	$this->assign('schoolabout',$recordSchoolSystem['about']);
    	
    	$SchoolWebmaster = M("SchoolWebmaster");
    	$recordSchoolWebmaster = $SchoolWebmaster->where("sid = $recordSchoolInfo[id]")->join("i_user_login ON i_school_webmaster.uid = i_user_login.uid")->order("position DESC")->select();
    	$this->assign('schoolwebmaster',$recordSchoolWebmaster);
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