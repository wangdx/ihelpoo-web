<?php

/**
 *
 * @author cho
 * @email admin@tvery.com 121670155@qq.com
 *
 */
class OtherAction extends Action {

    protected function _initialize()
    {
    }
    
    public function index()
    {
    	//$this->display();
    }
    
    public function verifi()
    {
    	/**
    	 * create virification code
    	 
    	Vendor('Ihelpoo.Verifi');
    	$verifi = new Verifi();
    	$verifiString = $verifi->value_rand();
    	$_SESSION['verificationcode'] =  $verifiString['formula'];
    	$_SESSION['verificationresult'] =  $verifiString['result'];
    	*/
            
    	/**
    	 * show verification code;
    	 */
    	Vendor('Ihelpoo.Verifi');
    	$verifi = new Verifi();
    	$verifi->img_create($_SESSION['verificationcode']);
    }
    
    
    /**
     * ajax
     */
    public function getschoollist()
    {
    	if (!empty($_POST['getschoollist'])) {
        	$SchoolInfo = M("SchoolInfo");
        	$resultsSchoolInfo = $SchoolInfo->order("initial ASC")->select();
        	echo '<div class="setting_school_list_div"><a class="gray f12" id="setting_school_close_span"><span class="close_x" title="关闭">×</span></a><ul>';
        	foreach ($resultsSchoolInfo as $schoolInfo) {
        		echo "<li><a href='".__ROOT__."/user/register?school=$schoolInfo[id]'>$schoolInfo[school]</a></li>";
        	}
        	echo '</ul></div>';
        }
    }

}

?>