<?php

/**
 *
 * @author cho
 * @email admin@tvery.com 121670155@qq.com
 *
 */
class IUserLoginModel extends Model{

    protected $tableName = 'user_login';

    /**
     *
     * Login check
     * @param char $email
     * @param char $password
     * @throws Exception
     * lastlogintime equal yesterday login time
     */
    public function userVerification($email, $password, $online = 1)
    {
        $user = $this->where("email = '$email'")->find();
        $dateInfo = getdate();
        $todayChar = $dateInfo['yday'];
        if (!$user['uid']) {
            return '用户不存在';
        } else {
            if ($user['password'] == $password) {
                $data['uid'] = $user['uid'];
                $data['ip'] = $_SERVER['REMOTE_ADDR'];
                $data['logintime'] = time();
                $data['online'] = $online;
            	$lastdayChar = date('z', $user['lastlogintime']);
            	if ($todayChar != $lastdayChar) {
            		$data['lastlogintime'] = $user['logintime'];
            	} else {
            		$data['lastlogintime'] = $user['lastlogintime'];
            	}
                $this->save($data);
                return array(
                    'uid' => $user['uid'], 
                    'logintime' => $data['logintime'], 
                    'lastlogintime' => $data['lastlogintime'],
                	'email' => $user['email'],
                	'password' => $user['password'],
                	'school' => $user['school']
                );
            } else {
                return '密码错误';
            }
        }
    }

    /**
     *
     * Return i_user_login record user object or FALSE;chech input type
     * @param int or varchar $uidOrEmail
     */
    public function userExists($uidOrEmail)
    {
        if (preg_match("/@/i", $uidOrEmail)) {
        	$user = $this->where("email = '$uidOrEmail'")->find();
        } else {
        	$user = $this->where("uid = '$uidOrEmail'")->find();
        }
        return $user == TRUE ? $user : FALSE;
    }
}
?>