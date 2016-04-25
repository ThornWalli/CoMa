coma.define(['underscore', 'jquery', '../../services/parser', '../../services/logs', '../../components/Controller', '../../services/history', '../../services/dragObserver'], function (_, $, parser, logs, Controller, history, dragObserver) {

    return Controller.extend({

            type: 'component',

            model: Controller.prototype.model.extend({
                defaults: function () {
                    return _.extend({
                        parentId: null,
                        move: false
                    }, Controller.prototype.model.prototype.defaults());
                },
            }),

            events: function () {
                return _.extend({
                    'click >.header>div>ul>li>.copy': onClickCopy,
                }, Controller.prototype.events());
            },

            initialize: function () {
                Controller.prototype.initialize.apply(this, arguments);
                setup(this);
            }
        }
    );

    function setup(scope) {
        scope.model.on('change:move', onChangeMove.bind(scope));
        scope.onDragChangePosition = function (model, position) {
            if (scope.model.get('move')) {
                scope.$el.css({
                    left: position.x,
                    top: position.y
                });
            }
        }.bind(scope);
    }

    function onClickCopy(e) {
        e.preventDefault();
        this.targetModel.copyComponent(this.model.get('id'));
    }

    function onChangeMove(model, move) {
        if (move) {
            this.$el.width(this.$el.width());
            this.$el.height(this.$el.height());
            this.onDragChangePosition(dragObserver, dragObserver.get('position'));
            dragObserver.on('change:position', this.onDragChangePosition);
            this.$el.addClass('coma-move');
            this.model.set('showGhost', true);
            this.targetModel.set('moveComponent', this);
            this.targetModel.set('highlight', true);

            $('html').addClass('coma-component-move');
        } else {
            dragObserver.off('change:position', this.onDragChangePosition);
            this.model.set('showGhost', false);
            this.$el.css({
                width: '',
                height: '',
                left: '',
                top: ''
            });
            this.$el.removeClass('coma-move');
            this.targetModel.set('moveComponent', null);
            this.targetModel.set('highlight', false);

            $('html').removeClass('coma-component-move');

        }
    }

});