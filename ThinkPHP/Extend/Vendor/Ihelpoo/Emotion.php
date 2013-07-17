<?php
class Emotion{
	public function transEmotion($words, $baseUrl = "/Public"){
		$patternS1 = '/\[呵呵\]/';
		$patternS2 = '/\[嘻嘻\]/';
		$patternS3 = '/\[哈哈\]/';
		$patternS4 = '/\[嘿嘿\]/';
		$patternS5 = '/\[赫赫\]/';
		$patternM1 = '/\[我晕\]/';
		$patternM2 = '/\[我靠\]/';
		$patternM3 = '/\[我擦\]/';
		$patternM4 = '/\[我勒个去\]/';
		$patternM5 = '/\[我吐\]/';
		$patternQ1 = '/\[害羞\]/';
		$patternQ2 = '/\[花心\]/';
		$patternQ3 = '/\[摆酷\]/';
		$patternQ4 = '/\[鼓掌\]/';
		$patternQ5 = '/\[亲亲\]/';
		$patternQ6 = '/\[可爱\]/';
		$patternQ7 = '/\[感动\]/';
		$patternQ8 = '/\[哭了\]/';
		$patternQ9 = '/\[鄙视\]/';
		$patternQ10 = '/\[挖鼻屎\]/';
		$patternQ11 = '/\[吐舌头\]/';
		$patternQ12 = '/\[耶\]/';
		$patternG1 = '/\[鲜花\]/';
		$patternG2 = '/\[月亮\]/';
		$patternG3 = '/\[太阳\]/';
		$patternG4 = '/\[猪头\]/';
		$patternG5 = '/\[便便\]/';
		$patternJ1 = '/\[大爱\]/';
		$patternJ2 = '/\[享受\]/';
		$patternJ3 = '/\[扭屁股\]/';
		$patternJ4 = '/\[顶\]/';
		$patternJ5 = '/\[投降了\]/';
		$patternJ6 = '/\[撞砖\]/';
		$patternJ7 = '/\[揉脸\]/';
		$patternJ8 = '/\[瞌睡\]/';
		$patternJ9 = '/\[汗\]/';
		$patternJ10 = '/\[哦也\]/';
		$patternJ11 = '/\[啊！\]/';
		$patternT1 = '/\[爱\]/';
		$patternT2 = '/\[兴奋\]/';
		$patternT3 = '/\[飞吻\]/';
		$patternT4 = '/\[安慰\]/';
		$patternT5 = '/\[扮鬼脸\]/';
		$patternQO1 = '/\[可怜\]/';
		$patternQO2 = '/\[愤怒\]/';
		$patternQO3 = '/\[惊恐\]/';
		$patternQO4 = '/\[骂\]/';
		$patternQO5 = '/\[疑问\]/';
		$patternSi1 = '/\[吃货\]/';
		$patternSi2 = '/\[笑哈哈\]/';
		$patternSi3 = '/\[江南style\]/';
		$patternSi4 = '/\[飞个吻\]/';
		$patternSi5 = '/\[浮云\]/';
		$patternSi6 = '/\[给力\]/';
		$patternSi7 = '/\[围观\]/';
		$patternSi8 = '/\[威武\]/';
		$patternSi9 = '/\[奥特曼\]/';
		$patternSi10 = '/\[生病\]/';
		$patternSi11 = '/\[泪\]/';
		$patternSi12 = '/\[太开心\]/';
		$patternSi13 = '/\[good\]/';
		$patternSi14 = '/\[不要\]/';
		$patternF1 = '/\[圣诞\]/';
		$patternF2 = '/\[结婚罗\]/';
		$patternF3 = '/\[红包\]/';
		$patternF4 = '/\[爆竹声声\]/';
		$patternF5 = '/\[生日快乐\]/';
		$patternF6 = '/\[国旗\]/';
		$patternF7 = '/\[蜡烛\]/';
		$patternF8 = '/\[熊猫\]/';
		$patternF9 = '/\[偷乐\]/';
		$patternF10 = '/\[ok\]/';
		
		$replacementS1 = '<img src="'.$baseUrl.'/image/emotion/s1.gif" title="呵呵" height="24" width="24" />';
		$replacementS2 = '<img src="'.$baseUrl.'/image/emotion/s2.gif" title="嘻嘻" height="24" width="24" />';
		$replacementS3 = '<img src="'.$baseUrl.'/image/emotion/s3.gif" title="哈哈" height="24" width="24" />';
		$replacementS4 = '<img src="'.$baseUrl.'/image/emotion/s4.gif" title="嘿嘿" height="24" width="24" />';
		$replacementS5 = '<img src="'.$baseUrl.'/image/emotion/s5.gif" title="赫赫" height="24" width="24" />';
		$replacementM1 = '<img src="'.$baseUrl.'/image/emotion/m1.gif" title="我晕" height="24" width="24" />';
		$replacementM2 = '<img src="'.$baseUrl.'/image/emotion/m2.gif" title="我靠" height="24" width="24" />';
		$replacementM3 = '<img src="'.$baseUrl.'/image/emotion/m3.gif" title="我擦" height="24" width="24" />';
		$replacementM4 = '<img src="'.$baseUrl.'/image/emotion/m4.gif" title="我勒个去" height="24" width="24" />';
		$replacementM5 = '<img src="'.$baseUrl.'/image/emotion/m5.gif" title="我吐" height="24" width="24" />';
		$replacementQ1 = '<img src="'.$baseUrl.'/image/emotion/q1.gif" title="害羞" height="24" width="24" />';
		$replacementQ2 = '<img src="'.$baseUrl.'/image/emotion/q2.gif" title="花心" height="24" width="24" />';
		$replacementQ3 = '<img src="'.$baseUrl.'/image/emotion/q3.gif" title="摆酷" height="24" width="24" />';
		$replacementQ4 = '<img src="'.$baseUrl.'/image/emotion/q4.gif" title="鼓掌" height="24" width="24" />';
		$replacementQ5 = '<img src="'.$baseUrl.'/image/emotion/q5.gif" title="亲亲" height="24" width="24" />';
		$replacementQ6 = '<img src="'.$baseUrl.'/image/emotion/q6.gif" title="可爱" height="24" width="24" />';
		$replacementQ7 = '<img src="'.$baseUrl.'/image/emotion/q7.gif" title="感动" height="24" width="24" />';
		$replacementQ8 = '<img src="'.$baseUrl.'/image/emotion/q8.gif" title="哭了" height="24" width="24" />';
		$replacementQ9 = '<img src="'.$baseUrl.'/image/emotion/q9.gif" title="鄙视" height="24" width="24" />';
		$replacementQ10 = '<img src="'.$baseUrl.'/image/emotion/q10.gif" title="挖鼻屎" height="24" width="24" />';
		$replacementQ11 = '<img src="'.$baseUrl.'/image/emotion/q11.gif" title="吐舌头" height="24" width="24" />';
		$replacementQ12 = '<img src="'.$baseUrl.'/image/emotion/q12.gif" title="耶" height="24" width="24" />';
		$replacementG1 = '<img src="'.$baseUrl.'/image/emotion/g1.gif" title="鲜花" height="24" width="24" />';
		$replacementG2 = '<img src="'.$baseUrl.'/image/emotion/g2.gif" title="月亮" height="24" width="24" />';
		$replacementG3 = '<img src="'.$baseUrl.'/image/emotion/g3.gif" title="太阳" height="24" width="24" />';
		$replacementG4 = '<img src="'.$baseUrl.'/image/emotion/g4.gif" title="猪头" height="24" width="24" />';
		$replacementG5 = '<img src="'.$baseUrl.'/image/emotion/g5.gif" title="便便" height="24" width="24" />';
		$replacementJ1 = '<img src="'.$baseUrl.'/image/emotion/j1.gif" title="大爱" height="40" width="40" />';
		$replacementJ2 = '<img src="'.$baseUrl.'/image/emotion/j2.gif" title="享受" height="40" width="40" />';
		$replacementJ3 = '<img src="'.$baseUrl.'/image/emotion/j3.gif" title="扭屁股" height="40" width="40" />';
		$replacementJ4 = '<img src="'.$baseUrl.'/image/emotion/j4.gif" title="顶" height="40" width="40" />';
		$replacementJ5 = '<img src="'.$baseUrl.'/image/emotion/j5.gif" title="投降了" height="40" width="40" />';
		$replacementJ6 = '<img src="'.$baseUrl.'/image/emotion/j6.gif" title="撞砖" height="40" width="40" />';
		$replacementJ7 = '<img src="'.$baseUrl.'/image/emotion/j7.gif" title="揉脸" height="40" width="40" />';
		$replacementJ8 = '<img src="'.$baseUrl.'/image/emotion/j8.gif" title="瞌睡" height="40" width="40" />';
		$replacementJ9 = '<img src="'.$baseUrl.'/image/emotion/j9.gif" title="汗" height="40" width="40" />';
		$replacementJ10 = '<img src="'.$baseUrl.'/image/emotion/j10.gif" title="哦也" height="40" width="40" />';
		$replacementJ11 = '<img src="'.$baseUrl.'/image/emotion/j11.gif" title="啊！" height="40" width="40" />';
		$replacementT1 = '<img src="'.$baseUrl.'/image/emotion/t1.gif" title="爱" height="35" width="36" />';
		$replacementT2 = '<img src="'.$baseUrl.'/image/emotion/t2.gif" title="兴奋" height="29" width="36" />';
		$replacementT3 = '<img src="'.$baseUrl.'/image/emotion/t3.gif" title="飞吻" height="31" width="38" />';
		$replacementT4 = '<img src="'.$baseUrl.'/image/emotion/t4.gif" title="安慰" height="30" width="30" />';
		$replacementT5 = '<img src="'.$baseUrl.'/image/emotion/t5.gif" title="扮鬼脸" height="27" width="34" />';
		$replacementQO1 = '<img src="'.$baseUrl.'/image/emotion/qo1.gif" title="可怜" height="24" width="24" />';
		$replacementQO2 = '<img src="'.$baseUrl.'/image/emotion/qo2.gif" title="愤怒" height="24" width="24" />';
		$replacementQO3 = '<img src="'.$baseUrl.'/image/emotion/qo3.gif" title="惊恐" height="24" width="24" />';
		$replacementQO4 = '<img src="'.$baseUrl.'/image/emotion/qo4.gif" title="骂" height="24" width="24" />';
		$replacementQO5 = '<img src="'.$baseUrl.'/image/emotion/qo5.gif" title="疑问" height="24" width="24" />';
		$replacementSi1 = '<img src="'.$baseUrl.'/image/emotion/si1.gif" title="吃货" height="22" width="22" />';
		$replacementSi2 = '<img src="'.$baseUrl.'/image/emotion/si2.gif" title="笑哈哈" height="22" width="22" />';
		$replacementSi3 = '<img src="'.$baseUrl.'/image/emotion/si3.gif" title="江南style" height="22" width="22" />';
		$replacementSi4 = '<img src="'.$baseUrl.'/image/emotion/si4.gif" title="飞个吻" height="22" width="22" />';
		$replacementSi5 = '<img src="'.$baseUrl.'/image/emotion/si5.gif" title="浮云" height="22" width="22" />';
		$replacementSi6 = '<img src="'.$baseUrl.'/image/emotion/si6.gif" title="给力" height="22" width="22" />';
		$replacementSi7 = '<img src="'.$baseUrl.'/image/emotion/si7.gif" title="围观" height="22" width="22" />';
		$replacementSi8 = '<img src="'.$baseUrl.'/image/emotion/si8.gif" title="威武" height="22" width="22" />';
		$replacementSi9 = '<img src="'.$baseUrl.'/image/emotion/si9.gif" title="奥特曼" height="22" width="22" />';
		$replacementSi10 = '<img src="'.$baseUrl.'/image/emotion/si10.gif" title="生病" height="22" width="22" />';
		$replacementSi11 = '<img src="'.$baseUrl.'/image/emotion/si11.gif" title="泪" height="22" width="22" />';
		$replacementSi12 = '<img src="'.$baseUrl.'/image/emotion/si12.gif" title="太开心" height="22" width="22" />';
		$replacementSi13 = '<img src="'.$baseUrl.'/image/emotion/si13.gif" title="good" height="22" width="22" />';
		$replacementSi14 = '<img src="'.$baseUrl.'/image/emotion/si14.gif" title="不要" height="22" width="22" />';
		$replacementF1 = '<img src="'.$baseUrl.'/image/emotion/f1.gif" title="圣诞" height="22" width="22" />';
		$replacementF2 = '<img src="'.$baseUrl.'/image/emotion/f2.gif" title="结婚罗" height="22" width="22" />';
		$replacementF3 = '<img src="'.$baseUrl.'/image/emotion/f3.gif" title="红包" height="22" width="22" />';
		$replacementF4 = '<img src="'.$baseUrl.'/image/emotion/f4.gif" title="爆竹声声" height="22" width="22" />';
		$replacementF5 = '<img src="'.$baseUrl.'/image/emotion/f5.gif" title="生日快乐" height="22" width="22" />';
		$replacementF6 = '<img src="'.$baseUrl.'/image/emotion/f6.gif" title="国旗" height="22" width="22" />';
		$replacementF7 = '<img src="'.$baseUrl.'/image/emotion/f7.gif" title="蜡烛" height="22" width="22" />';
		$replacementF8 = '<img src="'.$baseUrl.'/image/emotion/f8.gif" title="熊猫" height="22" width="22" />';
		$replacementF9 = '<img src="'.$baseUrl.'/image/emotion/f9.gif" title="偷乐" height="22" width="22" />';
		$replacementF10 = '<img src="'.$baseUrl.'/image/emotion/f10.gif" title="ok" height="22" width="22" />';
		
		if (preg_match($patternS1, $words)) {
			$words = preg_replace($patternS1, $replacementS1, $words);
		}
		if (preg_match($patternS2, $words)) {
			$words = preg_replace($patternS2, $replacementS2, $words);
		}
		if (preg_match($patternS3, $words)) {
			$words = preg_replace($patternS3, $replacementS3, $words);
		}
		if (preg_match($patternS4, $words)) {
			$words = preg_replace($patternS4, $replacementS4, $words);
		}
		if (preg_match($patternS5, $words)) {
			$words = preg_replace($patternS5, $replacementS5, $words);
		}
		if (preg_match($patternM1, $words)) {
			$words = preg_replace($patternM1, $replacementM1, $words);
		}
		if (preg_match($patternM2, $words)) {
			$words = preg_replace($patternM2, $replacementM2, $words);
		}
		if (preg_match($patternM3, $words)) {
			$words = preg_replace($patternM3, $replacementM3, $words);
		}
		if (preg_match($patternM4, $words)) {
			$words = preg_replace($patternM4, $replacementM4, $words);
		}
		if (preg_match($patternM5, $words)) {
			$words = preg_replace($patternM5, $replacementM5, $words);
		}
		if (preg_match($patternQ1, $words)) {
			$words = preg_replace($patternQ1, $replacementQ1, $words);
		}
		if (preg_match($patternQ2, $words)) {
			$words = preg_replace($patternQ2, $replacementQ2, $words);
		}
		if (preg_match($patternQ3, $words)) {
			$words = preg_replace($patternQ3, $replacementQ3, $words);
		}
		if (preg_match($patternQ4, $words)) {
			$words = preg_replace($patternQ4, $replacementQ4, $words);
		}
		if (preg_match($patternQ5, $words)) {
			$words = preg_replace($patternQ5, $replacementQ5, $words);
		}
		if (preg_match($patternQ6, $words)) {
			$words = preg_replace($patternQ6, $replacementQ6, $words);
		}
		if (preg_match($patternQ7, $words)) {
			$words = preg_replace($patternQ7, $replacementQ7, $words);
		}
		if (preg_match($patternQ8, $words)) {
			$words = preg_replace($patternQ8, $replacementQ8, $words);
		}
		if (preg_match($patternQ9, $words)) {
			$words = preg_replace($patternQ9, $replacementQ9, $words);
		}
		if (preg_match($patternQ10, $words)) {
			$words = preg_replace($patternQ10, $replacementQ10, $words);
		}
		if (preg_match($patternQ11, $words)) {
			$words = preg_replace($patternQ11, $replacementQ11, $words);
		}
		if (preg_match($patternQ12, $words)) {
			$words = preg_replace($patternQ12, $replacementQ12, $words);
		}
		if (preg_match($patternG1, $words)) {
			$words = preg_replace($patternG1, $replacementG1, $words);
		}
		if (preg_match($patternG2, $words)) {
			$words = preg_replace($patternG2, $replacementG2, $words);
		}
		if (preg_match($patternG3, $words)) {
			$words = preg_replace($patternG3, $replacementG3, $words);
		}
		if (preg_match($patternG4, $words)) {
			$words = preg_replace($patternG4, $replacementG4, $words);
		}
		if (preg_match($patternG5, $words)) {
			$words = preg_replace($patternG5, $replacementG5, $words);
		}
		if (preg_match($patternJ1, $words)) {
			$words = preg_replace($patternJ1, $replacementJ1, $words);
		}
		if (preg_match($patternJ2, $words)) {
			$words = preg_replace($patternJ2, $replacementJ2, $words);
		}
		if (preg_match($patternJ3, $words)) {
			$words = preg_replace($patternJ3, $replacementJ3, $words);
		}
		if (preg_match($patternJ4, $words)) {
			$words = preg_replace($patternJ4, $replacementJ4, $words);
		}
		if (preg_match($patternJ5, $words)) {
			$words = preg_replace($patternJ5, $replacementJ5, $words);
		}
		if (preg_match($patternJ6, $words)) {
			$words = preg_replace($patternJ6, $replacementJ6, $words);
		}
		if (preg_match($patternJ7, $words)) {
			$words = preg_replace($patternJ7, $replacementJ7, $words);
		}
		if (preg_match($patternJ8, $words)) {
			$words = preg_replace($patternJ8, $replacementJ8, $words);
		}
		if (preg_match($patternJ9, $words)) {
			$words = preg_replace($patternJ9, $replacementJ9, $words);
		}
		if (preg_match($patternJ10, $words)) {
			$words = preg_replace($patternJ10, $replacementJ10, $words);
		}
		if (preg_match($patternJ11, $words)) {
			$words = preg_replace($patternJ11, $replacementJ11, $words);
		}
		if (preg_match($patternT1, $words)) {
			$words = preg_replace($patternT1, $replacementT1, $words);
		}
		if (preg_match($patternT2, $words)) {
			$words = preg_replace($patternT2, $replacementT2, $words);
		}
		if (preg_match($patternT3, $words)) {
			$words = preg_replace($patternT3, $replacementT3, $words);
		}
		if (preg_match($patternT4, $words)) {
			$words = preg_replace($patternT4, $replacementT4, $words);
		}
		if (preg_match($patternT5, $words)) {
			$words = preg_replace($patternT5, $replacementT5, $words);
		}
	    if (preg_match($patternQO1, $words)) {
			$words = preg_replace($patternQO1, $replacementQO1, $words);
		}
	    if (preg_match($patternQO2, $words)) {
			$words = preg_replace($patternQO2, $replacementQO2, $words);
		}
	    if (preg_match($patternQO3, $words)) {
			$words = preg_replace($patternQO3, $replacementQO3, $words);
		}
	    if (preg_match($patternQO4, $words)) {
			$words = preg_replace($patternQO4, $replacementQO4, $words);
		}
	    if (preg_match($patternQO5, $words)) {
			$words = preg_replace($patternQO5, $replacementQO5, $words);
		}
	    if (preg_match($patternSi1, $words)) {
			$words = preg_replace($patternSi1, $replacementSi1, $words);
		}
	    if (preg_match($patternSi2, $words)) {
			$words = preg_replace($patternSi2, $replacementSi2, $words);
		}
	    if (preg_match($patternSi3, $words)) {
			$words = preg_replace($patternSi3, $replacementSi3, $words);
		}
	    if (preg_match($patternSi4, $words)) {
			$words = preg_replace($patternSi4, $replacementSi4, $words);
		}
	    if (preg_match($patternSi5, $words)) {
			$words = preg_replace($patternSi5, $replacementSi5, $words);
		}
	    if (preg_match($patternSi6, $words)) {
			$words = preg_replace($patternSi6, $replacementSi6, $words);
		}
	    if (preg_match($patternSi7, $words)) {
			$words = preg_replace($patternSi7, $replacementSi7, $words);
		}
	    if (preg_match($patternSi8, $words)) {
			$words = preg_replace($patternSi8, $replacementSi8, $words);
		}
	    if (preg_match($patternSi9, $words)) {
			$words = preg_replace($patternSi9, $replacementSi9, $words);
		}
	    if (preg_match($patternSi10, $words)) {
			$words = preg_replace($patternSi10, $replacementSi10, $words);
		}
	    if (preg_match($patternSi11, $words)) {
			$words = preg_replace($patternSi11, $replacementSi11, $words);
		}
	    if (preg_match($patternSi12, $words)) {
			$words = preg_replace($patternSi12, $replacementSi12, $words);
		}
	    if (preg_match($patternSi13, $words)) {
			$words = preg_replace($patternSi13, $replacementSi13, $words);
		}
	    if (preg_match($patternSi14, $words)) {
			$words = preg_replace($patternSi14, $replacementSi14, $words);
		}
	    if (preg_match($patternF1, $words)) {
			$words = preg_replace($patternF1, $replacementF1, $words);
		}
	    if (preg_match($patternF2, $words)) {
			$words = preg_replace($patternF2, $replacementF2, $words);
		}
	    if (preg_match($patternF3, $words)) {
			$words = preg_replace($patternF3, $replacementF3, $words);
		}
	    if (preg_match($patternF4, $words)) {
			$words = preg_replace($patternF4, $replacementF4, $words);
		}
	    if (preg_match($patternF5, $words)) {
			$words = preg_replace($patternF5, $replacementF5, $words);
		}
	    if (preg_match($patternF6, $words)) {
			$words = preg_replace($patternF6, $replacementF6, $words);
		}
		if (preg_match($patternF7, $words)) {
			$words = preg_replace($patternF7, $replacementF7, $words);
		}
		if (preg_match($patternF8, $words)) {
			$words = preg_replace($patternF8, $replacementF8, $words);
		}
		if (preg_match($patternF9, $words)) {
			$words = preg_replace($patternF9, $replacementF9, $words);
		}
		if (preg_match($patternF10, $words)) {
			$words = preg_replace($patternF10, $replacementF10, $words);
		}
		return $words;
	}
	
