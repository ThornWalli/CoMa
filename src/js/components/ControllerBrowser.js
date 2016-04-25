coma.define(['underscore', 'jquery', '../services/parser', './controllerBrowser/Controller', '../base/DomModel'], function (_, $, parser, Controller, DomModel) {

        return Controller.extend({
                model: Controller.prototype.model.extend({
                    defaults: function () {
                        return _.extend(Controller.prototype.model.prototype.defaults(), {
                            idType: 'pageId',
                            pageId: null,
                            canDisable: false
                        });
                    }
                }),

                events: function () {
                    return {
                        'change [name="toggleChecked"]': onChangeToggleChecked,
                        'change [name="pageId"]': onChange,
                        'click [name="removeSelected"]': onClickRemoveSelected,
                        'change [type="checkbox"]': onChangeCheckbox,
                        'change [name="entryType"]': onChangeEntryType
                    }
                },

                initialize: function () {
                    Controller.prototype.initialize.apply(this, arguments);
                    this.model.on('change:id', function (model, id) {
                        model.set('pageId', id)
                    });

                    this.model.set('id', this.$('[name="pageId"]').val())

                }
            }
        );

        function onChangeToggleChecked(e) {
            var $node = $(e.currentTarget);
            if ($node.is(':checked')) {
                this.$('input[type="checkbox"]').not(e.currentTarget).not(':checked').trigger('click');
            } else {
                this.$('input[type="checkbox"]:checked').not(e.currentTarget).trigger('click');
            }

        }

        function onChange(e) {
            this.model.set('id', $(e.currentTarget).val())
        }

        function onChangeCheckbox(e) {
            if (this.$('input[type="checkbox"][name="propertyRemove"]:checked').length > 0 && this.$('input[type="checkbox"][name="controllerRemove"]:checked').length > 0) {
                $('[name="removeSelected"]').attr('disabled', 'disabled');
            } else {
                $('[name="removeSelected"]').removeAttr('disabled');
            }
        }

        function onClickRemoveSelected(e) {
            e.preventDefault();

            var ids;
            var $properties = this.$('input[type="checkbox"][name="propertyRemove"]:checked');
            var $controllers = this.$('input[type="checkbox"][name="controllerRemove"]:checked');
            var isProperties = false;

            if ($properties.length > 0 && $controllers.length > 0) {
                return;
            }

            var removeCount = 0;
            if ($properties.length > 0) {
                // Properties
                isProperties = true;
                ids = {};
                $properties.each(function (i, node) {

                    if (!ids[$(node).closest('.partial').data('id')]) {
                        ids[$(node).closest('.partial').data('id')] = [];
                    }
                    ids[$(node).closest('.partial').data('id')].push(node.getAttribute('value'));
                    removeCount++;

                });

            } else if ($controllers.length > 0) {
                // Controllers
                isProperties = false;
                ids = [];
                $controllers.each(function (i, node) {
                    var id = $(node).val();
                    if (ids.indexOf(id) < 0) {
                        ids.push(id);
                        removeCount++;
                    }
                });

            }

            if (confirm(removeCount + " " + this.__('delete-property'))) {

                remove(this, ids, isProperties);

            }
        }


        function remove(scope, ids, isProperties) {
            return new Promise(function (resolve, reject) {

                if (!this.model.get('ajax')) {
                    reject('ajax is empty!');
                }

                var data = {
                    id: this.model.get('id'),
                    ids: ids
                };
                if (isProperties) {
                    data['action'] = 'component-remove-properties';
                } else {
                    data['action'] = 'component-remove';
                }
                $.ajax({
                    cache: false,
                    method: 'post',
                    dataType: 'json',
                    data: {
                        'coma-data': data
                    },
                    url: this.model.get('ajax')
                }).done(function (resultData) {

                    if (resultData.result) {
                        resolve();
                    } else {
                        reject(':(');
                    }

                }.bind(this));

            }.bind(scope)).then(function () {

                this.$('[name="propertyRemove"]:checked').each(function (i, node) {
                    $(node).closest('.property').remove();
                });
                this.$('[name="controllerRemove"]:checked').each(function (i, node) {
                    $(node).closest('.partial').remove();
                });

            }.bind(scope)).catch(function (err) {
                console.error(err);
            });
        }

        function onChangeEntryType(e) {
            e.preventDefault();
            this.$('>form').submit();
        }

    }
)
;