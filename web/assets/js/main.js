(function () {
    $(document).ready(function() {
        $('.alert-dismissible').delay(3000).fadeOut(500, function () {
            $(this).remove();
        });

        $('.js-select-advanced').select2();

        $('.date-advanced').datetimepicker({
            format: 'YYYY-MM-DD'
        });

        $('form').on('submit', function () {
            var $btn = $(this).find("button, input[type=submit], a");
            $btn.data('loading-text', '<i class="fa fa-refresh fa-spin"></i>');
            $btn.button('loading');
        })
    });
})();
