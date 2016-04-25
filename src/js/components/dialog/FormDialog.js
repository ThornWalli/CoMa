coma.define(['underscore', 'jquery', '../../base/Controller', '../../base/DomModel', '../../services/history', '../../services/logs', '../../utils/Fullscreen'], function (_, $, Controller, DomModel, history, logs, fullscreen) {

    return Controller.extend({

            model: DomModel.extend({

                defaults: function () {
                    return {
                        deepModal: null,
                        ajax: null,
                        pageId: null
                    };
                }

            }),

            events: function () {
                return {
                    'click [name="apply"]': onClickApply
                }
            },

            initialize: function () {
                Controller.prototype.initialize.apply(this, arguments);
            },

            onDone: function () {
                history.replace(this.model.get('deepModal'), null);
            },

            getAjaxData: function () {
                return {
                    properties: this.getProperties()
                };
            },

            getProperties: function () {

                var properties = {};

                tinyMCE.triggerSave();

                this.$('input, textarea, select').not('[type=button],[type=submit]').each(function (i, node) {
                    var $node = $(node);
                    var name = $node.attr('name');


                    var value = getNodeValue($node) || '';
                    value = value.replace(/\\/g, '\\\\');
                    value = encodeURIComponent(value);
                    var match;
                    if (name) {
                        match = name.match(/(.*)\[(.*)\]/);
                    }
                    if (match) {
                        if (!properties[match[1]]) {
                            properties[match[1]] = match.length > 2 ? {} : [];
                        }

                        if (match.length > 2) {
                            if (!properties[match[1]][match[2]]) {
                                properties[match[1]][match[2]] = [];
                            }
                            properties[match[1]][match[2]].push(value);
                        } else {
                            properties[match[1]].push(value);
                        }
                    } else if (name) {
                        if (!$node.is('[type="radio"]') || $node.is('[type="radio"]:checked') && value) {
                            properties[name] = value;
                        }
                    }
                });

                return properties;
            }


        }
    );

    function getNodeValue($node) {
        if ($node.is('[type="checkbox"]') || $node.is('[type="radio"]')) {
            // checkbox
            if ($node.is(':checked')) {
                return $node.val();
            } else {
                return null;
            }
        } else if ($node.is('input') || $node.is('textarea') || $node.is('select')) {
            return $node.val()
        } else {
            return $node.html();
        }
    }


    function onClickApply(e) {
        e.preventDefault();

        var promise = new Promise(function (resolve, reject) {

            var data = this.getAjaxData();
            if (!this.model.get('ajax')) {
                reject('ajax is empty!');
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
                    resolve(resultData);
                } else {
                    reject('area :(');
                }

            }.bind(this));


        }.bind(this)).then(function (data) {

            if (data.result) {
                this.onDone(data);
            } else {
                console.error('callback error :(');
            }
            if (data.logs) {
                logs.add(data.logs);
            }

        }.bind(this)).catch(function (err) {
            console.error(err);
        });

    }

});

