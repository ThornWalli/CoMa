coma.define(['underscore', 'jquery', './FormDialog', '../../services/history'], function (_, $, FormDialog, history) {

    return FormDialog.extend({

            model: FormDialog.prototype.model.extend({

                defaults: function () {
                    return _.extend({
                        pageId: history.getValue('coma-page-id'),
                        class: null,
                        position: null,
                        areaId: null,

                        deepModal: null,
                        deepComponent: null
                    }, FormDialog.prototype.model.prototype.defaults());
                }

            }),


            events: function () {
                return _.extend({
                    'click .radio-component': onClickRadio
                }, FormDialog.prototype.events());
            },

            initialize: function () {
                FormDialog.prototype.initialize.apply(this, arguments);
                this.$('[name="apply"]').attr('disabled', true);
            },

            getAjaxData: function () {
                var data = FormDialog.prototype.getAjaxData.apply(this, arguments);
                data.action = 'component-select';
                data.pageId = this.model.get('pageId');
                data.areaId = this.model.get('areaId');
                data.class = this.model.get('class');
                return data;
            },

            onDone: function (data) {
                history.replace(this.model.get('deepModal'), 'component-edit');
            }


        }
    );

    function onClickRadio(e) {
        this.model.set('class', e.currentTarget.value);
        this.$('[name="apply"]').attr('disabled', false);
    }

});
