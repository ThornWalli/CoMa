coma.define(['jquery', '../base/Collection', '../base/Model'], function ($, Collection, Model) {

    return Collection.extend({

        model: Model.extend({
            idAttribute: 'key',
            defaults: function () {
                return {
                    key: -1,
                    content: null,

                    method: 'post',

                }
            }
        }),

        initialize: function () {
            Collection.prototype.initialize(this, arguments);
        },

        getContent: function (key, callback, preventCache, data) {
            if (!data) {
                data = {};
            }
            var result = this.get(key);
            if (!result || !!preventCache) {
                $.ajax({
                    method: 'post',
                    url: key,
                    dataType: 'html',
                    cache: false,
                    data: data,
                    success: function (content) {
                        var test = callback(content);
                        this.add({key: key, content: test});
                    }.bind(this),
                    error: function () {
                        // hier muss ein 404 callback rein. bei erneutem versuch muss wiederholt ein request rausgeschickt werden
                    }
                });
            } else {
                callback(result.get('content'));
            }
        }
    });
});