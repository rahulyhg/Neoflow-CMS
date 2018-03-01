// Plugin as a click event listener to show confirm modal
$.fn.confirmModalClickListener = function () {
    $(this).on('click', function (e) {
        e.preventDefault();

        var $this = $(this),
                message = $this.data('message'),
                url = $this.attr('href');

        if (message && url) {
            showConfirmModal(message, url);
        }
    });

    return this;
};

// Apply click listener
$('.confirm-modal').confirmModalClickListener();

/**
 * Show confirm modal
 *
 * @param {string} message
 * @param {string} url
 * @returns {showConfirmModal.$confirmModal|$|Element}
 */
function showConfirmModal(message, url) {
    var $confirmModal = $('#confirmModal'),
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
        $confirmModal.modal('hide');
        $confirmModal.find('.modal-body').empty();
        $confirmModal.find('.modal-footer .btn-confirm').attr('href', '#');
        $confirmModal.find('.modal-body').html(message);
        $confirmModal.find('.modal-footer .btn-confirm').attr('href', url);
        $confirmModal.modal('show');
    }, transitionTime);

    return $confirmModal;
}