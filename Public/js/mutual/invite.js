$().ready(function(){
    $('#invite_one_a').click(function(){
    	$('#invite_one_part').css({background:"#FFFA85"});
    	$('#invite_two_part').css({background:"#FFF"});
    	$('#invite_three_part').css({background:"#FFF"});
    });
    $('#invite_two_a').click(function(){
    	$('#invite_two_part').css({background:"#FFFA85"});
    	$('#invite_one_part').css({background:"#FFF"});
    	$('#invite_three_part').css({background:"#FFF"});
    });
    $('#invite_three_a').click(function(){
    	$('#invite_three_part').css({background:"#FFFA85"});
    	$('#invite_two_part').css({background:"#FFF"});
    	$('#invite_one_part').css({background:"#FFF"});
    });
});

