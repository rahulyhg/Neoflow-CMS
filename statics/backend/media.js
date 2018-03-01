function showPreviewModal($output, downloadLink, size) {

    showCustomModal('#previewModal', function ($previewModal) {

        $previewModal.find('h5').text(mediaTranslation['Preview']);
        $previewModal.find('.modal-body').html($output);

        if (['sm', 'lg'].indexOf(size) >= 0) {
            $previewModal.find('.modal-dialog').addClass('modal-' + size);
        }

        var $downloadLinkAnchor = $previewModal.find('.btn-download');
        if (downloadLink) {
            $downloadLinkAnchor.show().prop('href', downloadLink);
        } else {
            $downloadLinkAnchor.hide();
        }
    });
}

(function () {
    var $inputExtension = $('#inputExtension'),
            $inputName = $('#inputName');

    $inputName.on('input', function () {
        var value = $(this).val();
        $inputExtension.parent().find('.input-group-addon').text(value);
    });

    $inputExtension.on('input', function () {
        var value = $(this).val();
        $inputName.parent().find('.input-group-addon').text(value);
    });

})();



$('a.preview').on('click', function (e) {

    var $this = $(this),
            previewUrl = $this.data('preview'),
            downloadUrl = $this.prop('href');

    if (previewUrl) {
        e.preventDefault();

        var extension = previewUrl.split('.').pop(),
                $emptyFileParagraph = $('<p>', {
                    text: mediaTranslation['Empty file']
                });

        if (['jpg', 'jpeg', 'bmp', 'png', 'gif'].indexOf(extension) >= 0) {

            var $img = $('<img>', {
                class: 'img-fluid',
                src: previewUrl
            }),
                    $wrapper = $('<div>', {
                        style: 'text-align: center'
                    }).append($img);

            showPreviewModal($wrapper, downloadUrl, 'lg');

        } else if (['txt'].indexOf(extension) >= 0) {

            $.get(previewUrl, function (data) {

                $span = $('<span>', {
                    html: data.replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1<br />$2'),
                    class: 'd-block'
                });

                showPreviewModal($span, downloadUrl, 'lg');
            }, 'text');

        } else if (['js', 'css'].indexOf(extension) >= 0) {

            $.get(previewUrl, function (data) {
                var $code = $('<code>', {
                    text: data
                }),
                        $pre = $('<pre>').append($code);

                showPreviewModal($pre, downloadUrl, 'lg');
            }, 'text');

        } else if (['pdf', 'html', 'htm'].indexOf(extension) >= 0) {

            var $iframe = $('<iframe>', {
                src: previewUrl,
                style: 'width: 100%; height: calc(100vh - 250px)',
                frameborder: 0
            });
            showPreviewModal($iframe, downloadUrl, 'lg');

        } else {

            var $span = $('<span>', {
                text: mediaTranslation['The file cannot be previewed']
            });
            showPreviewModal($span, downloadUrl, 'sm');

        }
    }
});