	public function loadEmotion($page = 1, $baseUrl = "/Public"){
		/*$patternS1 = '/\[呵呵\]/';
		$patternS2 = '/\[嘻嘻\]/';
		$patternS3 = '/\[哈哈\]/';
		$patternS4 = '/\[嘿嘿\]/';
		$patternS5 = '/\[赫赫\]/';
		$patternM1 = '/\[我晕\]/';
		$patternM2 = '/\[我靠\]/';
		$patternM3 = '/\[我擦\]/';
		$patternM4 = '/\[我勒个去\]/';
		$patternM5 = '/\[我吐\]/';
		$patternQ1 = '/\[害羞\]/';
		$patternQ2 = '/\[花心\]/';
		$patternQ3 = '/\[摆酷\]/';
		$patternQ4 = '/\[鼓掌\]/';
		$patternQ5 = '/\[亲亲\]/';
		$patternQ6 = '/\[可爱\]/';
		$patternQ7 = '/\[感动\]/';
		$patternQ8 = '/\[哭了\]/';
		$patternQ9 = '/\[鄙视\]/';
		$patternQ10 = '/\[挖鼻屎\]/';
		$patternQ11 = '/\[吐舌头\]/';
		$patternQ12 = '/\[耶\]/';
		
		$patternQO1 = '/\[可怜\]/';
		$patternQO2 = '/\[愤怒\]/';
		$patternQO3 = '/\[惊恐\]/';
		$patternQO4 = '/\[骂\]/';
		$patternQO5 = '/\[疑问\]/';
		
		
		$patternSi1 = '/\[吃货\]/';
		$patternSi2 = '/\[笑哈哈\]/';
		$patternSi3 = '/\[江南style\]/';
		$patternSi4 = '/\[飞个吻\]/';
		$patternSi5 = '/\[浮云\]/';
		$patternSi6 = '/\[给力\]/';
		$patternSi7 = '/\[围观\]/';
		$patternSi8 = '/\[威武\]/';
		$patternSi9 = '/\[奥特曼\]/';
		$patternSi10 = '/\[生病\]/';
		$patternSi11 = '/\[泪\]/';
		$patternSi12 = '/\[太开心\]/';
		$patternSi13 = '/\[good\]/';
		$patternSi14 = '/\[不要\]/';
		
		$patternG1 = '/\[鲜花\]/';
		$patternG2 = '/\[月亮\]/';
		$patternG3 = '/\[太阳\]/';
		$patternG4 = '/\[猪头\]/';
		$patternG5 = '/\[便便\]/';
		
		$patternF1 = '/\[圣诞\]/';
		$patternF2 = '/\[结婚罗\]/';
		$patternF3 = '/\[红包\]/';
		$patternF4 = '/\[爆竹声声\]/';
		$patternF5 = '/\[生日快乐\]/';
		$patternF6 = '/\[国旗\]/';
		

		$patternT1 = '/\[爱\]/';
		$patternT2 = '/\[兴奋\]/';
		$patternT3 = '/\[飞吻\]/';
		$patternT4 = '/\[安慰\]/';
		$patternT5 = '/\[扮鬼脸\]/';
		
		$patternJ1 = '/\[大爱\]/';
		$patternJ2 = '/\[享受\]/';
		$patternJ3 = '/\[扭屁股\]/';
		$patternJ4 = '/\[顶\]/';
		$patternJ5 = '/\[投降了\]/';
		$patternJ6 = '/\[撞砖\]/';
		$patternJ7 = '/\[揉脸\]/';
		$patternJ8 = '/\[瞌睡\]/';
		$patternJ9 = '/\[汗\]/';
		$patternJ10 = '/\[哦也\]/';
		$patternJ11 = '/\[啊！\]/';*/

		if ($page == 1) {
			$emotionString = "<li>";
			$emotionString .= '<img src="'.$baseUrl.'/image/emotion/s1.gif" title="呵呵" height="24" width="24" />';
			$emotionString .= '<img src="'.$baseUrl.'/image/emotion/s2.gif" title="嘻嘻" height="24" width="24" />';
			$emotionString .= '<img src="'.$baseUrl.'/image/emotion/s3.gif" title="哈哈" height="24" width="24" />';
			$emotionString .= '<img src="'.$baseUrl.'/image/emotion/s4.gif" title="嘿嘿" height="24" width="24" />';
			$emotionString .= '<img src="'.$baseUrl.'/image/emotion/s5.gif" title="赫赫" height="24" width="24" />';
			$emotionString .= '<img src="'.$baseUrl.'/image/emotion/m1.gif" title="我晕" height="24" width="24" />';
			$emotionString .= '<img src="'.$baseUrl.'/image/emotion/m2.gif" title="我靠" height="24" width="24" />';
			$emotionString .= '<img src="'.$baseUrl.'/image/emotion/m3.gif" title="我擦" height="24" width="24" />';
			$emotionString .= '<img src="'.$baseUrl.'/image/emotion/m4.gif" title="我勒个去" height="24" width="24" />';
			$emotionString .= '<img src="'.$baseUrl.'/image/emotion/m5.gif" title="我吐" height="24" width="24" />';
			$emotionString .= '<img src="'.$baseUrl.'/image/emotion/q1.gif" title="害羞" height="24" width="24" />';
			$emotionString .= '<img src="'.$baseUrl.'/image/emotion/q2.gif" title="花心" height="24" width="24" />';
			$emotionString .= '</li>';
			$emotionString .= '<li>';
			$emotionString .= '<img src="'.$baseUrl.'/image/emotion/q3.gif" title="摆酷" height="24" width="24" />';
			$emotionString .= '<img src="'.$baseUrl.'/image/emotion/q4.gif" title="鼓掌" height="24" width="24" />';
			$emotionString .= '<img src="'.$baseUrl.'/image/emotion/q5.gif" title="亲亲" height="24" width="24" />';
			$emotionString .= '<img src="'.$baseUrl.'/image/emotion/q6.gif" title="可爱" height="24" width="24" />';
			$emotionString .= '<img src="'.$baseUrl.'/image/emotion/q7.gif" title="感动" height="24" width="24" />';
			$emotionString .= '<img src="'.$baseUrl.'/image/emotion/q8.gif" title="哭了" height="24" width="24" />';
			$emotionString .= '<img src="'.$baseUrl.'/image/emotion/q9.gif" title="鄙视" height="24" width="24" />';
			$emotionString .= '<img src="'.$baseUrl.'/image/emotion/q10.gif" title="挖鼻屎" height="24" width="24" />';
			$emotionString .= '<img src="'.$baseUrl.'/image/emotion/q11.gif" title="吐舌头" height="24" width="24" />';
			$emotionString .= '<img src="'.$baseUrl.'/image/emotion/qo1.gif" title="可怜" height="24" width="24" />';
			$emotionString .= '<img src="'.$baseUrl.'/image/emotion/qo2.gif" title="愤怒" height="24" width="24" />';
			$emotionString .= '<img src="'.$baseUrl.'/image/emotion/qo3.gif" title="惊恐" height="24" width="24" />';
			$emotionString .= '</li>';
			$emotionString .= '<li>';
			$emotionString .= '<img src="'.$baseUrl.'/image/emotion/qo4.gif" title="骂" height="24" width="24" />';
			$emotionString .= '<img src="'.$baseUrl.'/image/emotion/qo5.gif" title="疑问" height="24" width="24" />';
			$emotionString .= '<img src="'.$baseUrl.'/image/emotion/q12.gif" title="耶" height="24" width="24" />';
			$emotionString .= '<img src="'.$baseUrl.'/image/emotion/si1.gif" title="吃货" height="22" width="22" />';
			$emotionString .= '<img src="'.$baseUrl.'/image/emotion/si2.gif" title="笑哈哈" height="22" width="22" />';
			$emotionString .= '<img src="'.$baseUrl.'/image/emotion/si3.gif" title="江南style" height="22" width="22" />';
			$emotionString .= '<img src="'.$baseUrl.'/image/emotion/si4.gif" title="飞个吻" height="22" width="22" />';
			$emotionString .= '<img src="'.$baseUrl.'/image/emotion/si5.gif" title="浮云" height="22" width="22" />';
			$emotionString .= '<img src="'.$baseUrl.'/image/emotion/si6.gif" title="给力" height="22" width="22" />';
			$emotionString .= '<img src="'.$baseUrl.'/image/emotion/si7.gif" title="围观" height="22" width="22" />';
			$emotionString .= '<img src="'.$baseUrl.'/image/emotion/si8.gif" title="威武" height="22" width="22" />';
			$emotionString .= '<img src="'.$baseUrl.'/image/emotion/si9.gif" title="奥特曼" height="22" width="22" />';
			$emotionString .= '</li>';
			$emotionString .= '<li>';
			$emotionString .= '<img src="'.$baseUrl.'/image/emotion/si10.gif" title="生病" height="22" width="22" />';
			$emotionString .= '<img src="'.$baseUrl.'/image/emotion/si11.gif" title="泪" height="22" width="22" />';
			$emotionString .= '<img src="'.$baseUrl.'/image/emotion/si12.gif" title="太开心" height="22" width="22" />';
			$emotionString .= '<img src="'.$baseUrl.'/image/emotion/si13.gif" title="good" height="22" width="22" />';
			$emotionString .= '<img src="'.$baseUrl.'/image/emotion/si14.gif" title="不要" height="22" width="22" />';
			$emotionString .= '<img src="'.$baseUrl.'/image/emotion/g1.gif" title="鲜花" height="22" width="22" />';
			$emotionString .= '<img src="'.$baseUrl.'/image/emotion/g2.gif" title="月亮" height="22" width="22" />';
			$emotionString .= '<img src="'.$baseUrl.'/image/emotion/g3.gif" title="太阳" height="22" width="22" />';
			$emotionString .= '<img src="'.$baseUrl.'/image/emotion/g4.gif" title="猪头" height="22" width="22" />';
			$emotionString .= '<img src="'.$baseUrl.'/image/emotion/g5.gif" title="便便" height="22" width="22" />';
			$emotionString .= '<img src="'.$baseUrl.'/image/emotion/f1.gif" title="圣诞" height="22" width="22" />';
			$emotionString .= '<img src="'.$baseUrl.'/image/emotion/f2.gif" title="结婚罗" height="22" width="22" />';
			$emotionString .= '</li>';
			$emotionString .= '<li>';
			$emotionString .= '<img src="'.$baseUrl.'/image/emotion/f3.gif" title="红包" height="22" width="22" />';
			$emotionString .= '<img src="'.$baseUrl.'/image/emotion/f4.gif" title="爆竹声声" height="22" width="22" />';
			$emotionString .= '<img src="'.$baseUrl.'/image/emotion/f5.gif" title="生日快乐" height="22" width="22" />';
			$emotionString .= '<img src="'.$baseUrl.'/image/emotion/f6.gif" title="国旗" height="22" width="22" />';
			$emotionString .= '<img src="'.$baseUrl.'/image/emotion/f7.gif" title="蜡烛" height="22" width="22" />';
			$emotionString .= '<img src="'.$baseUrl.'/image/emotion/f8.gif" title="熊猫" height="22" width="22" />';
			$emotionString .= '<img src="'.$baseUrl.'/image/emotion/f9.gif" title="偷乐" height="22" width="22" />';
			$emotionString .= '<img src="'.$baseUrl.'/image/emotion/f10.gif" title="ok" height="22" width="22" />';
			$emotionString .= '</li>';
		} else if ($page == 2) {
			$emotionString .= '<li>';
			$emotionString .= '<img src="'.$baseUrl.'/image/emotion/j1.gif" title="大爱" height="40" width="40" />';
			$emotionString .= '<img src="'.$baseUrl.'/image/emotion/j2.gif" title="享受" height="40" width="40" />';
			$emotionString .= '<img src="'.$baseUrl.'/image/emotion/j3.gif" title="扭屁股" height="40" width="40" />';
			$emotionString .= '<img src="'.$baseUrl.'/image/emotion/j4.gif" title="顶" height="40" width="40" />';
			$emotionString .= '<img src="'.$baseUrl.'/image/emotion/j5.gif" title="投降了" height="40" width="40" />';
			$emotionString .= '<img src="'.$baseUrl.'/image/emotion/j6.gif" title="撞砖" height="40" width="40" />';
			$emotionString .= '<img src="'.$baseUrl.'/image/emotion/j7.gif" title="揉脸" height="40" width="40" />';
			$emotionString .= '</li>';
			$emotionString .= '<li>';
			$emotionString .= '<img src="'.$baseUrl.'/image/emotion/j8.gif" title="瞌睡" height="40" width="40" />';
			$emotionString .= '<img src="'.$baseUrl.'/image/emotion/j9.gif" title="汗" height="40" width="40" />';
			$emotionString .= '<img src="'.$baseUrl.'/image/emotion/j10.gif" title="哦也" height="40" width="40" />';
			$emotionString .= '<img src="'.$baseUrl.'/image/emotion/j11.gif" title="啊！" height="40" width="40" />';
			$emotionString .= '<img src="'.$baseUrl.'/image/emotion/t1.gif" title="爱" height="35" width="36" />';
			$emotionString .= '<img src="'.$baseUrl.'/image/emotion/t2.gif" title="兴奋" height="29" width="36" />';
			$emotionString .= '<img src="'.$baseUrl.'/image/emotion/t3.gif" title="飞吻" height="31" width="38" />';
			$emotionString .= '</li>';
			$emotionString .= '<li>';
			$emotionString .= '<img src="'.$baseUrl.'/image/emotion/t4.gif" title="安慰" height="30" width="30" />';
			$emotionString .= '<img src="'.$baseUrl.'/image/emotion/t5.gif" title="扮鬼脸" height="27" width="34" />';
			$emotionString .= '</li>';
		}
		return $emotionString;
	}
}
?>