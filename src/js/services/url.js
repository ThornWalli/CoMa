coma.define(['jquery', 'underscore'], function ($, _) {
    var parser = document.createElement('a');

    var u = {
        toObject: function (url) {
            $(parser).attr({href: url});
            return {
                protocol: parser.protocol,                          // => "http:"
                host: parser.host,                                  // => "example.com:3000"
                hostname: parser.hostname,                          // => "example.com"
                port: parser.port,                                  // => "3000"
                pathname: parser.pathname,                          // => "/pathname/"
                query: queryToObject(parser.search.substring(1)),   // => "{search: 'test'}"
                queryString: parser.search.substring(1),            // => "search=test"
                hash: parser.hash.substring(1),                     // => "hash"
                toString: function () {
                    if (this.port) {
                        this.host = this.host.replace(/([\S]+):(?:\d+)/, '$1:8060');
                    }
                    var params = '';
                    if (this.query) {
                        params = '?' + $.param(this.query);
                    }
                    var hash = '';
                    if (this.hash && this.hash != '') {
                        hash = '#' + this.hash;
                    }
                    return this.protocol + '//' + this.host + this.pathname + params + hash;
                }
            }
        },

        makeShareable: function (url) {
            var obj = this.toObject(url);
            obj.query.hash = obj.hash;
            obj.hash = null;
            return obj.toString();
        },

        convertUrlToCollection: function (string, pattern) {
            var stack = null;
            switch (pattern) {
                case 'REST':
                    break;
                case 'GETREST':
                    stack = this.convertRESTUrlToCollection(string);
                    break;
                default:
                    stack = this.convertGETUrlToCollection(string);
                    break;
            }
            return stack;
        },

        convertGETUrlToCollection: function (url) {
            return convertToCollection(this.toObject(url).queryString, '&', function (collection, splittedQuery) {
                var keyValue = null;
                for (var i = 0, item; item = splittedQuery[i]; ++i) {
                    keyValue = item.split('=');
                    var value = true;
                    if (keyValue.length > 1) {
                        value = decodeURIComponent(keyValue[1].replace(/\+/g, ' '))
                    }
                    collection.push({name: keyValue[0], value: value});
                }
                return collection;
            });
        },

        convertRESTUrlToCollection: function (url) {
            return convertToCollection(this.toObject(url).queryString, '/', function (collection, splittedQuery) {
                splittedQuery = _.without(splittedQuery, '', undefined, null);
                for (var i = 0; i < splittedQuery.length; i += 2) {
                    collection.push({
                        name: splittedQuery[i],
                        value: decodeURIComponent(splittedQuery[i + 1].replace(/\+/g, ' '))
                    });
                }
                return collection;
            });
        },

        convertCollectionToUrl: function (collection, pattern) {
            var queryString = '';
            switch (pattern) {
                case 'REST':
                    break;
                case 'GETREST':
                    queryString = this.convertCollectionToRESTUrl(collection);
                    if (!!queryString) {
                        return window.location.origin + window.location.pathname + '?/' + queryString;
                    }
                    break;
                default:
                    queryString = this.convertCollectionToGETUrl(collection);
                    if (!!queryString) {
                        return window.location.origin + window.location.pathname + '?' + queryString;
                    }
                    break;
            }
            return window.location.origin + window.location.pathname;
        },

        convertCollectionToGETUrl: function (collection) {
            return convertCollectionToQueryList(collection, '&', '=');
        },

        convertCollectionToRESTUrl: function (collection) {
            return convertCollectionToQueryList(collection, '/', '/');
        }
    }

    return u;

    function queryToObject(query) {
        var paramValueStrings = query.split('&');
        var obj = {}, keyValue = [];
        for (var i = 0, item; item = paramValueStrings[i]; ++i) {
            keyValue = item.split('=');
            obj[keyValue[0]] = keyValue[1] || true;
        }
        return obj;
    }

    function convertToCollection(queryString, separator, converter) {
        var collection = [], keyValue = [];
        if ((!!queryString)) {
            collection = converter(collection, queryString.split(separator));
        }
        return collection;
    }

    function convertCollectionToQueryList(collection, separator, subSeparator, converter) {
        var queryList = [];
        for (var i = 0, item; item = collection[i]; ++i) {
            if (!!item.value) {
                queryList.push(item.name + subSeparator + item.value);
            }
        }
        return queryList.join(separator);
    }
});