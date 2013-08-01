$().ready(function(){
    showtime();
    secondShine('#secondpass');
    var iconpositionwidth = $('.icon_timebar_in_div').width() - 35;
    $('#icon_timebar_img').animate({left: iconpositionwidth}, 800);
});
function showtime(){
	today = new Date(); 
	var hours = today.getHours(); 
	var minutes = today.getMinutes(); 
	var seconds = today.getSeconds();
	var timeValue = hours;
	timeValue += ((minutes < 10) ? ":0" : ":") + minutes+""; 
	timeValue += ((seconds < 10) ? ":0" : ":") + seconds+"";
	var timetext=timeValue
	document.getElementById("timenow").innerText = timetext;
	setTimeout('showtime()',1000);
}
function secondShine(name){
	$(name).fadeOut(400,function(){
	    var x2 = 0;
	    var y2 = 6;
	    var rand2 = parseInt(Math.random() * (x2 - y2 + 1) + y2);
	    if (rand2 == 1) {
	        $(name).css({color:"#F30"});
	    }
	    if (rand2 == 2) {
	        $(name).css({color:"#9C0"});
	    }
	    if (rand2 == 3) {
	        $(name).css({color:"#09F"});
	    }
	    if (rand2 == 4) {
	        $(name).css({color:"#FC0"});
	    }
	    if (rand2 == 5) {
	        $(name).css({color:"#999"});
	    }
	    var x1 = -8;
	    var y1 = 8;
	    var rand1 = parseInt(Math.random() * (x1 - y1 + 1) + y1);
	    
	    var x = 5;
	    var y = 25;
	    var rand = parseInt(Math.random() * (x - y + 1) + y);
	    
	    var x3 = 5;
	    var y3 = 18;
	    var rand3 = parseInt(Math.random() * (x3 - y3 + 1) + y3);
	    $(name).css({fontSize:"16px"});
	    $(name).css({marginLeft:"0"});
	    $(name).css({top:"0"});
		$(name).fadeIn(300);
		$(name).animate({marginLeft: rand + "px", fontSize: rand3 + "px", top: rand1 + "px"}, 300);
		secondShine(name);
	});
}