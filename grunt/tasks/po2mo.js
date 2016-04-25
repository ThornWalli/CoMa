var fs = require('fs');
var gettextParser = require('gettext-parser');
//
//var path = require('path');
//var glob = require('glob');
//var handlebars = require('grunt-compile-handlebars/node_modules/handlebars');

module.exports = function (grunt) {
    return function () {

        var options = this.options({
            files: []
        });

        this.files.forEach(function (file) {
            var input = fs.readFileSync(file.src[0]);
            var po = gettextParser.po.parse(input);
            var output = gettextParser.mo.compile(po);
            fs.writeFileSync(file.dest, output);
            console.log('file ' + file.dest + ' created');
        });

    }
}