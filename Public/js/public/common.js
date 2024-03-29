$().ready(function () {

    /**
     * skin part
     */
    showSkin();
    flashPic('.message_shine');


    /**
     * quan && quan cancel
     */
    $(".do_quanta").live('click', function () {
        $this = $(this);
        var userid = $(".user_info_top_div").attr('userid');
        var $infoLoading = $('<img/>').attr({'src': baseUrl + 'Public/image/common/ajax_wait.gif', 'title': '提交中...请稍等'});
        var quaned_nums_common = $('#quaned_nums_common').html();
        $this.html($infoLoading);
        $.ajax({
            type: "POST",
            dataType: "json",
            url: baseUrl + "ajax/quanta",
            data: {uid: userid},
            success: function (msg) {
                if (msg.status == 'ok') {
                    $this.removeClass().addClass("btn_quaned do_quantacancel").html("已圈ta");
                    notice.send($('#data_touid').val(), userid);
                    var new_quaned_nums = parseInt(quaned_nums_common) + parseInt('1');
    				$('#quaned_nums_common').html(new_quaned_nums);
                } else {
                    ajaxInfo(msg.info);
                }
            }
        });
    });

    $(".do_quantacancel").live('click', function () {
        $this = $(this);
        var userid = $(".user_info_top_div").attr('userid');
        var $infoLoading = $('<img/>').attr({'src': baseUrl + 'Public/image/common/ajax_wait.gif', 'title': '提交中...请稍等'});
        var quaned_nums_common = $('#quaned_nums_common').html();
        $this.html($infoLoading);
        $.ajax({
            type: "POST",
            dataType: "json",
            url: baseUrl + "ajax/quantacancel",
            data: {uid: userid},
            success: function (msg) {
                if (msg.status == 'ok') {
                    $this.removeClass().addClass("btn_quan do_quanta").html("<span class='icon_plus'></span>圈ta");
                    var new_quaned_nums = parseInt(quaned_nums_common) - parseInt('1');
    				$('#quaned_nums_common').html(new_quaned_nums);
                } else {
                    ajaxInfo(msg.info);
                }
            }
        });
    });
    $('#message_talk_nums_div').click(function(){
        $(this).hide();
    });
    /**
     * nav hover
     */
    $("#header_nav_user").hover(
    	function () {
            var mainoffset = $('.main').offset();
            if (mainoffset != null) {
                var mainpositionleft = mainoffset.left + 173;
                if ("6.0" == $.browser.version) {
                	var mainpositionleft = mainoffset.left;
                }
            } else {
                var mainhelpoffset = $('.main_help').offset();
                var mainpositionleft = mainhelpoffset.left + 173;
                if ("6.0" == $.browser.version) {
                	var mainpositionleft = mainhelpoffset.left;
                }
            }
            $("#nav_hover_list_div").css({left: mainpositionleft}).slideDown("fast");
            $("#nav_hover_list_div_ul").html("<li><a href='/index/mate' class='nav_hover_list_div_ul_li_a'><i class='icon_hover_top_mate'></i>同学</a> " +
            	"<a href='/index/mate?w=grade&n=994' class='nav_hover_list_div_ul_li_a'><i class='icon_hover_top_teacher'></i>老师</a>" +
                "<a href='/index/mate?w=grade&n=995' class='nav_hover_list_div_ul_li_a'><i class='icon_hover_top_postgraduate'></i>研究生</a>" +
            	"<a href='/index/group' class='nav_hover_list_div_ul_li_a'><i class='icon_hover_top_group'></i>校园组织</a> " +
            	"<a href='/index/business' class='nav_hover_list_div_ul_li_a'><i class='icon_hover_top_business'></i>周边商家</a>" +
            	"</li>"
            );
        },
        function () {
            $("#nav_hover_list_div").hover(function () {
                $("#header_nav_more").removeClass('header_left_arrow_hover');
                $("#header_nav_user").addClass('header_left_arrow_hover');
                $("#nav_hover_list_div").show();
            }, function () {
                $("#header_nav_user").removeClass('header_left_arrow_hover');
                $("#nav_hover_list_div").slideUp("fast");
            }
        );
    });
    $("#header_nav_more").hover(
        function () {
            var mainoffset = $('.main').offset();
            if (mainoffset != null) {
                var mainpositionleft = mainoffset.left + 173;
                if ("6.0" == $.browser.version) {
                	var mainpositionleft = mainoffset.left;
                }
            } else {
                var mainhelpoffset = $('.main_help').offset();
                var mainpositionleft = mainhelpoffset.left + 173;
                if ("6.0" == $.browser.version) {
                	var mainpositionleft = mainhelpoffset.left;
                }
            }
            $("#nav_hover_list_div").css({left: mainpositionleft}).slideDown("fast");
            $("#nav_hover_list_div_ul").html("<li><a href='/index/hot?w=hit&t=week' class='nav_hover_list_div_ul_li_a'><i class='icon_hover_top_ranking'></i>排行</a> <a href='/activity' class='nav_hover_list_div_ul_li_a'><i class='icon_hover_top_activity'></i>活动</a> <a href='/index/app' class='nav_hover_list_div_ul_li_a'><i class='icon_hover_top_app'></i>App应用</a></li>");
        },
        function () {
            $("#nav_hover_list_div").hover(
            	function () {
	                $("#header_nav_user").removeClass('header_left_arrow_hover');
	                $("#header_nav_more").addClass('header_left_arrow_hover');
	                $("#nav_hover_list_div").show();
            	},
            	function () {
	                $("#header_nav_more").removeClass('header_left_arrow_hover');
	                $("#nav_hover_list_div").slideUp("fast");
            	}
            );
        }
    );
    $(".nav_hover_up").hover(
        function () {
            $("#nav_hover_list_div").slideUp("fast");
        },
        function () {
        }
    );
    $("body").mouseleave(function () {
        $("#header_nav_more").removeClass('header_left_arrow_hover');
        $("#nav_hover_list_div").slideUp("fast");
    });

    /**
     * pull message once
     */
    mseeageNumsOnce();

    /**
     * chage online status
     */
    $('#header_online_status').live("click", function () {
        var val_online = $(this).attr("value");
        $.ajax({
            type: "POST",
            url: baseUrl + "ajax/changeonlinestatus",
            data: "val_online=" + val_online,
            dataType: "json",
            success: function (msg) {
                if (msg.status == 'yes') {
                    if (msg.data == 1) {
                        $('#header_online_status').attr({ value: '1', title: '正常，点击切换为潜水状态'}).html('[在线]');
                    } else if (msg.data == 2) {
                        $('#header_online_status').attr({ value: '2', title: '潜水，点击切换为正常在线状态'}).html('[潜水]');
                    }
                } else {
                    alert(msg.info);
                }
            }
        });
    });

    /**
     * ajax info div position
     */
    var mainoffset = $('.main').offset();
    if (mainoffset != null) {
        var mainpositionleft = mainoffset.left + 330;
        $("#ajax_info_div").css({left: mainpositionleft});
        $("#ajax_info_div_close").live("click", function () {
            $("#ajax_info_div").fadeOut("fast");
            $("#ajax_info_div_outer").hide();
        });
    }

    /**
     * header search
     */
    $("#inputSearchBox").focus(function () {
        var textareaValue = $(this).val();
        if (textareaValue == '找人，找信息') {
            $(this).val('');
        }
    });
    $("#inputSearchBox").focusout(function () {
        var textareaValue = $(this).val();
        if (textareaValue == '') {
            $(this).val('找人，找信息');
        }
    });
    $("#inputSearchButton").click(function () {
        var searchWords = $("#inputSearchBox").val();
        window.location = baseUrl + "mutual/find?username=" + searchWords;
    });

    /**
     * get userinfo
     */
    var t_userinfo;
    $(".getuserinfo").live('mouseover',function (e) {
        $this = $(this);
        t_userinfo = setTimeout(function () {
            var userid = $this.attr('userid');
            var usernickname = $this.text();
            var positionleft = e.pageX + 10;
            var positiontop = e.pageY + 10;
            $.ajax({
                type: "POST",
                dataType: "json",
                url: baseUrl + "ajax/getuserinfo",
                data: {userid: userid, usernickname: usernickname},
                success: function (msg) {
                    if (!msg.status) {
                        var inhtml = "<span class='red_l'>" + msg.info + "</span>";
                        $('.user_info_div').css({ position: "absolute", left: positionleft, top: positiontop }).fadeIn('fast').html(inhtml);
                    } else {
                        if (msg.data.sex == 1) {
                            if (msg.data.user_relation == 'priority') {
                                var relationhtml = "<a class='btn_quaned do_quantacancel' title='取消圈他'>已圈他</a>";
                            } else if (msg.data.user_relation == 'shield') {
                                var relationhtml = "<a href='" + baseUrl + "wo/" + msg.data.uid + "' class='btn_quaned' target='_blank'>已屏蔽他</a>";
                            } else {
                                var relationhtml = "<a class='btn_quan do_quanta'><span class='icon_plus'></span>圈他</a>";
                            }
                        } else {
                            if (msg.data.user_relation == 'priority') {
                                var relationhtml = "<a class='btn_quaned do_quantacancel' title='取消圈她'>已圈她</a>";
                            } else if (msg.data.user_relation == 'shield') {
                                var relationhtml = "<a href='" + baseUrl + "wo/" + msg.data.uid + "' class='btn_quaned' target='_blank'>已屏蔽她</a>";
                            } else {
                                var relationhtml = "<a class='btn_quan do_quanta'><span class='icon_plus'></span>圈她</a>";
                            }
                        }
                        if (msg.data.remark != null) {
                            var userremarkhtml = "<a class='f12' id='user_remark_set' title='点击修改备注'>(" + msg.data.remark + ")</a>";
                        } else {
                            var userremarkhtml = "<a class='f12' id='user_remark_set' title='点击设置备注'>(备注)</a>";
                        }
                        if (msg.data.constellation != null) {
                            var userconstellation = msg.data.constellation;
                        } else {
                            var userconstellation = '';
                        }
                        var inhtml = "<div class='user_info_top_div' userid='" + msg.data.uid + "'>"
                            + "		  <a class='user_info_top_div_img_a' href='" + baseUrl + "wo/" + msg.data.uid + "' target='_blank'>"
                            + "		    <img width='60' height='45' src='" + msg.data.icon_url + "' />"
                            + "		    <span class='online" + msg.data.online + "'></span></a>"
                            + "		  <p class='user_info_top_div_nickname_p'><a href='" + baseUrl + "wo/" + msg.data.uid + "' class='f14 fb' target='_blank'>" + msg.data.nickname + "</a> " + userremarkhtml + "&nbsp;<span class='level" + msg.data.degree + "'></span></p>"
                            + "       <p class='user_info_top_div_quan_p black_l'>圈的:<span class='fb f14'>" + msg.data.follow + "</span> 圈子:<span class='fb f14' id='quaned_nums_common'>" + msg.data.fans + "</span> <span class='user_info_top_div_quan_p_do'>" + relationhtml + "&nbsp;<a href='/talk/to/" + msg.data.uid + "' class='btn_quaned'>悄悄话</a></span></p>"
                            + "       <span class='user_info_span_constellation'>" + userconstellation + "<span class='sex" + msg.data.sex + "'></span></span>"
                            + "		</div>"
                            + "		<div class='user_info_main_div'>"
                            + "			<ul>";
                        if (msg.data.schoolname != null) {
                            inhtml += "<li>学院: <a target='_blank' href='" + msg.data.domain + "index/mate?w=academy&n=" + msg.data.academy_id + "'>" + msg.data.academy + "</a> (<a target='_blank' href='" + msg.data.domain + "'>" + msg.data.schoolname + "</a>)</li>";
                        } else {
                            inhtml += "<li>学院: <a target='_blank' href='" + msg.data.domain + "index/mate?w=academy&n=" + msg.data.academy_id + "'>" + msg.data.academy + "</a></li>";
                        }
                        inhtml += "<li>专业: <a target='_blank' href='" + msg.data.domain + "index/mate?w=academy&n=" + msg.data.academy_id + "&specialty=" + msg.data.specialty_id + "'>" + msg.data.specialty + "</a></li>"
                            + "             <li>寝室: <a target='_blank' href='" + msg.data.domain + "index/mate?w=dormitory&n=" + msg.data.dormitory_id + "'>" + msg.data.dormitory + "</a></li>"
                            + "             <li>" + msg.data.introduction + "</li>"
                            + "			</ul>"
                            + "			<span class='close_x user_info_div_close' title='关闭'>×</span>"
                            + "		</div>";
                        $('.user_info_div').css({ position: "absolute", left: positionleft, top: positiontop }).fadeIn('fast').html(inhtml);
                        return false;
                    }
                }
            });
        }, 1000);
    }).mouseleave(function (e) {
        clearTimeout(t_userinfo);
        $('.user_info_div').hover(
        	function () {
            },
            function () {
                $(this).fadeOut("fast");
            }
        );
    });
    $(".user_info_div_close").live("click", function () {
        $(".user_info_div").fadeOut("fast");
    });

    /**
     * remark
     */
    $('#user_remark_set').live('click', function () {
        var inputremarkhtml = "<p class='newremarkname_p'><input type='text' id='newremarkname' class='input_style' /> <a id='user_remark_submit' class='btn f12'>确定</a></p>";
        $('.user_info_main_div').html(inputremarkhtml);
    });
    $('#user_remark_submit').live('click', function () {
        var newremarkname = $('#newremarkname').val();
        var newuserid = $('.user_info_top_div').attr('userid');
        if (newremarkname == '') {
            var newremarknameinfo = '已清空备注';
        } else {
            var newremarknameinfo = newremarkname;
        }
        $.ajax({
            type: "POST",
            dataType: "json",
            url: baseUrl + "ajax/newremark",
            data: {newuserid: newuserid, newremarkname: newremarkname},
            success: function (msg) {
                if (msg.status == '1') {
                    $('.user_info_main_div').html("<p class='newremarkname_p'><span class='icon_right'></span>更新备注成功</p>");
                    $('#user_remark_set').html("(" + newremarknameinfo + ")");
                } else if (msg.status == '2') {
                    $('.user_info_main_div').html("<p class='newremarkname_p'><span class='icon_right'></span>备注成功</p>");
                    $('#user_remark_set').html("(" + newremarknameinfo + ")");
                } else {
                    $('.user_info_main_div').html("<p class='newremarkname_p'><span class='icon_wrong'></span>备注失败 稍后再试</p>");
                }
            }
        });
    });

    $("body").click(function () {
        $('.record_plus_div').hide();
    });
});

