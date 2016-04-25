coma.define(['underscore', 'jquery', '../../services/parser', '../../base/Controller', '../../base/DomModel'], function (_, $, parser, Controller, DomModel) {

    return Controller.extend({

        model: DomModel.extend({
            defaults: function () {
                return {
                    ajax: null,
                    attachments: null,
                    sync: true,
                    progress: null,
                    maxProgress: null
                };
            }
        }),

        events: function () {
            return {
                'click [name="refreshThumbnails"]': onClickRefresh
            };
        },

        initialize: function () {
            Controller.prototype.initialize.apply(this, arguments);
            this.model.on('change:attachments', onChangeAttachments.bind(this));
            this.model.on('change:progress', onChangeProgress.bind(this));
            this.$results = this.$('.results');
            this.$progressBar = this.$('.progress>div');
        }
    });

    function onClickRefresh(e) {
        e.preventDefault();
        getAttachments(this);
    }

    function refreshAttachment(scope, id, callback, sync) {
        return new Promise(function (resolve, reject) {

            var $node = $('<li>Attachment Id: ' + id + '</li>');
            $node.addClass('coma-loading');
            this.$results.append($node);

            $.ajax({
                cache: false,
                method: 'get',
                dataType: 'json',
                data: {
                    'coma-action': 'refresh-attachment',
                    'coma-id': id
                },
                url: scope.model.get('ajax')
            }).done(function (resultData) {
                if (resultData.result) {
                    resolve($node);
                } else {
                    reject('refresh :(');
                }
            });

        }.bind(scope)).then(function ($node) {
            $node.removeClass('coma-loading');
            $node.addClass('coma-loaded');
            if (sync) {
                callback();
            }
        }).catch(function (err) {
            console.error(err);
        });
    }

    function onChangeProgress(model, progress) {
        console.log(progress);
        progress = parseInt(progress * 100) + '%';
        this.$progressBar.width(progress);
        this.$progressBar.attr('data-progress', progress);
    }

    function onChangeAttachments(model, attachments) {

        model.set('progress', 0);
        model.set('maxProgress', attachments.length);

        if (!!attachments) {
            this.$results.empty();
            var loop = function () {
                model.set('progress', 1 - (attachments.length / model.get('maxProgress')));
                if (attachments.length > 0) {
                    refreshAttachment(this, attachments.shift(), loop.bind(this), model.get('sync'));
                } else {
                    model.set('attachments', null);
                }
            }.bind(this);
            loop();
        }
    }


    function getAttachments(scope) {
        return new Promise(function (resolve, reject) {
            $.ajax({
                cache: false,
                method: 'get',
                dataType: 'json',
                data: {
                    'coma-action': 'prepare-attachments-refresh',
                    'coma-type': 'image'
                },
                url: this.model.get('ajax')
            }).done(function (resultData) {
                if (resultData.result) {
                    resolve(resultData.attachments);
                } else {
                    reject('attachments :(');
                }
            });
        }.bind(scope)).then(function (attachments) {
            this.model.set('attachments', attachments)
        }.bind(scope)).catch(function (err) {
            console.error(err);
        });
    }

    function onClickEdit(e) {
        e.preventDefault();


    }

})
;