coma.define(['underscore', 'jquery', 'base/Controller', 'services/logs'], function (_, $, Controller, logs) {


    return Controller.extend({
            rowTemplate: null,
            model: Controller.prototype.model.extend({
                defaults: function () {
                    return _.extend(Controller.prototype.model.prototype.defaults(), {});
                }
            }),

            events: function () {
                return {
                    'click .add-field': onClickAddField,
                    'click .remove>a': onClickRemove,
                }
            },

            initialize: function () {
                Controller.prototype.initialize.apply(this, arguments);
                setup(this);
            }
        }
    );

    function setup(scope) {
        scope.$rows = scope.$('.rows');
        scope.rowTemplate = _.template(scope.$('[type="text/template"]').html());
    }

    function onClickRemove(e) {
        e.preventDefault();
        $(e.currentTarget).closest('.fields').remove();
    }

    function onClickAddField(e) {
        e.preventDefault();

        var $row = $(this.rowTemplate());
        $row.find('.partial[data-partial="coma/assetboard/property-dialog/field-row"]').each(function (i, node){
            var id = _.uniqueId('property-dialog-');
            var $node = $(node);
            $node.find('[id]').attr('id', id);
            $node.find('[for]').attr('for', id);
        });

        this.$rows.append($row);

    }

});