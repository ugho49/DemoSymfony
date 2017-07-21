(function () {
    $(document).ready(function() {
        alertDismissibleEvent();
        select2Event();
        dateTimePickerEvent();
        submitFormsEvent();
        timeago();
        setInterval(timeago, 60 * 1000); // every minutes
    });

    const timeago = function () {
        $('.timeago').html(function () {
            return moment($(this).data('time'), "YYYY-MM-DD HH:mm:ss").fromNow();
        });
    };

    const submitFormsEvent = function () {
        $('form').on('submit', function () {
            var $btn = $(this).find("button, input[type=submit], a");
            $btn.data('loading-text', '<i class="fa fa-refresh fa-spin"></i>&nbsp;Loading...');
            $btn.button('loading');
        })
    };

    const dateTimePickerEvent = function () {
        $('.date-advanced').datetimepicker({
            format: 'YYYY-MM-DD'
        });
    };

    const select2Event = function () {
        $('.js-select-advanced').select2();
    };

    const alertDismissibleEvent = function () {
        $('.alert-dismissible').delay(3000).fadeOut(500, function () {
            $(this).remove();
        });
    };
})();
