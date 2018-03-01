/**
 * Formate time
 *
 * @param {Number} time
 * @returns {String}
 */
function formatTime(time) {
    var seconds = Math.floor(time % 60),
            minutes = Math.floor((time / 60) % 60),
            hours = Math.floor((time / (60 * 60)) % 24);

    hours = hours < 10 ? '0' + hours : hours;
    minutes = minutes < 10 ? '0' + minutes : minutes;
    seconds = seconds < 10 ? '0' + seconds : seconds;

    return hours + ':' + minutes + ':' + seconds;
}

