
// Navigation
(function () {

    // Sidenav minimize/expand behaviour
    var $sideNavigation = $('#sideNavigation')
            .on('minimized', function (e, silent) {
                localStorage['side-navigation'] = 'minimized';
            })
            .on('expanded', function (e, silent) {
                localStorage['side-navigation'] = 'expanded';
            })
            .on('expand minimize', function (e, silent) {
                $sideNavigation.removeScrollbar();
            })
            .on('expanded minimized', function (e, silent) {
                setTimeout(function () {
                    $sideNavigation.addScrollbar();
                }, 200);
            });

    // Toggle minimize/expand function
    $sideNavigation.toggle = function (silent) {
        if ($('body').hasClass('side-navigation-minimized')) {
            $sideNavigation.expand(silent);
        } else {
            $sideNavigation.minimize(silent);
        }
    };

    // Minimize function
    $sideNavigation.minimize = function (silent) {
        $sideNavigation.trigger('minimize', [silent]);
        $('body').addClass('side-navigation-minimized');
        if (!silent) {
            $sideNavigation.find('.navbar-sidenav .nav-link-collapse').addClass('collapsed');
            $sideNavigation.find('.navbar-sidenav .collapse').removeClass('show');
        }
        $sideNavigation.trigger('minimized', [silent]);
    };

    // Expand function
    $sideNavigation.expand = function (silent) {
        $sideNavigation.trigger('expand', [silent]);
        $('body').removeClass('side-navigation-minimized');
        if (!silent) {
            $sideNavigation.find('.navbar-sidenav .active > .nav-link-collapse').remove('collapsed');
            $sideNavigation.find('.navbar-sidenav .active > .nav-link-collapse + .collapse').addClass('show');
        }
        $sideNavigation.trigger('expanded', [silent]);
    };

    // Add scrollbar function
    $sideNavigation.addScrollbar = function () {
        $sideNavigation.niceScroll({
            zindex: 99999999,
            cursorborder: 0,
            cursorborderradius: 0,
            cursorcolor: '#5f5f6c',
            cursoropacitymin: 0.4,
            autohidemode: 'leave',
            cursorwidth: '6px'
        });
    };

    // Remove scrollbar function
    $sideNavigation.removeScrollbar = function () {
        $sideNavigation.getNiceScroll().remove();
    };

    // Check current state of side navigation
    if (localStorage['side-navigation'] === 'minimized') {
        $sideNavigation.minimize(false);
    } else {
        $sideNavigation.expand(false);
    }

    // Set toggle button for side navigation
    $('#sidenavToggleLeftRightBtn').on('click', function (e) {
        e.preventDefault();
        $sideNavigation.toggle(false);
    });

    // Expand sidenav when a collapsible nav link is clicked
    $('.navbar-sidenav .nav-link-collapse').click(function (e) {
        e.preventDefault();
        $sideNavigation.expand(true);
    });

    // Prevent the content wrapper from scrolling when the fixed side navigation hovered over
    $('body.fixed-nav .navbar-sidenav, body.fixed-nav .sidenav-toggle-btn, body.fixed-nav .navbar-collapse').on('mousewheel DOMMouseScroll', function (e) {
        var e0 = e.originalEvent,
                delta = e0.wheelDelta || -e0.detail;
        this.scrollTop += (delta < 0 ? 1 : -1) * 30;
        e.preventDefault();
    });
})();