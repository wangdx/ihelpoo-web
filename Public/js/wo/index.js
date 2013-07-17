$().ready(function(){
    /**
     * enlarge image
     */
    var imageTempContent = '';
    $('.s_li_p_content_image img').live('click', function(){
    	var enlargeSwitch = $(this).attr('enlargeswitch');
    	if (enlargeSwitch != 'on') {
	    	var imageurl = $(this).attr('src');
	    	if (imageurl.match("ihelpoo-public") != '') {
	    		var reg = new RegExp("thumb_","g");
	    		var imageurllarge = imageurl.replace(reg,"");
	    	}
	    	imageTempContent = $(this).parent().html();
	    	$(this).parent().html('<p class="f12 s_li_p_content_image_title"><a href="'+imageurllarge+'" target="_blank"><span class="icon_plus"></span>查看原图</a> <a class="s_li_p_content_image_title_up"><span class="icon_up"></span>收起</a></p><img src="'+imageurllarge+'" class="s_li_p_content_image_p_img" enlargeswitch="on"/></p>');
    	}
    });
    $('.s_li_p_content_image_title_up').live('click', function(){
	    $(this).parent().parent().html(imageTempContent);
	});

    /**
     * video play
     */
    $('.s_li_p_content_mv_img_p').click(function(){
    	$(this).hide();
    	$(this).parent().find('.s_li_p_content_mv_object_p').slideDown('fast');
    });

    $('.s_li_p_content_mv_object_up').click(function(){
    	$(this).parent().hide();
    	$('.s_li_p_content_mv_img_p').show();
    });

    /**
     * get userinfo
     */
    var t;
    $(".getuserinfo").mouseenter(function(e){
    	$this = $(this);
    	t=setTimeout(function(){
    		var userid = $this.attr('userid');
    		var usernickname = $this.text();
    		var positionleft = e.pageX + 10;
        	var positiontop = e.pageY + 10;
    		$.ajax({
                type: "POST",
        		dataType: "json",
        		url: baseUrl + "ajax/getuserinfo",
        		data:{userid: userid, usernickname: usernickname},
        		success:function(msg){
                	if (!msg.status) {
                		var inhtml = "<span class='red_l'>"+ msg.info + "</span>";
                		$('.user_info_div').css({ position: "absolute", left: positionleft, top: positiontop }).fadeIn('fast').html(inhtml);
    				} else {
    					var inhtml = "<div class='user_info_top_div'>"
    						+ "		  <a class='user_info_top_div_img_a' href='"+baseUrl+"stream/u/"+msg.data.uid+"' target='_blank'>"
    						+ "		    <img width='60' height='45' src='"+msg.data.icon_url+"' />"
    						+ "		    <span class='online"+msg.data.online+"'></span></a>"
    						+ "		  <p class='user_info_top_div_nickname_p'><a href='"+baseUrl+"wo/"+msg.data.uid+"' class='f14 fb' target='_blank'>"+msg.data.nickname+"</a> <span class='gray'>("+msg.data.type+")</span> <span class='level"+msg.data.degree+"'></span></p>"
    						+ "       <p class='user_info_top_div_quan_p black_l'>圈的:<span class='fb f14'>"+msg.data.follow+"</span> 圈子:<span class='fb f14'>"+msg.data.fans+"</span> "+msg.data.constellation+"<span class='sex"+msg.data.sex+"'></span></p>"
    						+ "		</div>"
    						+ "		<div class='user_info_main_div'>"
    						+ "			<ul>"
    						+ "             <li>学院: <a target='_blank' href='"+baseUrl+"index/mate?w=academy&n="+msg.data.academy_id+"'>"+msg.data.academy+"</a></li>"
    						+ "             <li>专业: <a target='_blank' href='"+baseUrl+"index/mate?w=academy&n="+msg.data.academy_id+"&specialty="+msg.data.specialty_id+"'>"+msg.data.specialty+"</a></li>"
    						+ "             <li>寝室: <a target='_blank' href='"+baseUrl+"index/mate?w=dormitory&n="+msg.data.dormitory_id+"'>"+msg.data.dormitory+"</a></li>"
    						+ "             <li>"+msg.data.introduction+"</li>"
    						+ "			</ul>"
    						+ "		</div>"
    					$('.user_info_div').css({ position: "absolute", left: positionleft, top: positiontop }).fadeIn('fast').html(inhtml);
    					return false;
    				}
                }
            });
    	},1000);
    }).mouseleave(function(){
    	clearTimeout(t);
    	$('.user_info_div').hover(function(){},
    	function(){
    		$(this).fadeOut("slow");
    	});
    });
});