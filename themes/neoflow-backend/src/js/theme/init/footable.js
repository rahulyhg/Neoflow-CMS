$(function () {

    var FootableReadyCallbacks = [
        function (e, ft) {
            $('.footable-filtering', ft.$el).each(function () {
                $('.form-inline', this).addClass('d-flex justify-content-end');
            });
        },
        function (e, ft) {
            $('tbody td', ft.$el).each(function () {
                $('a[disabled]', this).preventDefaultClickBehaviour();
                $('.confirm-modal', this).confirmModalClickListener();
                $('.alert-modal', this).alertModalClickListener();
            });
        },
        function (e, ft) {
            $('.footable-paging', ft.$el).each(function () {
                $('.footable-page', this).addClass('page-item');
                $('.footable-page-link', this).addClass('page-link');
                $('.divider, .label', this).remove();
            });
        }
    ];

    $('.footable')
            .on('preinit.ft.table', function (e, ft) {
                console.time('footable init');
                ft.$loader.css({
                    minHeight: $(e.currentTarget).outerHeight(true) + 55
                });
            })
            .on('ready.ft.table', function (e, ft) {
                FootableReadyCallbacks.forEach(function (callback) {
                    callback(e, ft);
                });
                console.timeEnd('footable init');
            })
            .footable({
                'xs': 0, // extra small
                'sm': 648, // small
                'md': 768, // medium
                'lg': 992, // large
                paging: {
                    enabled: true,
                    countFormat: '{CP} / {TP}',
                    size: 8,
                },
//                state: {
//                    enabled: true
//                },
                filtering: {
                    enabled: true,
                    delay: 0,
                },
                sorting: {
                    enabled: true,
                }
            });

});