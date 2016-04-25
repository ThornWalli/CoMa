coma.define([], function () {
    var generatedIDs = [];

    return {
        color: function() {
            var color = [];
            for(var i = 0; i < 3; i++) {
                color.push(Math.round(Math.random() * 255));
            }
            return 'rgb(' + color.join(',') + ')';
        },

        id: function() {
            var identifier = String.fromCharCode(97 + Math.random() * 25) + Math.random().toString(36).substring(7);

            if(generatedIDs.indexOf(identifier) == -1) {
                generatedIDs.push(identifier);
                return identifier;
            } else {
                return this.id();
            }
        },

        integer: function(min, max) {
            return Math.floor(Math.random() * (max - min + 1)) + min;
        }
    }
});