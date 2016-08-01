var fs = require('fs');
var gettextParser = require('gettext-parser');

module.exports = function (grunt) {
    return function () {        
        for (var src in this.data.files) {
            var dest = this.data.files[src];
            var input = fs.readFileSync(src);
            var po = gettextParser.po.parse(input);
            var output = gettextParser.mo.compile(po);
            fs.writeFileSync(dest, output);
            console.log('file ' + dest + ' created');

        }
    }
}
