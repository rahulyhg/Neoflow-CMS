
// Initialize Bootstrap Fileselect
$('input:file').fileselect({
    language: LANGUAGE_CODE,
    browseBtnClass: 'btn btn-outline-light',
    validationCallback: function (message, type, instance) {
        showAlertModal(message);
    }
});