$().ready(function(){
	$('.main_scorll_div').animate({scrollLeft: 500}, 1000).animate({scrollTop: 300}, 1000);
	$('.fast_move_map').click(function(){
		var area = $(this).attr('value');
		$('.fast_move_map').removeClass('btn');
		$(this).addClass('btn');
		if (area == '1') {
			$('.main_scorll_div').animate({scrollLeft: 50}, 1000).animate({scrollTop: 50}, 1000);
		} else if (area == '2') {
			$('.main_scorll_div').animate({scrollLeft: 800}, 1000).animate({scrollTop: 0}, 1000);
		} else if (area == '3') {
			$('.main_scorll_div').animate({scrollLeft: 600}, 1000).animate({scrollTop: 500}, 1000);
		} else if (area == '4') {
			$('.main_scorll_div').animate({scrollLeft: 100}, 1000).animate({scrollTop: 600}, 1000);
		}
	});
    $('.user_show_p').hover(
        function(){
            $(this).css({zIndex: '1112'});
            $(this).animate({ top: "-=30" }, 500);
        },
        function(){
            $(this).css({zIndex: '999'});
            $(this).animate({ top: "+=30" }, 500);
        }
    );
    
    var t;
    $(".white").mouseenter(function(e){
    	$this = $(this);
    	t=setTimeout(function(){
    		var domitoryname = $this.text();
    		var $testinfo = "<br/><br/><span class='dormitoryinfo'>点击进入 "+domitoryname+"</span>";
    		$this.after($testinfo);
    	},500);
    }).mouseleave(function(){
    	clearTimeout(t);
    	$('.dormitoryinfo').fadeOut("slow");
    });
});