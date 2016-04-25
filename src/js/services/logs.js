coma.define(['module', 'jquery', 'underscore', '../base/Model'],
    function (module, $, _, Model) {


        return new (Model.extend({
            defaults: function () {
                return {
                    logs: null
                };
            },

            initialize: function () {
                Model.prototype.initialize.apply(this, arguments);
            },

            getLogs: function (){
                var logs = this.get('logs');
                this.set('logs', null);
                return logs;
            },

            add: function (logs) {
                this.set('logs', logs);
            }


        }));


    });