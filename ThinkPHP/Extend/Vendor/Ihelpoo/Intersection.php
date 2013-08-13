<?php
/**
 * @author cho
 */
class Intersection{

	/**
	 *
	 * compare two users in
	 *
	 * sex
	 * birthday
	 * enteryear
	 * loginintime
	 *
	 * academy_op
	 * specialty_op
	 * dormitory_op
	 * *province
	 * city_op
	 *
	 * active
	 * online_is
	 *
	 * @param $userFirst array
	 * @param $userSecond array
	 * return $compareResult array
	 */
    public function compare($userFirst, $userSecond){
        if ($userFirst['sex'] == $userSecond['sex']) {
            $sexArray = array(
                'flag' => TRUE,
                'text' => "物理法则~你们是同性相斥",
            );
        } else {
       	    $sexArray = array(
                'flag' => TRUE,
                'text' => "物理法则~你们是异性相吸",
            );
        }

        if ($userFirst['birthday'] == $userSecond['birthday']) {
            $birthdayArray = array(
                'flag' => TRUE,
                'text' => "哈哈，你们是同年同月同日生的呢",
            );
        }

        if ($userFirst['enteryear'] == $userSecond['enteryear']) {
        	$enteryearArray = array(
                'flag' => TRUE,
                'text' => "你们同一年进入大学",
        	    'para' => $userSecond['enteryear'],
            );
        } else if (abs($userFirst['enteryear'] - $userSecond['enteryear']) <= 2 && $userFirst['sex'] != $userSecond['sex']) {
            if ($userFirst['sex'] == 1 && $userFirst['enteryear'] < $userSecond['enteryear']) {
                $enteryearArray = array(
                    'flag' => TRUE,
                    'text' => "哈哈，学妹是学长的",
        	        'para' => $userSecond['enteryear'],
                );
            } else if ($userFirst['sex'] == 1 && $userFirst['enteryear'] > $userSecond['enteryear']) {
            	$enteryearArray = array(
                    'flag' => TRUE,
                    'text' => "哈哈，学姐与学弟...",
        	        'para' => $userSecond['enteryear'],
                );
            } else if ($userFirst['sex'] == 2 && $userFirst['enteryear'] < $userSecond['enteryear']) {
            	$enteryearArray = array(
                    'flag' => TRUE,
                    'text' => "哈哈，学姐与学弟...",
        	        'para' => $userSecond['enteryear'],
                );
            } else if ($userFirst['sex'] == 2 && $userFirst['enteryear'] > $userSecond['enteryear']) {
            	$enteryearArray = array(
                    'flag' => TRUE,
                    'text' => "哈哈，学妹是学长的",
        	        'para' => $userSecond['enteryear'],
                );
            }
        }

        if ($userSecond['logintime'] > $userFirst['logintime']) {
        		$timeGape = $userSecond['logintime'] - $userFirst['logintime'];
        	} else {
        		$timeGape = $userFirst['logintime'] - $userSecond['logintime'];
        	}
        	if ($timeGape < 60 * 60 ) {
        	    $timeGape = round($timeGape / 60);
        	    $timeGape .= "mins";
        	} else if ($timeGape < 60 * 60 * 24) {
        	    $timeGape = round($timeGape / 3600);
        	    $timeGape .= "hours";
        	} else {
        	    $timeGape = round($timeGape / 86400);
        	    $timeGape .= "days";
        	}
        	$logintimeArray = array(
                'flag' => TRUE,
                'text' => "你们这次登录我帮圈圈的时间差是: ".$timeGape,
        );

        /**
         * 
         */
        if ($userFirst['academy_op'] && $userSecond['academy_op']) {
            if ($userFirst['academy_op'] == $userSecond['academy_op']) {
            	$academy_opArray = array(
                    'flag' => TRUE,
                    'text' => "你们就读的是一样的学院，很可能认识啊",
        	        'para' => $userSecond['academy_op'],
                );
            }
        }

        if ($userFirst['specialty_op'] && $userSecond['specialty_op']) {
            if ($userFirst['specialty_op'] == $userSecond['specialty_op']) {
            	$specialty_opArray = array(
                    'flag' => TRUE,
                    'text' => "你们学的是一样的专业，估计是同班童鞋",
        	        'para' => $userSecond['specialty_op'],
                );
            }
        }

        /**
         * 
         */
        if ($userFirst['dormitory_op'] && $userSecond['dormitory_op']) {
            if ($userFirst['dormitory_op'] == $userSecond['dormitory_op']) {
            	$dormitory_opArray = array(
                    'flag' => TRUE,
                    'text' => "你们住在同一栋寝室呀",
        	        'para' => $userSecond['dormitory_op'],
                );
            } 
        }

        if ($userFirst['city_op'] && $userSecond['city_op']) {
            if ($userFirst['city_op'] == $userSecond['city_op']) {
            	$city_opArray = array(
                    'flag' => TRUE,
                    'text' => "你们家乡是同样的城市哦",
        	        'para' => $userSecond['city_op'],
                );
            }
        }

        if (abs($userFirst['active'] - $userSecond['active']) <= 100) {
            $activeArray = array(
                'flag' => TRUE,
                'text' => "你们在我帮圈圈中活跃程度差不多",
        	    'para' => $userSecond['active'],
            );
        }

        if ($userFirst['online'] == $userSecond['online']) {
            $onlineArray = array(
                'flag' => TRUE,
                'text' => "你们刚才都在线呢，快去聊聊吧",
        	    'para' => $userSecond['online'],
            );
        }

        return array(
            'sex' => $sexArray,
            'birthday' => $birthdayArray,
            'enteryear' => $enteryearArray,
            'logintime' => $logintimeArray,
            'academy_op' => $academy_opArray,
            'specialty_op' => $specialty_opArray,
            'dormitory_op' => $dormitory_opArray,
            'city_op' => $city_opArray,
            'active' => $activeArray,
            'online' => $onlineArray,
        );
    }
}