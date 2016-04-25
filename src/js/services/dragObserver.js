coma.define(['jquery', 'underscore', '../base/Model'], function ($, _, Model) {


    function onCollectionAdd(listener) {

    }

    var dragObserver = Model.extend({
        defaults: function () {
            return {
                collection: [],
                wrapper: window,
                debounce: 350
            };
        },

        initialize: function () {
            Model.prototype.initialize.apply(this, arguments);

            this.$wrapper = $(this.get('wrapper'));

            var move = _.debounce(onMove.bind(this), 5);
            this.$wrapper.mousemove(move);

            this.on('add', onCollectionAdd.bind(this));

        },

        add: function (listener) {
            this.get('collection').push(listener);
            this.trigger('add', listener);
        },

        remove: function (listener) {
            this.get('collection').remove(listener);
        }
    });

    function onMove(e) {

        this.set('position', {
            x: e.clientX,
            y: e.clientY
        });
    }

    return new dragObserver();

});