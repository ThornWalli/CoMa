coma.define(['underscore', './FormDialog'], function (_, FormDialog) {

    return FormDialog.extend({

            model: FormDialog.prototype.model.extend({

                defaults: function () {
                    return _.extend({}, FormDialog.prototype.model.prototype.defaults());
                }

            }),

        initialize: function () {
            FormDialog.prototype.initialize.apply(this, arguments);
        },

            getAjaxData: function () {
                var data = FormDialog.prototype.getAjaxData.apply(this, arguments);
                data.action = 'global-edit-properties';
                return data;
            },

            onDone: function () {
                window.location.reload();
            }

        }
    );

});

