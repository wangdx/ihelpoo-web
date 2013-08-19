$().ready(function(){
    $('.user_show_p').hover(
        function(){
            $(this).animate({ top: "-=30" }, 500);
        },
        function(){
            $(this).animate({ top: "+=30" }, 500);
        }
    );
});