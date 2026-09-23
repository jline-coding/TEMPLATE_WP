(function ($) {
    const $window = $(window);
    const $document = $(document);

    $window.on("load", function () {
        $(".js-mCustomScrollbar").mCustomScrollbar();
    });

    $(function () {
        const selectSelector = '.c-select, .smf-select-control';

        // Toggle .is-active on select wrappers (.c-select and .smf-select-control)
        $document.on('click', selectSelector, function (e) {
            e.stopPropagation();
            const $this = $(this);
            $(selectSelector).not($this).removeClass('is-active');
            $this.toggleClass('is-active');
        });

        $document.on('click', function () {
            $(selectSelector).removeClass('is-active');
        });

        $document.on('change blur', '.c-select select, .smf-select-control select', function () {
            $(this).closest(selectSelector).removeClass('is-active');
        });

        const defaultText = '添付する';

        $('.js-file input[type="file"]').on('change', function () {
            const file = this.files[0];
            const $this = $(this);
            const $wrapper = $this.closest('.js-file');
            const $content = $wrapper.find('.js-file__content');
            const $clearBtn = $wrapper.find('.js-file-clear');

            if (file) {
                $content.text(file.name).addClass('is-active');
                $clearBtn.css('display', 'inline-flex');
            } else {
                $content.text(defaultText).removeClass('is-active');
                $clearBtn.hide();
            }
        });

        $('.js-file-clear').on('click', function () {
            const $this = $(this);
            const $wrapper = $this.closest('.js-file');
            const $input = $wrapper.find('input[type="file"]');
            const $content = $wrapper.find('.js-file__content');

            $input.val('');
            $content.text(defaultText).removeClass('is-active');
            $this.hide();
        });
    });
   
    $(function () {
        if (typeof $.fn.datepicker !== 'undefined' && $.datepicker) {
            $.datepicker.regional['ja'] = {
                closeText: '閉じる',
                prevText: '&#x3c;前',
                nextText: '次&#x3e;',
                currentText: '今日',
                monthNames: ['1月','2月','3月','4月','5月','6月',
                '7月','8月','9月','10月','11月','12月'],
                monthNamesShort: ['1月','2月','3月','4月','5月','6月',
                '7月','8月','9月','10月','11月','12月'],
                dayNames: ['日曜日','月曜日','火曜日','水曜日','木曜日','金曜日','土曜日'],
                dayNamesShort: ['日','月','火','水','木','金','土'],
                dayNamesMin: ['日','月','火','水','木','金','土'],
                weekHeader: '週',
                dateFormat: 'yy/mm/dd',
                firstDay: 0,
                isRTL: false,
                showMonthAfterYear: true,
                yearSuffix: '年'
            };

            $.datepicker.setDefaults($.datepicker.regional['ja']);

            const $datePicker = $('.js-datepicker');
            if ($datePicker.length) {
                $datePicker.datepicker();
            }
        }
    });

})(jQuery);
