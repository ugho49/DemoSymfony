(function () {
    $(document).ready(function() {
        $('.alert-dismissible').delay(3000).fadeOut(500, function () {
            $(this).remove();
        });

        $('.js-select-advanced').select2();

        $('.date-advanced').datetimepicker({
            format: 'YYYY-MM-DD'
        });
    });
})();
