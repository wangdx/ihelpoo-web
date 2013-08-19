$().ready(function(){
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
});