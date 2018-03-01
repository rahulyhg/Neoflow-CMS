// Plugin as a click event listener to show relogin modal
$.fn.reloginModalClickListener = function () {
    $(this).on('click', function (e) {
        e.preventDefault();

        showReloginModal();
    });

    return this;
};

// Apply click listener
$('.relogin-modal').reloginModalClickListener();

/**
 * Show relogin modal
 *
 * @returns {showReloginModal.$reloginModal|$|Element}
 */
function showReloginModal() {
    var $reloginModal = $('#reloginModal'),
            $sessionTimer = $('#sessionTimer'),
            $reloginModalForm = $reloginModal.find('form'),
            $openModals = $('.modal.show'),
            transitionTime = 400;

    $reloginModal
            .on('show.bs.modal', function () {
                $reloginModalForm.find('.form-control[disabled]').removeAttr('disabled');
            })
            .on('hide.bs.modal', function () {
                $reloginModalForm.find('.form-control:not([disabled])').attr('disabled', true);
            });

    $reloginModalForm
            .on('submit', function (e) {
                $.ajax({
                    type: 'POST',
                    url: $reloginModalForm.prop('action'),
                    data: $reloginModalForm.serialize(),
                    success: function (data)
                    {
                        if (data.status) {
                            $reloginModal.modal('hide');
                            $sessionTimer.get(0).restart();
                            $reloginModalForm.off('submit');
                            $reloginModal.find('p.alert').remove();
                        } else {
                            var $modalBody = $reloginModal.find('.modal-body');
                            $modalBody.find('p.alert').remove();
                            $modalBody.prepend('<p class="alert alert-danger">' + data.message + '</p>');
                        }
                    }
                });
                e.preventDefault();
            });

    // Hide all modals
    if ($openModals.length > 0) {
        $openModals.modal('hide');
    } else {
        transitionTime = 0;
    }

    // Show modal
    setTimeout(function () {
        $reloginModal.modal('show');
    }, transitionTime);

    return $reloginModal;
}
