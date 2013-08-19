$().ready(function(){
    $('.user_show_p').hover(
        function(){
            $(this).animate({ top: "-=10" }, 300);
        },
        function(){
            $(this).animate({ top: "+=10" }, 300);
        }
    );
});