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

$('.regexomat[data-regex]').on('input', function () {
    var $this = $(this),
            regexRule = $this.data('regex'),
            value = $this.val();

    value = value.replace(new RegExp(regexRule), '');
    $this.val(value);
});
