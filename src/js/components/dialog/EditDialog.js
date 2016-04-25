coma.define(['underscore', 'jquery', './FormDialog', '../../services/history'], function (_, $, FormDialog, history) {

    return FormDialog.extend({

            model: FormDialog.prototype.model.extend({

                defaults: function () {
                    return _.extend({
                        pageId: history.getValue('coma-page-id'),

                        id: null,
                        class: null,
                        position: null,
                        areaId: null
                    }, FormDialog.prototype.model.prototype.defaults());
                }

            }),

            events: function () {
                return _.extend({}, FormDialog.prototype.events());
            },

            initialize: function () {
                FormDialog.prototype.initialize.apply(this, arguments);
            },

            getAjaxData: function () {
                var data = FormDialog.prototype.getAjaxData.apply(this, arguments);
                if (this.model.get('id')) {
                    data.id = this.model.get('id');
                }
                return data;
            }


        }
    );


});

