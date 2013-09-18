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
    
    public function iconupload()
    {	
    	$this->ajaxReturn(0,'message ajax','ok');
    }

}

?>