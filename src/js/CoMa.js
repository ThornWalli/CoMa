coma.define(['jquery', 'base/Controller', 'base/DomModel', 'services/history', 'services/logs'], function ($, Controller, DomModel, history, logs) {

    return Controller.extend({

            model: DomModel.extend({

                defaults: function () {
                    return {
                        pageId: null,
                        deepModal: null,

                        ajax: null,
                        editProperty: null
                    };
                },


                getModalData: function () {
                    return {
                        'coma-page-id': this.get('pageId')
                    };
                }

            }),

            events: function () {
                return {}
            },

            initialize: function () {
                Controller.prototype.initialize.apply(this, arguments);

                $('.coma-cache-reset').on('click', onClickCacheReset.bind(this));
                $('.coma-global-properties').on('click', onClickGlobalProperties.bind(this));
                $('.coma-page-properties').on('click', onClickPageProperties.bind(this));
                $('.coma-mode-toggle>a').on('click', onClickModeToggle.bind(this));

            }
        }
    );

    function onClickModeToggle(e) {
        if ($('html').width() <= 782) {
            e.preventDefault();
        }

    }

    function onClickCacheReset(e) {
        e.preventDefault();
        if (this.model.get('ajax')) {

                $.ajax({
                    cache: false,
                    method: 'post',
                    dataType: 'json',
                    data: {
                        'coma-data': {
                            action: 'reset-cache',
                            id: this.model.get('pageId')
                        }
                    },
                    url: this.model.get('ajax')
                }).done(function (data) {
                    if (data.result) {
                        window.location.reload();
                    }
                    if (data.logs) {
                        logs.add(data.logs);
                    }
                }.bind(this));

        } else {
            console.error('ajax is empty!');
        }

    }

    function onClickGlobalProperties(e) {
        e.preventDefault();
        history.replace(this.model.get('deepModal'), 'global-properties');
    }

    function onClickPageProperties(e) {
        e.preventDefault();
        history.replace(this.model.get('deepModal'), 'page-properties&coma-page-id=' + this.model.get('pageId'));
    }

});
