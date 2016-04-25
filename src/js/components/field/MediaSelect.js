coma.define(['underscore', 'jquery', '../../base/Controller', '../../base/DomModel', '../../services/history'], function (_, $, Controller, DomModel, history) {

    var globalFrame = {};

    return Controller.extend({

            model: DomModel.extend({

                defaults: function () {
                    return {
                        mediaType: 'default',
                        id: null,
                        attachment: null

                    };
                }

            }),

            events: function () {
                return {
                    'click .select': onClickSelect,
                    'click .remove': onClickRemove
                }
            },

            initialize: function () {
                Controller.prototype.initialize.apply(this, arguments);
                setup(this);
            }
        }
    );

    function selectAttachment(scope, id) {
        getAttachment(id, function (attachment) {
            this.model.set('attachment', attachment);
            this.model.set('id', attachment.id);
        }.bind(scope))
    }

    function setup(scope) {

        scope.$titleInput = scope.$('[type="text"]');
        scope.$idInput = scope.$('[type="hidden"]');
        scope.$preview = scope.$('.preview>img');
        scope.model.on('change:attachment', onChangeAttachment.bind(scope));

        if (!!scope.model.get('id')) {
            selectAttachment(scope, scope.model.get('id'));
        }


    }

    function showMediaSelect(scope) {


        var config = {
            title: jQuery(this).data('uploader_title'),
            button: {
                text: jQuery(this).data('uploader_button_text'),
            },
            frame: 'select',
            multiple: false,
            libary: {}
        };

        var mediaType = scope.model.get('mediaType');

        if (mediaType != 'default') {
            config.libary = library = {
                type: scope.model.get('mediaType')
                //uploadedTo : wp.media.view.settings.post.id
            };
        }

        if (scope.model.get('value')) {
            config.libary.post__in = scope.model.get('value');
        }


        var frame = null;
        if (globalFrame[mediaType]) {
            if (globalFrame[mediaType]) {
                frame = globalFrame[mediaType];
            }
        } else {
            frame = wp.media.frames.globalFrame = wp.media(config);
            globalFrame[mediaType] = frame;
        }

        if (frame.state()) {
            // unselected
            frame.state().get('selection').reset();
        }

        frame.on('select', onSelect.bind(scope));
        frame.on('open', function () {

            var selection = frame.state().get('selection');
            var attachment = scope.model.get('attachment');
            if (!!attachment) {
                attachment = getAttachment(attachment.id, function () {
                    selection.add(attachment ? [attachment] : [])
                });
            }
        }.bind(scope));

        frame.open();

    }

    function getAttachment(id, callback) {
        var attachment = wp.media.attachment(id);
        attachment.fetch({
            success: function (attachment) {
                callback(attachment);
            }
        });

    }

    function onChangeAttachment(model, attachment) {
        if (!attachment) {
            this.$titleInput.val(null);
            this.$idInput.val(null);
            this.$preview.attr('src', null);
        } else {
            this.$titleInput.val(attachment.get('title'));
            this.$idInput.val(attachment.get('id'));
            this.$preview.attr('src', attachment.get('sizes').thumbnail.url);
        }
    }

    function onClickSelect(e) {
        e.preventDefault();
        showMediaSelect(this);

    }

    function onClickRemove(e) {
        e.preventDefault();
        this.model.set('attachment', null);
    }

    function onSelect() {
        this.model.set('attachment', globalFrame[this.model.get('mediaType')].state().get('selection').first());
    }

});