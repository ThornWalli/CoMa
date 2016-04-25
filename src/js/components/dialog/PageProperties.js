coma.define(['underscore', './FormDialog'], function (_, FormDialog) {

    return FormDialog.extend({

            model: FormDialog.prototype.model.extend({

                defaults: function () {
                    return _.extend({
                        id: null
                    }, FormDialog.prototype.model.prototype.defaults());
                }

            }),

            getAjaxData: function () {
                var data = FormDialog.prototype.getAjaxData.apply(this, arguments);
                data.action = 'page-edit-properties';
                data.pageId = this.model.get('pageId');
                return data;
            },

            onDone: function () {
                window.location.reload();
            }

        }
    );

});

