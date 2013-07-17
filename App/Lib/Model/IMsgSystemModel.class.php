<?php

/**
 *
 * @author cho
 * @email admin@tvery.com 121670155@qq.com
 *
 */
class IMsgSystemModel extends Model{
	
    protected $tableName = 'msg_system'; 
	
    /**
     * 
     * Get the nums message not deliver
     * @param int $uid
     */
    public function messageCount($uid)
    {
        $recordNums = $this->where("uid = $uid AND deliver = 0")->count();
        return $recordNums == TRUE ? $recordNums : FALSE;
    }
    
    /**
     * 
     * Get the message not deliver
     * @param int $uid
     */
    public function message($uid, $count, $offset)
    {
        $records = $this->where("uid = $uid")->order("time DESC")->limit($offset,$count)->select();
        return $records == TRUE ? $records : NULL;
    }
    
    /**
     * 
     * if delievered update the record 
     * @param int $id
     */
    public function deliver($id)
    {
    	$set = array(
    		'id' => $id,
    	    'deliver' => 1
    	);
    	$affected = $this->save($set);
        return $affected == TRUE ? $affected : FALSE;
    }
}
?>