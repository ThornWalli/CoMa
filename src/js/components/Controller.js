coma.define(['underscore', 'jquery', '../services/parser', '../services/logs', '../base/Controller', '../base/DomModel', '../services/history'], function (_, $, parser, logs, Controller, DomModel, history) {

        return Controller.extend({

                $ghostEl: null,
                type: 'container',

                model: DomModel.extend({
                    idAttribute: 'id',
                    defaults: function () {
                        return {
                            wait: false,
                            containerType: null,
                            showContainer: false,
                            id: null,
                            parentId: null,
                            rank: 0,
                            ajax: null,
                            class: null,
                            deepModal: null,
                            disabled: false,
                            templateName: null,
                            showGhost: false
                        };
                    },
                    refreshRanks: function () {
                        this.trigger('refreshRanks');
                    }
                }),

                events: function () {
                    return {
                        'click': onClick,
                        'click >.header>div>ul>li>.edit': onClickEdit,
                        'click >.header>div>ul>li>.activate': onClickDisable,
                        'click >.header>div>ul>li>.deactivate': onClickDisable,
                        'click >.header>div>ul>li>.remove': onClickRemove,
                        'click >.header>div>ul>li>.move': onClickMove,
                        'click >.header>div>ul>li>.up': onClickUp,
                        'click >.header>div>ul>li>.down': onClickDown
                    }
                },

                initialize: function () {
                    Controller.prototype.initialize.apply(this, arguments);
                    setup(this);
                }
            }
        );

        function setup(scope) {
            scope.model.on('refreshRanks', onRefreshRanks.bind(scope));
            scope.model.on('change:disabled', onChangeDisabled.bind(scope));
            scope.model.on('change:showGhost', onChangeShowGhost.bind(scope));
            scope.$activate = scope.$('>.header>div>ul>li>.activate');
            scope.$deactivate = scope.$('>.header>div>ul>li>.deactivate');
            onChangeDisabled.bind(scope)(scope.model, scope.model.get('disabled'));
        }

        function onRefreshRanks() {
            var ranks = {};
            this.$('>.area-content').children('[data-id]').each(function (i, node) {
                ranks[i] = $(node).data('id');
            });
            refreshRanks(this, ranks);
        }

        function onChangeShowGhost(model, showGhost) {
            if (showGhost) {
                this.$ghostEl = $('<div class="coma-ghost-element" lang-placeholder="Placeholder" data-template-name="' + model.get('templateName') + '" />');
                this.$ghostEl.width(this.$el.width());
                this.$ghostEl.height(this.$el.height());
                this.$el.after(this.$ghostEl);
            } else {
                this.$ghostEl.remove();
                this.$ghostEl = null;
            }
        }

        function onChangeDisabled(model, disabled) {
            if (disabled) {
                this.$activate.parent().show();
                this.$deactivate.parent().hide();
            } else {
                this.$activate.parent().hide();
                this.$deactivate.parent().show();
            }
        }

        function onClick(e) {
            e.preventDefault();
            var parent = $(e.target).parents('[data-partial^="coma/component/controller"]').get(0);
            if (parent && parent == e.currentTarget) {
                if (this.targetModel && this.targetModel.get('moveComponent') && !this.targetModel.get('moveComponent').model.compare(this.model)) {
                    move(this, this.targetModel.get('moveComponent'), 'component');
                } else if (this.type == 'area' && this.model.get('moveComponent')) {
                    move(this, this.model.get('moveComponent'), 'area');
                } else {
                    $('[data-partial^="coma/component/controller"].coma-show, [data-partial^="coma/component/editable-property"].coma-show').removeClass('coma-show');
                    this.$el.addClass('coma-show');
                }
            }
        }

        function onClickEdit(e) {
            e.preventDefault();
            editComponent(this);
        }

        function onClickRemove(e) {
            e.preventDefault();
            removeController(this);
        }

        function onClickDisable(e) {
            e.preventDefault();
            disableComponent(this, parseInt($(e.currentTarget).data('disabled')));
        }

        function onClickMove(e) {
            e.preventDefault();
            this.model.set('move', true);
        }

        function onClickUp(e) {
            e.preventDefault();
            setRank(this, 'down');
        }

        function onClickDown(e) {
            e.preventDefault();
            setRank(this, 'up');
        }

        function getWrapperBeforeAreaContent($node) {
            if ($node.parent().hasClass('area-content')) {
                return $node;
            } else {
                return getWrapperBeforeAreaContent($node.parent());
            }
        }

        function moveContainer(type, $wrapper) {
            if (type == 'up') {
                $wrapper.next().after($wrapper);
            } else {
                $wrapper.prev().before($wrapper);
            }
        }

        function move(scope, moveComponent, type) {
            var hasRanks = false, ranks = {};
            switch (type) {
                case 'area':
                    var $contentArea = scope.$(':not([data-partial="coma/component/controller/area"]) .area-content');
                    if ($contentArea.length < 1) {
                        $contentArea = scope.$('>.area-content');
                    }
                    $contentArea = $contentArea.eq(0);
                    console.log($contentArea);
                    $contentArea.append(moveComponent.$el);
                    $contentArea.children('[data-id]').each(function (i, node) {
                        ranks[i] = $(node).data('id');
                        hasRanks = true;
                    });
                    break;
                case 'component':
                    scope.$el.before(moveComponent.$el);
                    getWrapperBeforeAreaContent(scope.$el).parent().children('[data-id]').each(function (i, node) {
                        ranks[i] = $(node).data('id');
                        hasRanks = true;
                    });
                    break;
            }
            if (hasRanks) {
                refreshRanks(scope, ranks);
            }
            console.log('move');
            moveComponent.model.set('move', false);
        }

        function refreshRanks(scope, ranks) {
            return new Promise(function (resolve, reject) {
                if (!this.model.get('ajax')) {
                    reject('ajax is empty!');
                }
                $.ajax({
                    cache: false,
                    method: 'post',
                    dataType: 'json',
                    data: {
                        'coma-data': {
                            action: 'component-set-ranks',
                            ranks: ranks
                        }
                    },
                    url: this.model.get('ajax')
                }).done(function (resultData) {
                    if (resultData.result) {
                        resolve()
                    } else {
                        reject('can\'t remove component');
                    }
                }.bind(this));
            }.bind(scope)).catch(function (err) {
                console.error(err);
            });
        }

        function removeController(scope) {
            return new Promise(function (resolve, reject) {
                if (!scope.model.get('id')) {
                    reject('id is empty!');
                } else if (!scope.model.get('ajax')) {
                    reject('ajax is empty!');
                }
                if (confirm(scope.__('delete'))) {
                    $.ajax({
                        cache: false,
                        method: 'post',
                        dataType: 'json',
                        data: {
                            'coma-data': {
                                action: 'component-remove',
                                id: scope.model.get('id')
                            }
                        },
                        url: scope.model.get('ajax')
                    }).done(function (resultData) {
                        if (resultData.result) {
                            resolve(true)
                        } else {
                            reject('can\'t remove component');
                        }
                    });
                } else {
                    resolve(false);
                }
            }.bind(scope)).then(function (remove) {
                if (remove) {
                    this.$el.remove();
                    // remove the copies from static areas
                    $('[data-id="' + this.model.get('id') + '"]').remove();
                }
            }.bind(scope)).catch(function (err) {
                console.error(err);
            });
        }

        function disableComponent(scope, disabled) {
            return new Promise(function (resolve, reject) {
                if (!scope.model.get('id')) {
                    reject('id is empty!');
                } else if (!scope.model.get('ajax')) {
                    reject('ajax is empty!');
                }
                if (confirm(this.__(disabled ? 'deactivate' : 'activate'))) {
                    $.ajax({
                        cache: false,
                        method: 'post',
                        dataType: 'json',
                        data: {
                            'coma-data': {
                                action: 'component-disabled',
                                id: this.model.get('id'),
                                disabled: disabled
                            }
                        },
                        url: this.model.get('ajax')
                    }).done(function (resultData) {
                        if (resultData.result) {
                            resolve(disabled)
                        } else {
                            reject('can\'t disable component');
                        }
                    }.bind(this));
                }
            }.bind(scope)).then(function (disabled) {
                this.model.set('disabled', disabled);
            }.bind(scope)).catch(function (err) {
                console.error(err);
            });
        }

        function setRank(scope, type) {
            return new Promise(function (resolve, reject) {
                if (!scope.model.get('ajax')) {
                    reject('ajax is empty!');
                }
                if (type == 'up' && $wrapper.next().length > 0 ||
                    type == 'down' && $wrapper.prev().length) {
                    $.ajax({
                        cache: false,
                        method: 'post',
                        dataType: 'json',
                        data: {
                            'coma-data': {
                                action: 'component-rank-' + type,
                                id: scope.model.get('id'),
                                rank: scope.model.get('rank')
                            }
                        },
                        url: scope.model.get('ajax')
                    }).done(function (resultData) {
                        if (resultData.result) {
                            resolve(type);
                        } else {
                            reject('can\'t change rank');
                        }
                    }.bind(scope));
                }
            }.bind(scope)).then(function (type) {
                moveContainer(type, getWrapperBeforeAreaContent(this.$el));
            }.bind(scope)).catch(function (err) {
                console.error(err);
            });
        }

        function editComponent(scope) {
            return new Promise(function (resolve, reject) {
                if (!scope.model.get('id') && this.type != 'area') {
                    reject('id is empty!');
                } else if (!scope.model.get('ajax')) {
                    reject('ajax is empty!');
                }
                if (scope.model.get('wait')) {
                    return;
                }

                var data = {
                    action: scope.type + '-select',
                    class: scope.model.get('class'),
                    position: scope.model.get('position')
                };
                if (this.model.get('id')) {
                    data.id = scope.model.get('id');
                }

                if (scope.type == 'area') {
                    if (this.model.get('parentId')) {
                        data.parentId = this.model.get('parentId');
                    } else if (this.model.get('pageId')) {
                        data.pageId = this.model.get('pageId');
                    }
                }

                scope.model.set('wait', true);
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
                        resolve(this);
                    } else {
                        reject(resultData);
                    }
                    scope.model.set('wait', false);
                }.bind(this));
            }.bind(scope)).then(function (scope) {
                history.replace(scope.model.get('deepModal'), scope.type + '-edit');
            }).catch(function (resultData) {
                if (typeof resultData == 'string') {
                    console.error(resultData);
                } else {
                    console.error(this.type + ' :(');
                    if (resultData.logs) {
                        logs.add(resultData.logs);
                    }
                }
            }.bind(scope));
        }

    }
);
