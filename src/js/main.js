coma.define(['module', 'underscore', 'jquery', './services/parser'], function (module, _, $, parser) {


    //  Change underscore templating prefix % to {
    _.templateSettings.escape = /{{-([\s\S]+?)}}/g;
    _.templateSettings.evaluate = /{{([\s\S]+?)}}/g;
    _.templateSettings.interpolate = /{{=([\s\S]+?)}}/g;

    $(function () {
        parser.parse();
        console.log(module.config());
    });

});