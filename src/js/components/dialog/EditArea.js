coma.define(['underscore', 'jquery', './EditDialog'], function (_, $, EditDialog) {

    return EditDialog.extend({

            model: EditDialog.prototype.model.extend({

                defaults: function () {
                    return _.extend({
                        pageId: null
                    }, EditDialog.prototype.model.prototype.defaults());
                }

            }),

            events: function () {
                return _.extend({}, EditDialog.prototype.events());
            },

            initialize: function () {
                EditDialog.prototype.initialize.apply(this, arguments);

            },

            getAjaxData: function () {
                var data = EditDialog.prototype.getAjaxData.apply(this, arguments);
                data.action = 'area-edit';
                return data;
            },

            onDone: function (data) {
                this.targetModel.refreshNode();
                EditDialog.prototype.onDone.apply(this, arguments)
            }
        }
    );

});

