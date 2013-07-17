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
                'text' => "你们都同一年来到民院",
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
         * 1 文传
         * 2 理学院
         * 3 信工
         * 4 化环
         * 5 医学院
         * 6 经管
         * 7 生科
         * 8 外国语
         * 9 体育
         * 10 艺术
         * 11 法学院
         * 12 马克思学院
         * 15 预科
         */
        if ($userFirst['academy_op'] && $userSecond['academy_op']) {
            if ($userFirst['academy_op'] == $userSecond['academy_op']) {
            	$academy_opArray = array(
                    'flag' => TRUE,
                    'text' => "你们就读的是一样的学院，很可能认识啊",
        	        'para' => $userSecond['academy_op'],
                );
            } else if (in_array($userFirst['academy_op'], array(1,6)) && in_array($userSecond['academy_op'], array(1,6))) {
            	$academy_opArray = array(
                    'flag' => TRUE,
                    'text' => "文传和经管挨着的，你们可能N次擦肩",
            	    'para' => $userSecond['academy_op'],
                );
            } else if (in_array($userFirst['academy_op'], array(3,4)) && in_array($userSecond['academy_op'], array(3,4))) {
            	$academy_opArray = array(
                    'flag' => TRUE,
                    'text' => "信工和化环楼上与楼下的位置关系，你们应该能见到",
            	    'para' => $userSecond['academy_op'],
                );
            } else if (in_array($userFirst['academy_op'], array(6,4)) && in_array($userSecond['academy_op'], array(6,4))) {
            	$academy_opArray = array(
                    'flag' => TRUE,
                    'text' => "经管的后面山上就是化环，可惜路线差异，你们见到机会小。",
            	    'para' => $userSecond['academy_op'],
                );
            } else if (in_array($userFirst['academy_op'], array(6,8)) && in_array($userSecond['academy_op'], array(6,8))) {
            	$academy_opArray = array(
                    'flag' => TRUE,
                    'text' => "经管和外国语，路线差异，你们见到机会小。",
            	    'para' => $userSecond['academy_op'],
                );
            } else if (in_array($userFirst['academy_op'], array(3,4)) && in_array($userSecond['academy_op'], array(3,4))) {
            	$academy_opArray = array(
                    'flag' => TRUE,
                    'text' => "信工和外国语学院偶尔联谊啊，不参加活动的孩子应该不认识",
            	    'para' => $userSecond['academy_op'],
                );
            } else if (in_array($userFirst['academy_op'], array(1,3)) && in_array($userSecond['academy_op'], array(1,3))) {
            	$academy_opArray = array(
                    'flag' => TRUE,
                    'text' => "文传和信工邻居，可能碰过面",
            	    'para' => $userSecond['academy_op'],
                );
            } else if (in_array($userFirst['academy_op'], array(3,6)) && in_array($userSecond['academy_op'], array(3,6))) {
            	$academy_opArray = array(
                    'flag' => TRUE,
                    'text' => "经管和信工不远，你们可能碰面过",
            	    'para' => $userSecond['academy_op'],
                );
            } else if (in_array($userFirst['academy_op'], array(1,6,7)) && in_array($userSecond['academy_op'], array(1,6,7))) {
            	$academy_opArray = array(
                    'flag' => TRUE,
                    'text' => "生科和经管、文传 面对面的，你们可能多次碰面",
            	    'para' => $userSecond['academy_op'],
                );
            } else if (in_array($userFirst['academy_op'], array(9,11,12)) && in_array($userSecond['academy_op'], array(9,11,12))) {
            	$academy_opArray = array(
                    'flag' => TRUE,
                    'text' => "体育学院和法学院、马克思学院 在一栋楼啊，你们可能多次碰面",
            	    'para' => $userSecond['academy_op'],
        	        'para' => $userSecond['academy_op'],
                );
            } else if (in_array($userFirst['academy_op'], array(10,8)) && in_array($userSecond['academy_op'], array(10,8))) {
            	$academy_opArray = array(
                    'flag' => TRUE,
                    'text' => "艺术学院和外国语学院 不远噢，你们可能N次擦肩",
            	    'para' => $userSecond['academy_op'],
        	        'para' => $userSecond['academy_op'],
                );
            } else if (in_array($userSecond['academy_op'], array(5))) {
            	$academy_opArray = array(
                    'flag' => TRUE,
                    'text' => "医学院上选修课的好地方啊，但人口密集，你们见过也估计不认识",
            	    'para' => $userSecond['academy_op'],
        	        'para' => $userSecond['academy_op'],
                );
            } else if (in_array($userFirst['academy_op'], array(15)) && in_array($userSecond['academy_op'], array(15))) {
            	$academy_opArray = array(
                    'flag' => TRUE,
                    'text' => "都是预科的“小朋友”呀，大本营欢迎你们",
            	    'para' => $userSecond['academy_op'],
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
         * 1 1d
         * 2 2d
         * 3 3d
         * 4 4d
         * 5 5d
         * 6 6d
         * 7 7d
         * 8 8d
         * 9 9d
         * 10 10d
         * 11 11d
         * 12 12d
         * 13 13d
         * 14 14d
         * 15 15d
         * 16 16d
         * 17 17d
         * 36 k1
         * 37 k2
         * 38 k3
         * 18 k4
         * 19 k5
         * 20 k6
         * 21 k7
         * 22 k8
         * 23 k9
         * 24 k10
         * 25 k11
         * 26 三洼 18
         * 27 三洼 19
         * 28 三洼 20
         * 29 三洼 21
         * 35 三洼 23
         * 39 三洼 22
         * 30 26d
         * 31 教工
         * 40 松林湾
         * 41 三小
         * 33 校内其他
         * 34 校外
         * 42 木子潭
         */
        if ($userFirst['dormitory_op'] && $userSecond['dormitory_op']) {
            if ($userFirst['dormitory_op'] == $userSecond['dormitory_op']) {
            	$dormitory_opArray = array(
                    'flag' => TRUE,
                    'text' => "你们住在同一栋寝室呀",
        	        'para' => $userSecond['dormitory_op'],
                );
            } else if (in_array($userFirst['dormitory_op'], array(31,40,41,30)) && in_array($userSecond['dormitory_op'], array(31,40,41,30))) {
            	$dormitory_opArray = array(
                    'flag' => TRUE,
                    'text' => "你们都活跃在教工食堂 行政楼附近一带，教工宿舍 松林湾 三小 26栋 区域，你们可能经常擦肩而过",
        	        'para' => $userSecond['dormitory_op'],
                );
            } else if (in_array($userFirst['dormitory_op'], array(1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17)) && in_array($userSecond['dormitory_op'], array(1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17))) {
            	$dormitory_opArray = array(
                    'flag' => TRUE,
                    'text' => "你们都活跃在图书馆 行政楼 2食堂附近一带，1~7栋宿舍 8~17栋宿舍 区域，你们可能经常碰面",
        	        'para' => $userSecond['dormitory_op'],
                );
            } else if (in_array($userFirst['dormitory_op'], array(36,37,38,18,19,20,21,22,23,24,25)) && in_array($userSecond['dormitory_op'], array(36,37,38,18,19,20,21,22,23,24,25))) {
            	$dormitory_opArray = array(
                    'flag' => TRUE,
                    'text' => "你们都活跃在五环体育场附近一带，k1~k11栋宿舍 区域，你们可能经常碰面",
        	        'para' => $userSecond['dormitory_op'],
                );
            } else if (in_array($userFirst['dormitory_op'], array(26,27,28,29,35,39)) && in_array($userSecond['dormitory_op'], array(26,27,28,29,35,39))) {
            	$dormitory_opArray = array(
                    'flag' => TRUE,
                    'text' => "你们都活跃在 三洼 艺术学院 附近一带，三洼宿舍 区域，你们可能经常碰面",
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