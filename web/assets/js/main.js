$(document).ready(function() {
    $('.alert-dismissible').delay(3000).fadeOut(500, function () {
        $(this).remove();
    });
});