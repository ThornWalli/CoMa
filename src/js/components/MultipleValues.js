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
                    'click a.remove': onClickRemove,
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
        $row.find('.partial[data-partial="coma/assetboard/property-dialog/field-row"]').each(function (i, row) {
            var id = _.uniqueId('property-dialog-');
            var $row = $(row);
            $row.find('[id]').attr('id', id);
            $row.find('[for]').attr('for', id);
        });

        this.$rows.append($row);


        function refreshIndex(scope) {
            scope.$rows.children().each(function (i, row) {
                row.querySelectorAll('[name*="[%index%]"]').forEach(function (node) {
                    node.setAttribute('name', node.getAttribute('name').replace(/%index%/g, i))
                });
            });
        }

        refreshIndex(this);

    }

});
