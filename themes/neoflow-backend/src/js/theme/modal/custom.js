/**
 * Show custom modal
 *
 * @returns {showCustomModal.$customModal|$|Element}
 */
function showCustomModal(selector, callback) {

    var $customModal = $(selector),
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

        callback($customModal);

        $customModal.modal('show');

    }, transitionTime);

    return $customModal;
}