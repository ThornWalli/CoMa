coma.define(['jquery', 'underscore', './Model'], function ($, _, Model) {

    return Model.extend({
        defaults: function () {
            return {};
        },

        initialize: function (options, data) {
            Model.prototype.initialize.apply(this, arguments);

            //copy values
            for (var key in this.attributes) {
                if (options && options[key] != undefined) {
                    setValue(this, key, options[key]);
                } else if (data && data[key] != undefined) {
                    setValue(this, key, data[key]);
                }
            }
        },
        remove: function () {
            Model.prototype.remove.apply(this, arguments);
            this.trigger('remove');

        }
    });

    function setValue(obj, key, value) {
        if (obj[key.toUpperCase()]) {
            obj.set(key, convertValueToCorrectType(obj[key.toUpperCase()][value.toUpperCase()]));
        } else {
            obj.set(key, convertValueToCorrectType(value));
        }
    }

    function convertValueToCorrectType(value) {
        return JSON.parse(JSON.stringify(value));
    }

});