// Event listener for auto submit
$('form.auto-submit').on('change', function () {
    $(this).submit();
});

// Plugin to prevent default click behaviour
$.fn.preventDefaultClickBehaviour = function () {
    $(this).on('click', function (e) {
        e.preventDefault();
    });

    return this;
};

// Prevent default click behaviour for disabled anchors
$('a[disabled]').preventDefaultClickBehaviour();

// Custom regex to allow only values based on the rule for input fields
$('.regexomat[data-regex]')
    .on('input', function () {
        var $this = $(this),
            regexRule = $this.data('regex'),
            value = $this.val();

        value = value.replace(new RegExp(regexRule), '');
        $this.val(value);
    });

$('form').on('submit', function () {
    var $this = $(this)
        .addClass('form-loading')

    $this.find(':submit').blur();

    var $placeholder = $('<div>')
        .css({
            'display': 'none',
            'height': $this.outerHeight() + 'px',
            'width': $this.outerWidth() + 'px',
        })
        .addClass('loader');

    $this.append($placeholder.fadeIn(200));


});