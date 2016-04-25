coma.define(['underscore', 'jquery', '../services/logs', '../base/Controller'], function (_, $, logs, Controller) {

    return Controller.extend({
            model: Controller.prototype.model.extend({
                defaults: function () {
                    return _.extend(Controller.prototype.model.prototype.defaults(), {});
                }
            }),

            events: function () {
                return {
                    'click >div>*': onClickLog
                }
            },

            initialize: function () {
                Controller.prototype.initialize.apply(this, arguments);


                var $logTemplate = $('#coma-template-log');
                this.template = _.template($logTemplate.html());

                logs.on('change:logs', onChangeLogs, this);
                onChangeLogs(logs, logs.getLogs());
                this.$wrapper = this.$('>div');
            }
        }
    );

    function onClickLog(e) {
        e.preventDefault();
        $(e.currentTarget).remove();
    }

    function onChangeLogs(model, logs) {
        if (logs == null) {
            return;
        }
        logs = model.getLogs();
        _.each(logs, function (log) {
            var $node = $(this.template(log));
            this.$wrapper.append($node);
        }.bind(this));

    }

});