function flashPic(name) {
    $(name).fadeOut('slow', function () {
        $(this).fadeIn('fast', function () {
            $(this).fadeOut('slow', function () {
                $(this).fadeIn('fast', function () {
                    $(this).fadeOut('slow', function () {
                        $(this).fadeIn('fast', function () {
                            $(this).fadeOut('slow', function () {
                                $(this).fadeIn('fast');
                            });
                        });
                    });
                });
            });
        });
    });
}
function ajaxInfo(htmlobj) {
    $("#ajax_info_div_outer").show();
    $("#ajax_info_div").fadeIn('fast');
    $("#ajax_info_div_msg").fadeIn('fast').html(htmlobj);
}
function mseeageNumsOnce() {
    $.ajax({
        type: "POST",
        url: baseUrl + "ajax/getmessage",
        global: false,
        data: {acquireseconds: 'default'},
        dataType: "json",
        success: function (msg) {
            if (msg.status == 'ok') {
                var acquiremilliseconds = msg.data.acquireSeconds;
                if (msg.data.messageSystemNums != 0) {
                    $('#message_system_nums_a').show();
                    $('#message_system_nums_a').children('span').html('+' + msg.data.messageSystemNums);
                } else {
                    $('#message_system_nums_a').fadeOut('fast');
                }
                if (msg.data.messageCommentNums != 0) {
                    $('#message_comment_nums_a').show();
                    $('#message_comment_nums_a').children('span').html('+' + msg.data.messageCommentNums);
                } else {
                    $('#message_comment_nums_a').fadeOut('fast');
                }
                if (msg.data.messageAtNums != 0) {
                    $('#message_at_nums_a').show();
                    $('#message_at_nums_a').children('span').html('@ +' + msg.data.messageAtNums);
                } else {
                    $('#message_at_nums_a').fadeOut('fast');
                }
                if (msg.data.messageTalkNums != 0) {
                    $('#message_talk_nums_div').fadeIn('fast');
                    $('#message_talk_nums_img_icon').show().attr({'src': msg.data.lastTalkContentUserImg, 'title': msg.data.lastTalkContentUserNickname});
                    $('#message_talk_nums_span_content').html(msg.data.lastTalkContent);
                    $('#message_talk_nums_p_content_info').html('来自' + msg.data.lastTalkContentUserNickname + '...等的 <span class="f12 fb blue"> ' + msg.data.messageTalkNums + '</span>条悄悄话');
                    $('.message_talk_to_url').attr({ href: baseUrl + "talk/to/" + msg.data.lastTalkContentUserId });
                } else {
                    $('#message_talk_nums_div').fadeOut('fast');
                }
            }
        },
        timeout: 10000,
        error: function () {
            $('#message_talk_nums_div').fadeIn('fast');
            $('#message_talk_nums_img_icon').hide();
            $('#message_talk_nums_span_content').html('<span class="red_l f12">圈圈亲，系统检测到您断网了!</span>');
            $('#message_talk_nums_p_content_info').html('');
            $('.message_talk_to_url').attr({ href: "", 'title': '与我帮圈圈服务器失去连接 :(' });
            setTimeout('mseeageNumsOnce()', 1000);
        }
    });
    $('#message_talk_nums_span_close').click(function () {
        $('#message_talk_nums_div').fadeOut('fast');
    });
}

