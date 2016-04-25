coma.require.config({
    packages: [{
        name: "cm",
        main: "lib/codemirror"
    }],

    shim: {
        'native-history': {
            exports: 'History'
        }
    },

    map: {
        '*': {
            jquery: 'multiversion/jquery-master',
            underscore: 'multiversion/underscore-master'
        },
        jQuery: {
            jquery: 'jquery'
        }
    },

    paths: {
        'codemirror' : '../../js/codemirror',
        'libs' : '../../js/libs'
    }

});

coma.require(['libs', 'codemirror'], function (module) {
    coma.require(['main']);
});