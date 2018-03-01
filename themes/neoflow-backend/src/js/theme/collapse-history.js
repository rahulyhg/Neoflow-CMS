
// Collapse History
(function () {

    $('.collapse-history')
            .each(function () {
                if (localStorage['collapse-state-' + this.id] === 'shown') {
                    $(this).addClass('show');
                    $('.collapsed[data-target="#' + this.id + '"], .collapsed[href="#' + this.id + '"]').removeClass('collapsed');
                } else if (localStorage['collapse-state-' + this.id] === 'hidden') {
                    $(this).removeClass('show');
                    $('[data-target="#' + this.id + '"], [href="#' + this.id + '"]').addClass('collapsed');
                }
            })
            .on('hidden.bs.collapse', function () {
                localStorage['collapse-state-' + this.id] = 'hidden';
            })
            .on('shown.bs.collapse', function () {
                localStorage['collapse-state-' + this.id] = 'shown';
            });
}
)();
