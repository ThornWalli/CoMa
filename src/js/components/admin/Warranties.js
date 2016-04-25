coma.define(['underscore', 'jquery', '../../services/parser', '../../base/Controller', '../../base/DomModel'], function (_, $, parser, Controller, DomModel) {

    function onChangeToggleChecked(e) {
        var $node = $(e.currentTarget);
        if ($node.is(':checked')) {
            this.$('input[type="checkbox"]').not(e.currentTarget).not(':checked').trigger('click');
        } else {
            this.$('input[type="checkbox"]:checked').not(e.currentTarget).trigger('click');
        }

    }

    function onChangeRole(e) {
        this.model.set('role', $(e.currentTarget).val())
    }

    function onChangeFilter(e) {
        this.model.set('filter', $(e.currentTarget).val())
    }

    function onChangeCheckbox(e) {
        if (this.$('input[type="checkbox"][name="propertyRemove"]:checked').not('[name="toggleChecked"]').length != this.$('input[type="checkbox"]:checked').not('[name="toggleChecked"]').length) {
            $('[name="removeSelected"]').attr('disabled', 'disabled');
        } else {
            $('[name="removeSelected"]').removeAttr('disabled');
        }
    }

    function getCaps(scope) {

    }

    function onClickRemoveSelected(e) {
        var ids = [];
        this.$('input[type="checkbox"][name="propertyRemove"]:checked').each(function (i, node) {

            ids.push(node.getAttribute('value'));

        });

        if (confirm(ids.length + " Eigenschaften wirklich l�schen?")) {

            if (scope.model.get('ajax')) {
                var data = {
                    action: 'save-role-caps',
                    role: model.get('role'),
                    ids: getCaps(this)
                };
                $.ajax({
                    cache: false,
                    method: 'post',
                    dataType: 'json',
                    data: {
                        'coma-data': data
                    },
                    url: model.get('ajax')
                }).done(function (resultData) {
                    if (resultData.result) {
                    } else {
                        console.error(':(');
                    }
                }.bind(model));
            } else {
                console.error('ajax is empty!');
            }

        }
    }

    function renderCapabilities(scope, capabilities) {
        scope.$list.empty();
        scope.$list.html(scope.model.get('capabilityTemplate')({roles: capabilities}));
    }

    function onClickSave(e) {
        e.preventDefault();
        $(e.currentTarget).attr('disabled', true);
        saveCapabilities(this);
    }

    function loadCapabilities(scope) {


        if (scope.model.get('ajax')) {
            var data = {
                'coma-action': 'get-capabilities'
            };
            if (!!scope.model.get('filter')) {
                data['coma-filter'] = scope.model.get('filter');
            }
            if (!!scope.model.get('role')) {
                data['coma-role'] = scope.model.get('role');
            }
            $.ajax({
                cache: false,
                method: 'get',
                dataType: 'json',
                data: data,
                url: scope.model.get('ajax')
            }).done(function (resultData) {
                if (resultData.result) {
                    renderCapabilities(scope, resultData['capabilities']);
                } else {
                    console.error(':(');
                }
            }.bind(scope));
        } else {
            console.error('ajax is empty!');
        }

    }

    function saveCapabilities(scope) {

        var $inputs = scope.$list.find('[type="checkbox"]');
        var capatilities = {};
        console.log('$inputs', $inputs)
        $inputs.each(function (i, input) {
            var $input = $(input);
            capatilities[$input.attr('name')] = $input.is(':checked') ? 1: 0;
        });

        if (scope.model.get('ajax')) {
            var data = {
                'coma-data': {
                    'action': 'capabilities-save',
                    'role': scope.model.get('role'),
                    'capatilities': capatilities
                }
            };
            $.ajax({
                cache: false,
                method: 'post',
                dataType: 'json',
                data: data,
                url: scope.model.get('ajax')
            }).done(function (resultData) {
                if (resultData.result) {
                    this.$('[name="save"]').attr('disabled', false);
                } else {
                    console.error(':(');
                    this.$('[name="save"]').attr('disabled', false);
                }
            }.bind(scope));
        }

        else {
            console.error('ajax is empty!');
        }

    }

    return Controller.extend({
            model: Controller.prototype.model.extend({
                defaults: function () {
                    return _.extend(Controller.prototype.model.prototype.defaults(), {
                        ajax: null,
                        role: null,
                        filter: null
                    });
                }
            }),

            events: function () {
                return {
                    'change [name="toggleChecked"]': onChangeToggleChecked,
                    'change [name="role"]': onChangeRole,
                    'change [name="filter"]': onChangeFilter,
                    'click [name="save"]': onClickSave
                }
            },

            initialize: function () {
                Controller.prototype.initialize.apply(this, arguments);

                this.model.set('role', this.$('[name="role"]').val());
                this.model.set('filter', this.$('[name="filter"]').val());

                this.model.set('capabilityTemplate', _.template(this.$('#capability-template').html()));

                this.model.on('change:role', function (model, role) {
                    loadCapabilities(this);
                }.bind(this));
                this.model.on('change:filter', function (model) {
                    loadCapabilities(this);
                }.bind(this));

                this.$list = this.$('>.capabilities>ul');

                loadCapabilities(this)

            }
        }
    );


})
;