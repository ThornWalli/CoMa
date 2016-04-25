coma.define(['module', 'jquery', '../base/Events'], function (module, $, Events) {

    return function (prefix) {

        Events.prototype.constructor.apply(this, arguments);

        this.isChange = false;
        this.prefix = prefix || module.config().prefix || '';

        var $html = $('html');

        setClass(this, false);

        document.addEventListener('fullscreenchange', onChangeFullscreen.bind(this));
        document.addEventListener('webkitfullscreenchange', onChangeFullscreen.bind(this));
        document.addEventListener('mozfullscreenchange', onChangeFullscreen.bind(this));
        document.addEventListener('MSFullscreenChange', onChangeFullscreen.bind(this));

        this.fullscreen = function (el) {

            if (this.isChange) {
                return;
            }
            this.isChange = true;

            if (!el) {
                el = document.documentElement;
            }

            if (el.requestFullscreen) {
                el.requestFullscreen();
            } else if (el.msRequestFullscreen) {
                el.msRequestFullscreen();
            } else if (el.mozRequestFullScreen) {
                el.mozRequestFullScreen();
            } else if (el.webkitRequestFullscreen) {
                el.webkitRequestFullscreen(Element.ALLOW_KEYBOARD_INPUT);
            }
        };

        this.exitFullscreen = function () {
            if (this.isChange) {
                return;
            }
            this.isChange = true;
            if (document.exitFullscreen) {
                document.exitFullscreen();
            } else if (document.msExitFullscreen) {
                document.msExitFullscreen();
            } else if (document.mozCancelFullScreen) {
                document.mozCancelFullScreen();
            } else if (document.webkitExitFullscreen) {
                document.webkitExitFullscreen();
            }
        };

        this.toggleFullscreen = function (el) {
            if (!document.fullscreenElement && !document.mozFullScreenElement && !document.webkitFullscreenElement && !document.msFullscreenElement) {  // current working methods
                this.fullscreen(el);
            } else {
                this.exitFullscreen();
            }
        };

        function onChangeFullscreen() {
            var fullscreen = !$html.hasClass(this.prefix + 'fullscreen');
            setClass(this, fullscreen);
            this.isChange = false;
            this.trigger('change', fullscreen);
        }

        function setClass(scope, fullscreen) {
            if (!fullscreen) {
                $html.removeClass(scope.prefix + 'fullscreen');
                $html.addClass(scope.prefix + 'no-fullscreen');
            } else {
                $html.removeClass(scope.prefix + 'no-fullscreen');
                $html.addClass(scope.prefix + 'fullscreen');
            }
        }

    };

});

