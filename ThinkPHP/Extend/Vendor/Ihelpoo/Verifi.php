<?php
class Verifi{
    public function value_rand($value = NULL)
    {
        if (empty($value)) {
            $x = mt_rand(0, 11);
            $y = mt_rand(0, 9);
            $char = "+,-,*";
            $charArray = explode(",", $char);
            $charMax = count($charArray) - 1;
            $randnum = mt_rand(0, $charMax);
            $way = $charArray[$randnum];
            if ($x > $y) {
            	$formula = $x." ".$way." ".$y." = ?";
            } else {
            	$formula = $y." ".$way." ".$x." = ?";
            }
            if ("+" == $way) {
                $result = $x + $y;
            } else if ("-" == $way) {
            	if ($x > $y) {
                	$result = $x - $y;
            	} else {
            		$result = $y - $x;
            	}
            } else if ("*" == $way) {
                $result = $x * $y;
            }
            $valueArray = array(
               'formula' => $formula,
               'result' => $result
            );
        } else {
        	$valueArray = array(
               'formula' => $value,
            );
        }
        return $valueArray;
    }
    
    public function img_create($value)
    {
        header("Content-type: image/png"); 
        //$im = imagecreatetruecolor(100, 25);
        $im = imagecreatefrompng("http://ihelpoo.sinaapp.com/Public/image/common/verifi.png");
        $background_color = imagecolorallocate($im, 255, 255, 255); 
        //填充背景颜色(这个东西类似油桶)
        imagefill($im,0,0,$background_color); 
        //获取边框颜色 
        $border_color = imagecolorallocate($im, 222, 222, 222); 
        //画矩形，边框颜色200,200,200 
        //imagerectangle($im,0,0,103,24,$border_color); 
        //画文字
        $stringlength = strlen($value);
        $x = mt_rand(8, 18);
        for($i = 0 ; $i <= $stringlength; $i++) {
	        $font_size = mt_rand(3, 7);
	        $text_color = imagecolorallocate($im,rand(80,200),rand(80,200),rand(80,200));
	        $y = mt_rand(3, 6);
            $nowChar = substr($value, $i, 1);
            imagechar($im, $font_size, $x, $y, $nowChar, $text_color);
            $x += mt_rand(5, 7);
        }
        imagepng($im);
        imagedestroy($im);
    }
}