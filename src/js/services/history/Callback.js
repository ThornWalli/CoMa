coma.define(['../../base/Model', '../random'], function(Model, random) {
    return Model.extend({
        defaults: function() {
            return {
                id: random.id(),
                key: null,
                callback: null
            }
        }
    });
});
