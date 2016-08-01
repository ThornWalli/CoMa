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

                var tabProperties = {}, properties;

                tinyMCE.triggerSave();

                this.$('input, textarea, select').not('[type=button],[type=submit]').each(function (i, node) {
                    var $node = $(node);
                    var name = $node.attr('name');
                    if (!!name) {
                        
                        var value = getNodeValue($node) || '';
                        value = value.replace(/\\/g, '\\\\');
                        value = encodeURIComponent(value);
                        
                        var regex = /\[([^[]+)*\]/g;
                        var match;
                        var path = [];
                        while ((match = regex.exec(name)) !== null) {
                            if (match.index === regex.lastIndex) {
                                regex.lastIndex++;
                            }
                            path.push(match[1]);
                        }
                        if (path.length > 0) {
                            var tabName = name.match(/^([^[]*)/g)[0];
                            if (!tabProperties[tabName]) {
                                tabProperties[tabName] = {};
                            }
                            if ($node.is('[type="checkbox"]') && !value) {
                                value = false;
                                return;
                            }
                            setVar(tabProperties[tabName], path, value);
                        }

                    }

                    //var value = getNodeValue($node) || '';
                    //value = value.replace(/\\/g, '\\\\');
                    //value = encodeURIComponent(value);
                    //var match, _properties = {};
                    //if (name) {
                    //    match = name.match(/(.*)\[(.*)\]/);
                    //}
                    //if (match) {
                    //    
                    //    _properties = {};
                    //    tabName = match[1];
                    //    if (!properties[tabName]) {
                    //        properties[tabName] = {};
                    //    }
                    //    if (!properties[match[2]]) {
                    //        _properties[match[2]] = match.length > 2 ? {} : [];
                    //    }
                    //    if (match.length > 3) {
                    //        if (!_properties[match[2]][match[3]]) {
                    //            _properties[match[2]][match[3]] = [];
                    //        }
                    //        _properties[match[2]][match[3]].push(value);
                    //    } else {
                    //        _properties[match[2]]=value;
                    //    }
                    //    
                    //} else if (name) {
                    //    if (!$node.is('[type="radio"]') || $node.is('[type="radio"]:checked') && value) {
                    //        _properties[name] = value;
                    //    }
                    //}
                    //
                    //if (tabName) {
                    //    properties[tabName] = _.extend(properties[tabName], _properties);
                    //}

                });

                console.log(tabProperties);
                return tabProperties;
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

    function setVar(data, path, value) {
        if (!data) {
            data = [];
        }
        var name = path.shift();
        console.log(name);

        if (!data[name] || typeof data != 'object') {
            data[name] = {};
        }
        if (path.length > 0) {
            data[name] = setVar(data[name], path, value);
        } else {
            data[name] = value;
        }

        return data;

    }

});

