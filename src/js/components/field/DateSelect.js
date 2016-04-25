coma.define(['underscore', 'jquery', '../../base/Controller', '../../base/DomModel'], function (_, $, Controller, DomModel) {

    return Controller.extend({

            model: DomModel.extend({

                defaults: function () {
                    return {
                        day: null,
                        month: null,
                        year: null,
                        date: null
                    };
                }

            }),

            events: function () {
                return {
                    'change select': onChangeSelect,
                    'click .reset': onClickReset
                }
            },

            initialize: function () {
                Controller.prototype.initialize.apply(this, arguments);
                setup(this);
            }
        }
    );

    function setup(scope) {
        scope.model.on('change:day', onChange.bind(scope));
        scope.model.on('change:month', onChange.bind(scope));
        scope.model.on('change:year', onChange.bind(scope));

        scope.$input = scope.$('input');
        scope.$day = scope.$('[data-type="day"]');
        scope.$month = scope.$('[data-type="month"]');
        scope.$year = scope.$('[data-type="year"]');

        if (scope.model.get('date')) {
            var date = new Date(Date.parse(scope.model.get('date')));
            var day = date.getDate();
            var month = date.getMonth()+1;
            var year = date.getFullYear();
            scope.$day.children('[value="'+day+'"]').attr('selected', true);
            scope.$month.children('[value="'+month+'"]').attr('selected', true);
            scope.$year.children('[value="'+year+'"]').attr('selected', true);
            scope.model.set({
                day: day,
                month: month,
                year: year
            });
        }
    }

    function onClickReset(e) {
        e.preventDefault();
        this.$day.children().eq(0).attr('selected', true);
        this.$month.children().eq(0).attr('selected', true);
        this.$year.children().eq(0).attr('selected', true);
        this.model.set({
            day: this.$day.val(),
            month: this.$month.val(),
            year: this.$year.val()
        });
    }

    function onChangeSelect(e) {
        e.preventDefault();
        var $node = $(e.currentTarget);
        this.model.set($node.data('type'), parseInt($node.val()));
    }

    function onChange(model) {
        if (model.get('year') == 0 && model.get('month') == 0 && model.get('day') == 0) {
            this.$input.val('');
        } else {
            this.$input.val((model.get('month') || 1) + '/' + (model.get('day') || 1) + '/' + (model.get('year') || '0000'));
        }

    }

});