<?php

/**
 *
 * @author cho
 * @email admin@tvery.com 121670155@qq.com
 *
 */
class ISysParameterModel extends Model{
	
    protected $tableName = 'sys_parameter'; 
	
    /**
     * 
     * get a record through parameter
     * @param string $parameter
     */
    public function getParam($parameter)
    {
        $record = $this->where("parameter = '$parameter'")->find();
        return $record != NULL ? $record : NULL;
    }
    
    /**
     * 
     * Update parameter
     * @param varchar $parameter
     * @param int $value
     */
    public function paraUpdate($parameter, $value)
    {
    	$record = $this->where("parameter = $parameter")->find();
        $set = array(
        	'id' => $record['id'],
            'value' => $value,
        );
        $affected = $this->save($set);
        return $affected == TRUE ? $affected : FALSE;
    }
}
?>