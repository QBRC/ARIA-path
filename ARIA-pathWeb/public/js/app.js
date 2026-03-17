$(function () {
    $('[data-toggle="tooltip"]').tooltip()
});

window.setTimeout(function() {
    $(".flash").fadeTo(500, 0).slideUp(500, function(){
        $(this).remove();
    });
}, 5000);
