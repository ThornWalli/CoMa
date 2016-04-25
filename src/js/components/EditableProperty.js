coma.define(['underscore', 'jquery', '../base/Controller', '../services/logs'], function (_, $, Controller, logs) {

    return Controller.extend({
            model: Controller.prototype.model.extend({
                defaults: function () {
                    return _.extend(Controller.prototype.model.prototype.defaults(), {

                        action: null,
                        name: null,
                        value: null

                    });
                }
            }),

            events: function () {
                return {
                    'keypress': function (e) {
                        if (e.keyCode == 13) {
                            onClickSave.bind(this)(e);
                        }
                    },
                    'click': onClick,
                    'click .save': onClickSave,
                    'click .cancel': onClickCancel
                }
            },

            initialize: function () {
                Controller.prototype.initialize.apply(this, arguments);
                this.model.on('change:value', onChangeValue.bind(this));
                setup(this);
            }
        }
    );

    function onChangeValue(model, value) {
        if (value) {
            this.$el.removeClass('empty');
        } else {
            this.$el.addClass('coma-empty');
        }
        if (!value) {
            value = this.__('empty');
        }
        this.$input.val(value);
        this.$valueWrapper.html(value);

    }

    function setup(scope) {

        createEditNode(scope);

    }

    function createEditNode(scope) {

        scope.$input = scope.$el.find('.input').children('[name="' + scope.model.get('name') + '"]');
        scope.$input.css('fontSize', scope.$el.css('fontSize'));
        scope.$input.val(scope.model.get('value'));
        scope.$helper = scope.$el.children('.helper');
        scope.$valueWrapper = scope.$el.children('.value');

    }


    function onClickSave(e) {
        e.preventDefault();
        e.stopPropagation();
        save(this);
    }

    function save(scope) {

        return new Promise(function (resolve, reject) {
            if (!this.targetModel.get('ajax')) {
                reject('ajax is empty!');
            }

            var value = this.$input.val();

            var properties = {};
            properties[this.model.get('name')] = encodeURIComponent(value);

            var data = {
                action: this.model.get('action'),
                properties: properties
            };

            $.ajax({
                cache: false,
                method: 'post',
                dataType: 'json',
                data: {
                    'coma-data': data
                },
                url: this.targetModel.get('ajax')
            }).done(function (data) {
                if (data.result) {
                    resolve(value);
                } else {
                    reject('callback error :(');
                }
                if (data.logs) {
                    logs.add(data.logs);
                }
            });

        }.bind(scope)).then(function (value) {

            this.model.set({
                value: value
            });
            this.$el.removeClass('coma-show');

        }.bind(scope)).catch(function (err) {
            console.error(err);
        });

    }

    function onClickCancel(e) {
        e.preventDefault();
        e.stopPropagation();
        this.$el.removeClass('coma-show');
    }

    function onClick(e) {
        e.preventDefault();
        if (!this.$el.hasClass('coma-show')) {
            $('[data-partial^="coma/component/controller"].coma-show, [data-partial^="coma/component/editable-property"].coma-show').removeClass('coma-show');
            this.$helper.width(this.$el.width());

            if (this.$valueWrapper.height() - this.$el.height() < 2) {
                this.$helper.css({
                    top: ((this.$valueWrapper.height() - this.$helper.height()) / 2) + 'px'
                });
            }
            this.$el.addClass('coma-show');
        }
    }

});