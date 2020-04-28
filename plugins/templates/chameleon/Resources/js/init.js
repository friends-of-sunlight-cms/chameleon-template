$(document).ready(function () {
    // detect device width
    var width = $(window).width();

    if (width < 450) {
        // add class
        $("body").addClass("mobile");
        // move column
        $('#left_sidebar').each(function () {
            $(this).insertAfter($(this).parent().find('#content'));
        });
    }
});