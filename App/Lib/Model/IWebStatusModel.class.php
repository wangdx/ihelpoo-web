<?php

/**
 *
 * @author cho
 * @email admin@tvery.com 121670155@qq.com
 *
 */
class IWebStatusModel extends Model{
	
    protected $tableName = 'web_status';
    
    /**
     *
     * Return i_user_login record user object or FALSE;chech input type
     * @param int or varchar $uidOrEmail
     */
    public function paraExists($para)
    {
        $record = $this->where("parameter = '".$para."'")->find();
        return $record == TRUE ? $record : FALSE;
    }
	
}
?>