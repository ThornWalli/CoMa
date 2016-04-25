coma.define(['underscore', 'jquery', '../../services/parser', '../../base/Controller', '../../base/DomModel'], function (_, $, parser, Controller, DomModel) {

    function onClickProperty(e) {
        //e.preventDefault();
        //return false;
    }

    function onChangeProperty(e) {
        var $node = $(e.currentTarget);
        editProperty(this.model, $node.attr('name'), $node.val());
    }

    function onChangePropertyName(e) {
        var $node = $(e.currentTarget);
        renameProperty(this.model, $node.data('lastValue'), $node.val());
    }

    function onChangeOpened(model, opened) {
        this.$el.toggleClass('opened', opened);
    }

    return Controller.extend({
            model: DomModel.extend({
                idAttribute: 'id',
                defaults: function () {
                    return {
                        idType: 'parentId',
                        id: null,
                        ajax: null,
                        loaded: false,
                        controllers: null
                    };
                }
            }),

            events: function () {
                return {

                    'click >.title': onClick,
                    'click >.properties>.property': onClickProperty,
                    'change >[name="controllerDisabled"]': onChangeDisabled,
                    'change >.properties>.property input[type="text"]:not([name="propertyName"])': onChangeProperty,
                    'change >.properties>.property input[type="text"][name="propertyName"]': onChangePropertyName,
                    'change >.properties>.property textarea:not([name="propertyName"])': onChangeProperty,
                    'change >.properties>.property textarea[name="propertyName"]': onChangePropertyName

                }
            },

            initialize: function () {
                Controller.prototype.initialize.apply(this, arguments);
                this.model.on('change:controllers', onChangeControllers.bind(this));
                this.model.on('change:opened', onChangeOpened.bind(this));
                this.model.on('change:properties', onChangeProperties.bind(this));
                this.model.on('change:' + this.model.get('idType'), onChangeId.bind(this));
                this.$properties = this.$('>.properties');
                this.$controllers = this.$('>.controllers');

                if (this.targetModel && this.targetModel.get('controllerTemplate')) {
                    this.model.set('propertyTemplate', this.targetModel.get('propertyTemplate'));
                    this.model.set('controllerTemplate', this.targetModel.get('controllerTemplate'));
                } else {
                    this.model.set('propertyTemplate', _.template(this.$('#property-template').html()));
                    this.model.set('controllerTemplate', _.template(this.$('#controller-template').html()));
                }

            }
        }
    );

    function onClick(e) {
        e.preventDefault();
        if (!this.model.get('opened')) {
            //if (!this.model.get('loaded')) {
            loadControllers(this.model);
            loadProperties(this.model);
            //    this.model.set('loaded', true)
            //}
            this.model.set('opened', true);
        } else {
            this.model.set('opened', false);
        }
    }

    function onChangeDisabled(e) {
        e.preventDefault();
        disable(this, $(e.currentTarget).val())
    }

    function onChangeId(model, value) {
        loadControllers(model);
        loadProperties(model);
    }

    function loadControllers(model) {
        return new Promise(function (resolve, reject) {
            if (!model.get('ajax')) {
                reject('ajax is empty!');
            }
            var data = {
                action: 'get-components'
            };
            data[model.get('idType')] = model.get('id');
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
                    resolve(resultData.controllers);
                } else {
                    reject(':(');
                }
            });
        }).then(function (controllers) {
            this.set('controllers', controllers);
        }.bind(model)).catch(function (err) {
            console.error(err);
        });
    }

    function loadProperties(model) {
        return new Promise(function (resolve, reject) {
            if (!model.get('ajax')) {
                reject('ajax is empty!');
            }
            var data = {
                action: 'get-component-properties',
                id: model.get('id')
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
                    resolve(resultData.properties);
                } else {
                    reject(':(');
                }
            });
        }).then(function (properties) {
            this.set('properties', properties);
        }.bind(model)).catch(function (err) {
            console.error(err);
        });
    }

    function renameProperty(model, lastName, name) {
        return new Promise(function (resolve, reject) {
            if (!model.get('ajax')) {
                reject('ajax is empty!');
            }
            var data = {
                action: 'component-rename-property',
                id: model.get('id'),
                lastName: lastName,
                name: name
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
                    resolve();
                } else {
                    reject(':(');
                }
            });
        }).then(function (resultData) {
        }).catch(function (err) {
            console.error(err);
        });
    }

    function disable(scope, disabled) {
        return new Promise(function (resolve, reject) {
            if (!this.model.get('ajax')) {
                reject('ajax is empty!');
            }
            var data = {
                action: 'component-disabled',
                id: this.model.get('id'),
                disabled: disabled
            };
            $.ajax({
                cache: false,
                method: 'post',
                dataType: 'json',
                data: {
                    'coma-data': data
                },
                url: this.model.get('ajax')
            }).done(function (resultData) {
                if (!resultData.result) {
                    resolve();
                } else {
                    reject(':(');
                }
            });
        }.bind(scope)).then(function (resultData) {
        }).catch(function (err) {
            console.error(err);
        });
    }

    function editProperty(model, name, value) {
        return new Promise(function (resolve, reject) {
            if (!model.get('ajax')) {
                reject('ajax is empty!');
            }
            var data = {
                action: 'component-edit-property',
                id: model.get('id'),
                name: name,
                value: escapeValue(value, false)
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
                    resolve();
                } else {
                    reject(':(');
                }
            });
        }).then(function (resultData) {
        }).catch(function (err) {
            console.error(err);
        });
    }


    function escapeValue(value, unescape) {
        if (unescape) {
            value = decodeURIComponent(value);
            value = value.replace(/\r/g, '');
            value = value.replace(/\n/g, '\\n');
            value = value.replace(/\t/g, '\\t');
            value = value.replace(/"/g, "&quot;");
        } else {
            value = value.replace(/\r/g, '');
            value = value.replace(/\\n/g, '\n');
            value = value.replace(/\\t/g, '\t');
            value = encodeURIComponent(value);
        }
        return value;
    }

    function escapeProperties(properties, unescape) {
        for (var key in properties) {
            if (Array.isArray(properties[key])) {
                properties[key] = escapeProperties(properties[key], unescape);
            } else {
                properties[key] = escapeValue(properties[key], unescape);
            }
        }
        return properties;
    }

    function onChangeProperties(model, properties) {
        this.$properties.empty();
        properties = escapeProperties(properties, true);
        console.log(properties);
        this.$properties.append(model.get('propertyTemplate')({
            properties: properties,
            canDisable: model.has('canDisable') ? model.get('canDisable') : this.targetModel.get('canDisable')
        }));
    }

    function onChangeControllers(model, controllers) {
        this.$controllers.empty();
        this.$controllers.append($(model.get('controllerTemplate')({
            controllers: controllers,
            canDisable: model.has('canDisable') ? model.get('canDisable') : this.targetModel.get('canDisable'),
            ajax: this.model.get('ajax')
        })));
        parser.parse(this.$controllers);
    }

});