coma.define(['jquery', '../base/Controller'], function ($, Controller) {

    return Controller.extend({

            events: function () {
                return {
                    'click ul>li>a': switchTab
                }
            },

            initialize: function () {
                Controller.prototype.initialize.apply(this, arguments);

                var value;
                if (!!this.$el.data().value) {
                    value = this.$el.data().value;
                } else {
                    value = this.$('>ul>li').first().children().get(0).hash.replace(/#/, '');
                }
                toggleLinkActiveClass(this.$('>ul>li>a'), value);
                toggleContainerActiveClass(this.$('>section>*'), value);
            }
        }
    );

    function switchTab(e) {
        e.preventDefault();
        var value = $(e.currentTarget).get(0).hash.replace(/#/, '');
        toggleLinkActiveClass(this.$('>ul>li>a'), value);
        toggleContainerActiveClass(this.$('>section>*'), value);
    }

    function toggleLinkActiveClass(nodes, value) {
        var selected = nodes.filter('[href="#' + value + '"]').parent();
        nodes.parent().not(selected).removeClass('active');
        selected.addClass('active');
    }

    function toggleContainerActiveClass(nodes, value) {
        var selected = nodes.filter('[data-tab="#' + value + '"]');
        nodes.not(selected).removeClass('active');
        selected.addClass('active');
    }

});