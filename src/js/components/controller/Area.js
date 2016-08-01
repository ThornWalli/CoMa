coma.define(['underscore', 'jquery', '../../services/parser', '../../services/logs', '../../components/Controller', '../../services/history'], function (_, $, parser, logs, Controller, history) {

    return Controller.extend({

            type: 'area',

            model: Controller.prototype.model.extend({

                defaults: function () {
                    return _.extend({
                        position: null,
                        pageId: null,
                        parentId: null,
                        moveComponent: null,
                        highlight: false
                    }, Controller.prototype.model.prototype.defaults());
                },

                copyComponent: function (id) {
                    this.trigger('copyComponent', id);
                },

                loadComponent: function (id, saveRanks) {
                    this.trigger('loadComponent', id, saveRanks);
                },

                loadComponents: function () {
                    this.trigger('loadComponents');
                },

                refreshNode: function (saveRanks) {
                    this.trigger('refreshNode', saveRanks);
                }

            }),

            events: function () {
                return _.extend({
                    'click >.header>div>ul>li>.append': onClickAppend,
                    'click >.append': onClickAppend
                }, Controller.prototype.events());
            },

            initialize: function () {
                Controller.prototype.initialize.apply(this, arguments);
                setup(this);
            }
        }
    );

    function setup(scope) {

        scope.model.on('change:id', onChangeId.bind(scope));
        scope.model.on('change:highlight', onChangeHighlight.bind(scope));
        scope.model.on('copyComponent', onCopyComponent.bind(scope));
        scope.model.on('loadComponent', onLoadComponent.bind(scope));
        scope.model.on('loadComponents', onLoadComponents.bind(scope));
        scope.model.on('refreshNode', onRefreshNode.bind(scope));
        //if (scope.targetModel) {
        //    scope.model.set('pageId', scope.targetModel.get('pageId'));
        //}
    }

    function onChangeId(model, id) {
        this.$el.attr('data-id', id);
    }

    function onChangeHighlight(model, highlight) {
        if (highlight) {
            this.$el.addClass('coma-highlight');
        } else {
            this.$el.removeClass('coma-highlight');
        }
    }

    function onClickAppend(e) {
        e.preventDefault();
        append(this);
    }

    function append(scope) {
        return new Promise(function (resolve, reject) {
            if (!this.model.get('ajax')) {
                reject('ajax is empty!');
            }
            if (this.model.get('wait')) {
                return;
            }
            this.model.set('wait', true);

            var data = {
                action: 'area-select',
                class: this.model.get('class'),
                position: this.model.get('position')
            };
            if (this.model.get('id')) {
                data.id = this.model.get('id');
            }
            if (this.model.get('parentId')) {
                data.parentId = this.model.get('parentId');
            } else if (this.model.get('pageId')) {
                data.pageId = this.model.get('pageId');
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
            });
        }.bind(scope)).then(function (resultData) {
            this.model.set('id', resultData.id);
            history.replace(this.model.get('deepModal'), 'component-select');
            if (resultData.logs) {
                logs.add(resultData.logs);
            }
            this.model.set('wait', false);
        }.bind(scope)).catch(function (err) {
            console.error(err);
            this.model.set('wait', false);
        }.bind(scope));
    }

    function copyComponent(scope, componentId) {
        return new Promise(function (resolve, reject) {
            if (!this.model.get('ajax')) {
                reject('ajax is empty!');
            }
            if (this.model.get('wait')) {
                return;
            }
            this.model.set('wait', true);
            $.ajax({
                cache: false,
                method: 'post',
                dataType: 'json',
                data: {
                    'coma-data': {
                        action: 'area-copy-component',
                        id: this.model.get('id'),
                        componentId: componentId
                    }
                },
                url: this.model.get('ajax')
            }).done(function (resultData) {
                if (resultData.result) {
                    resolve(resultData.id);
                } else {
                    reject('area :(');
                }
            });
        }.bind(scope)).then(function (id) {
            onLoadComponent.bind(this)(id, true);
            this.model.set('wait', false);
        }.bind(scope)).catch(function (err) {
            console.error(err);
            this.model.set('wait', false);
        }.bind(scope));
    }

    function onCopyComponent(id) {
        copyComponent(this, id);
    }

    /**
     * Refresh Node from the area.
     */
    function onRefreshNode(saveRank) {
        return new Promise(function (resolve, reject) {
            if (!this.model.get('ajax')) {
                reject('ajax is empty!');
            }
            $.ajax({
                cache: false,
                method: 'get',
                dataType: 'html',
                data: {
                    'coma-action': 'render-area',
                    'coma-area-id': this.model.get('id'),
                    'coma-edit-mode': true
                },
                url: this.model.get('ajax')
            }).done(function (resultData) {
                resolve(resultData);
            });
        }.bind(this)).then(function (resultData) {

            var $parent = this.$el.parent();
            var $newArea = $(resultData);

            $newArea.css('opacity', 0);

            var $wrapper = $('<div />');
            this.$el.after($wrapper);
            $wrapper.append(this.$el);
            $wrapper.height(this.$el.height());
            $wrapper.css({
                'position': 'relative'
            });
            this.$el.css({
                'position': 'absolute',
                'width': '100%'
            });
            this.$el.after($newArea)
            this.$el.animate({
                opacity: 0

            }, 650, function () {
                this.$el.remove();
                $wrapper.height(null);
                $wrapper.after($newArea)
                $wrapper.remove();
                parser.parse($parent);
                $newArea.animate({
                    opacity: 1
                }, 650, function () {
                    if (saveRank) {
                        this.model.refreshRanks();
                    }
                }.bind(this));
            }.bind(this));

        }.bind(this)).catch(function (err) {
            console.error(err);
        });
    }

    function getAreaContent(scope) {
        var $areaContent = scope.$('div:not([data-partial="coma/component/controller/area"])>.area-content');
        if ($areaContent.length < 1) {
            $areaContent = scope.$('>.area-content');
        }
        return $areaContent;
    }

    function onLoadComponent(id, saveRank) {
        return new Promise(function (resolve, reject) {
            if (!this.model.get('ajax')) {
                reject('ajax is empty!');
            }
            $.ajax({
                cache: false,
                method: 'get',
                dataType: 'html',
                data: {
                    'coma-action': 'render-component',
                    'coma-component-id': id,
                    'coma-page-id': this.targetModel.get('pageId'),
                    'coma-edit-mode': true
                },
                url: this.model.get('ajax')
            }).done(function (resultData) {
                resolve(resultData);
            });
        }.bind(this)).then(function (resultData) {
            var $node = this.$('[data-id="' + id + '"]');
            var $areaContent = getAreaContent(this);
            if ($node.length < 1) {
                $areaContent.each(function (i, areaContent) {
                    var $newNode = $(resultData);
                    var $areaContent = $(areaContent);
                    $newNode.css('opacity', 0);
                    $node.before($newNode);
                    $areaContent.append($newNode);
                    parser.parse($areaContent);
                    $newNode.animate({
                        opacity: 1
                    }, 650, function () {
                        if (saveRank) {
                            this.model.refreshRanks();
                        }
                    }.bind(this));
                }.bind(this));
            } else {
                $node.each(function (i, node) {
                    var $newNode = $(resultData);
                    var $node = $(node);
                    $newNode.css('opacity', 0);
                    $node.before($newNode);
                    if ($node.length > 0) {
                        $node.before();
                        $('> *', $node).detach();
                        $node.animate({
                            opacity: 0
                        }, 650, function () {
                            setTimeout(function () {
                                $node.remove();
                            }, 650)
                        });
                    } else {
                        $areaContent.append($newNode);
                    }
                    parser.parse($areaContent);
                    $newNode.animate({
                        opacity: 1
                    }, 650, function () {
                        if (saveRank) {
                            this.model.refreshRanks();
                        }
                    }.bind(this));
                }.bind(this));
            }
        }.bind(this)).catch(function (err) {
            console.error(err);
        });
    }

    function onLoadComponents() {
        return new Promise(function (resolve, reject) {
            if (!this.model.get('ajax')) {
                reject('ajax is empty!');
            }
            $.ajax({
                cache: false,
                method: 'get',
                dataType: 'html',
                data: {
                    'coma-action': 'render-components',
                    'coma-area-id': this.model.get('id'),
                    'coma-edit-mode': true
                },
                url: this.model.get('ajax')
            }).done(function (resultData) {
                resolve(resultData);
            });

        }.bind(this)).then(function (resultData) {

            var $areaContent = getAreaContent(this);
            $('> *', $areaContent).detach();

            var $nodes = $(resultData);

            $areaContent.animate({
                opacity: 0
            }, 650, function () {
                $areaContent.empty();
                $areaContent.append($nodes);
                parser.parse($areaContent);
            });


            setTimeout(function () {
                $areaContent.animate({
                    opacity: 1
                }, 650, function () {
                });
            }, 650);

        }.bind(this)).catch(function (err) {
            console.error(err);
        });
    }

});
