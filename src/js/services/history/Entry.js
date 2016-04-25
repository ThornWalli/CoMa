coma.define(['../../base/Model'], function(Model) {
    return Model.extend({
        idAttribute: 'name',
        defaults: function () {
            return {
                name: null,
                value: null
            }
        }
    })
});