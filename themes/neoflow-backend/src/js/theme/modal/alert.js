// Plugin as a click event listener to show alert modal
$.fn.alertModalClickListener = function () {
    $(this).on('click', function (e) {
        e.preventDefault();

        var $this = $(this),
                message = $this.data('message');

        if (message) {
            showAlertModal(message);
        }
    });

    return this;
};

// Apply click listener
$('.alert-modal').alertModalClickListener();

/**
 * Show alert modal
 *
 * @param {string} message
 * @returns {showAlertModal.$alertModal|$|Element}
 */
function showAlertModal(message) {
    var $alertModal = $('#alertModal'),
            $openModals = $('.modal.show'),
            transitionTime = 400;

    // Hide all modals
    if ($openModals.length > 0) {
        $openModals.modal('hide');
    } else {
        transitionTime = 0;
    }

    // Set content and show modal
    setTimeout(function () {
        $alertModal.find('.modal-body').empty();
        $alertModal.find('.modal-body').html(message);
        $alertModal.modal('show');
    }, transitionTime);

    return $alertModal;
}