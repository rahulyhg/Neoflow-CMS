$.fn.dataTableExt.oStdClasses.sWrapper = 'dataTables_wrapper dt-bootstrap4';
$.fn.dataTableExt.oStdClasses.sFilterInput = 'form-control';
$.fn.dataTableExt.oStdClasses.sLengthSelect = 'form-control';

$('.datatable').each(function () {
    var $dataTable = $(this);

    var orderIndex = 0,
            $defaultOrderedColumn = $dataTable.find('[data-order="true"]:first');
    if ($defaultOrderedColumn.length > 0) {
        orderIndex = $dataTable.find('[data-order="true"]:first').index();
    }

    var isResponsive = false;
    if ($dataTable.hasClass('responsive')) {
        isResponsive = true;
        orderIndex += 1;
        $dataTable
                .find('thead > tr')
                .prepend($('<th />', {
                    'data-orderable': 'false',
                    'data-filterable': 'false',
                    'data-priority': 0,
                    'class': 'sorting_disabled',
                }));
        $dataTable
                .find('tbody > tr')
                .prepend($('<td />'));
    }

    var showContent = false,
            infoContent = '',
            $infoContentSrc = $dataTable.next('.dataTable_info_src');
    if ($infoContentSrc.length > 0) {
        showContent = true;
        infoContent = $infoContentSrc
                .remove()
                .html();
        $infoContentSrc;
    }

    $dataTable
            .DataTable({
                responsive: isResponsive,
                language: {
                    url: THEME_URL + '/js/dataTables/' + LANGUAGE_CODE + '.json'
                },
                info: showContent,
                order: [[orderIndex, 'asc']],
                pagingType: 'numbers',
                stateSave: true,
                stateDuration: 0,
                infoCallback: function (settings, start, end, max, total, pre) {
                    return infoContent;
                },
                dom:
                        '<"card-body"<"row"<"col-sm-6"l><"col-sm-6"f>>>' +
                        'tr' +
                        '<"card-body"<"row"<"order-md-first col-md-5"i><"order-first col-md-7"p>>>',
            })
            .on('draw', function (e) {

                var $currentDt = $(e.currentTarget),
                        $currentDtWrapper = $currentDt.parent();

                $currentDtWrapper
                        .find('.dataTables_length select')
                        .select2({
                            theme: 'bootstrap',
                            width: '80px',
                            minimumResultsForSearch: -1
                        }).on('ready', function () {
                    $(this).css({display: 'inline-block'});
                });

                var $currentDtPaginate = $currentDtWrapper
                        .find('.dataTables_paginate');

                if ($currentDtPaginate.find('.pagination li').length < 2) {
                    $currentDtPaginate.hide();
                } else {
                    $currentDtPaginate.show();
                }



                $dataTable.css('visibility', 'visible');
            })
            .on('responsive-resize', function (e, datatable, columns) {

                var $currentDataTable = $(e.currentTarget);

                var isCollapsed = false;
                for (var i in columns) {
                    if (columns[i] === false) {
                        isCollapsed = true;
                        break;
                    }
                }

                if (isCollapsed) {
                    $currentDataTable.find('td:first-child').show();
                    $currentDataTable.find('th:first-child').show();
                } else {
                    $currentDataTable.find('tr > td:first-child').hide();
                    $currentDataTable.find('tr > th:first-child').hide();
                }

            });
});