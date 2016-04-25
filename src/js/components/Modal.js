coma.define(['jquery', '../components/PageManager', '../services/history', '../utils/Fullscreen'], function ($, PageManager, history, Fullscreen) {

    return PageManager.extend({

        events: function () {
            return {
                'click > .content': closeOutside,
                'click > .content > section > .fullscreen-toggle': onClickFullscreenToggle
            }
        },

        initialize: function () {
            PageManager.prototype.initialize.apply(this, arguments);
            this.fullscreen = new Fullscreen('modal-');
        },

        update: function (model) {
            if (PageManager.prototype.update.apply(this, arguments)) {
                this.$el.removeClass('no-active').addClass('active');
                $('body').addClass('coma-active-modal ' + this.$el.data('deep'));
            } else {
                if (this.$el.hasClass('active')) {
                    $('body').removeClass('coma-active-modal ' + this.$el.data('deep'));
                    this.$el.removeClass('active').addClass('no-active');
                    this.fullscreen.exitFullscreen();
                }
            }
        }
    });

    function onClickFullscreenToggle(e) {
        e.preventDefault();
        this.fullscreen.toggleFullscreen(this.el);
    }

    function closeOutside(e) {
        var modal = $(e.currentTarget);
        if (modal.is(e.target)) {
            $(modal).find('section > a.close').trigger('click');
        }
    }

    function preventOverscroll(container) {
        container.on("touchstart", beforeScroll);
        container.on("touchmove", onScroll);
    }

    function beforeScroll() {
        var content = $('> .content', this).get(0);
        this.allowScrollUp = content.scrollTop > 0;
        this.allowScrollDown = (content.scrollTop < content.scrollHeight - content.clientHeight);
        this.lastY = event.pageY;
    }

    function onScroll() {
        var up = (event.pageY > this.lastY);
        var down = !up;
        this.lastY = event.pageY;
        if ((up && this.allowScrollUp) || (down && this.allowScrollDown)) {
            event.stopPropagation();
        } else {
            event.preventDefault();
        }
    }
});