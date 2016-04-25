coma.define(['underscore', 'jquery', '../../base/Controller', '../../base/DomModel'], function (_, $, Controller, DomModel) {

    return Controller.extend({

            model: DomModel.extend({

                defaults: function () {
                    return {
                        hex: null
                    };
                }

            }),

            events: function () {
                return {}
            },

            initialize: function () {
                Controller.prototype.initialize.apply(this, arguments);
                setup(this);
            }
        }
    );

    function setup(scope) {
        scope.$input = window.jQuery(scope.$('[type="text"]'));
        scope.$input.wpColorPicker();
    }

});