function showSkin() {
    var $valofskin = $("#headerskinvalue").attr("value");
    var $changeheader = $(".header");
    var $changemain = $(".main");
    var $changelay_background = $("#layBackground");
    var $changebody = $("body");
    if ($valofskin == '0') {
        $changeheader.removeClass();
        $changemain.removeClass();
        $changelay_background.removeClass();
        $changebody.removeClass();
        $changeheader.addClass("header");
        $changemain.addClass("main");
        $changelay_background.addClass("lay_background");
        $changebody.addClass("body");
    } else if ($valofskin == '1') {
        $changeheader.removeClass();
        $changemain.removeClass();
        $changelay_background.removeClass();
        $changebody.removeClass();
        $changeheader.addClass("header header_1");
        $changemain.addClass("main main_1");
        $changelay_background.addClass("lay_background_1");
        $changebody.addClass("body_1");
    } else if ($valofskin == '2') {
        $changeheader.removeClass();
        $changemain.removeClass();
        $changelay_background.removeClass();
        $changebody.removeClass();
        $changeheader.addClass("header header_2");
        $changemain.addClass("main main_2");
        $changelay_background.addClass("lay_background_2");
        $changebody.addClass("body_2");
    } else if ($valofskin == '3') {
        $changeheader.removeClass();
        $changemain.removeClass();
        $changelay_background.removeClass();
        $changebody.removeClass();
        $changeheader.addClass("header header_3");
        $changemain.addClass("main main_3");
        $changelay_background.addClass("lay_background_3");
        $changebody.addClass("body_3");
    } else if ($valofskin == '4') {
        $changeheader.removeClass();
        $changemain.removeClass();
        $changelay_background.removeClass();
        $changebody.removeClass();
        $changeheader.addClass("header header_4");
        $changemain.addClass("main main_4");
        $changelay_background.addClass("lay_background_4");
        $changebody.addClass("body_4");
    } else if ($valofskin == '5') {
        $changeheader.removeClass();
        $changemain.removeClass();
        $changelay_background.removeClass();
        $changebody.removeClass();
        $changeheader.addClass("header header_5");
        $changemain.addClass("main main_5");
        $changelay_background.addClass("lay_background_5");
        $changebody.addClass("body_5");
    } else if ($valofskin == '6') {
        $changeheader.removeClass();
        $changemain.removeClass();
        $changelay_background.removeClass();
        $changebody.removeClass();
        $changeheader.addClass("header header_6");
        $changemain.addClass("main main_6");
        $changelay_background.addClass("lay_background_6");
        $changebody.addClass("body_6");
    } else if ($valofskin == '7') {
        $changeheader.removeClass();
        $changemain.removeClass();
        $changelay_background.removeClass();
        $changebody.removeClass();
        $changeheader.addClass("header header_7");
        $changemain.addClass("main main_7");
        $changelay_background.addClass("lay_background_7");
        $changebody.addClass("body_7");
    }
}

