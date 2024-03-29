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
    	if ($this->isPost()) {
            if (!empty($_FILES)) {
            	$userloginid = $_POST['userloginid'];
                if ($_FILES["uploadedimg"]["error"] > 0) {
                    $this->ajaxReturn(0, '上传图片失败, info' . $_FILES["uploadedimg"]["error"], 'error');
                } else {
                    $imageOldName = $_FILES["uploadedimg"]["name"];
                    $imageSize = $_FILES["uploadedimg"]["size"];
                    $imageTmpName = $_FILES["uploadedimg"]["tmp_name"];
                }
                if ($imageSize > 3670016) {
                    $this->ajaxReturn(0, '上传图片太大, 最大能上传单张 3.5MB', 'error');
                } else {

                    /**
                     * storage in upyun
                     */
                    Vendor('Ihelpoo.Upyun');
                    $upyun = new UpYun('ihelpoo', 'image', 'ihelpoo2013');
                    $fh = fopen($imageTmpName, 'rb');
                    $fileName = 'iconorignal' . $userloginid . time() . '.jpg';
                    $storageTempFilename = '/useralbum/' . $userloginid . '/' . $fileName;
                    $rsp = $upyun->writeFile($storageTempFilename, $fh, True);
                    fclose($fh);
                    $imageStorageUrl = image_storage_url();
                    $newfilepath = $imageStorageUrl . $storageTempFilename;

                    $opts = array(
                        UpYun::X_GMKERL_TYPE => 'fix_max',
                        UpYun::X_GMKERL_VALUE => 150,
                        UpYun::X_GMKERL_QUALITY => 95,
                        UpYun::X_GMKERL_UNSHARP => True
                    );
                    $fh = fopen($imageTmpName, 'rb');
                    $storageThumbTempFilename = '/useralbum/' . $userloginid . '/thumb_' . $fileName;
                    $rsp = $upyun->writeFile($storageThumbTempFilename, $fh, True, $opts);
                    fclose($fh);

                    /**
                     * insert into i_user_album
                     */
                    $UserAlbum = M("UserAlbum");
                    $newAlbumIconData = array(
                        'uid' => $userloginid,
                        'type' => 1,
                        'url' => $newfilepath,
                        'size' => $imageSize,
                        'time' => time()
                    );
                    $UserAlbum->add($newAlbumIconData);

                    /**
                     * ajax return
                     */
                    $this->ajaxReturn($newfilepath, '上传成功', 'uploaded');
                }
            }
        }	
    }

}

?>