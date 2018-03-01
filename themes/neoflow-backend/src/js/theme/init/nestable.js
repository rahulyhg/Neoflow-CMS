
// Initialize Nestable
(function () {

    $('.nestable').each(function () {
        var $nestable = $(this);

        $nestable
                .nestable({
                    rootClass: 'nestable',
                    listClass: 'nestable-list list-group',
                    itemClass: 'nestable-item',
                    placeClass: 'nestable-placeholder',
                    dragClass: 'nestable-dragging',
                    handleClass: 'nestable-handle',
                    emptyClass: 'nestable-empty',
                    toggleClass: 'nestable-toggle',
                    stateSave: true,
                    threshold: 20
                })
                .on('change', function (e) {
                    var $target = $(e.target);
                    if (!$target.is('input') && !$target.is('a') && $nestable.data('save-url')) {
                        $.ajax({
                            type: 'POST',
                            url: $nestable.data('save-url'),
                            data: JSON.stringify($nestable.nestable('serialize')),
                            contentType: 'application/json',
                            dataType: 'json'
                        });
                    }
                });
    });

})();