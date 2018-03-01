
// Timer
(function () {

    $('.timer').each(function () {

        this.start = function () {

            var $this = $(this),
                    duration = $this.data('time') - 1;

            if (duration > 0) {
                var interval = setInterval(function () {

                    $this.text(formatTime(duration));

                    if (--duration < 0) {
                        if ($this.data('timeout-redirect')) {
                            window.location.href = $this.data('timeout-redirect');
                        } else if ($this.data('timeout-callback')) {
                            var callback = eval($this.data('timeout-callback'));
                            if (typeof callback === 'function') {
                                callback();
                            }
                        }

                        clearInterval(interval);
                        clearTimeout(duration);
                    }

                }, 1000);

            } else {
                console.warn('Duration not defined. Cannot start timer.');
            }
        };

        this.reset = function () {
            var $this = $(this),
                    duration = $this.data('time');

            $this.text(formatTime(duration));
        };

        this.restart = function () {
            this.reset();
            this.start();
        };

        this.start();

    });

})();