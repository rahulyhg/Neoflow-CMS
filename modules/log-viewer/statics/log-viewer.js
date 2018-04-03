var $logfileIframe = $('#logfileIframe'),
        logfileUrl = $logfileIframe.prop('src');

// Scroll onload to bottom
$logfileIframe.on('load', function () {
    this.contentWindow.scrollTo(0, 1000000000);
});

var $logViewerTimer = $('#logViewerTimer');

// Refresh iframe function
function refreshLogfileIframe() {
    $logfileIframe.prop('src', logfileUrl);
    $logViewerTimer.get(0).restart();
}