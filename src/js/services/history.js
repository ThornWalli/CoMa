coma.define(['module', 'jquery', 'underscore', '../base/Collection', 'native-history', './history/Entry', './history/Callback', '../services/url'],
    function(module, $, _, Collection, History, Entry, Callback, url) {
    // both variables are required -> tempBrowserControlsUsed will be overwritten, so browserControlsUsed is required to save state permanently
    var tempBrowserControlsUsed = true;
    var browserControlsUsed = true;
    var defaultTitle = $('title').text();

    var h = new (Collection.extend({
        model: Entry,

        callbacks: new (Collection.extend({
            model: Callback
        }))(),

        initialize: function() {
            Collection.prototype.initialize.apply(this, arguments);

            History.Adapter.bind(window,'statechange', function() {
                browserControlsUsed = tempBrowserControlsUsed;
                tempBrowserControlsUsed = true;
                this.add(History.getState().data,{merge: true});
            }.bind(this));
            this.add(toCollection(decodeURIComponent(History.getState().cleanUrl)));

            this.on('change:value', function(model, value) {
                _.each(this.callbacks.where({key: model.get('name')}), function(entry, index) {
                    entry.get('callback')(value, model, detectCache(model.get('name'), value));
                })
            }.bind(this));

            History.replaceState(this.toJSON(), defaultTitle, toString(this.toJSON()));
        },

        /*
         * Register hash:change observer
         */
        register: function(name, callback) {
            // when no arguments are defined cancel registration
            if( name===undefined ){
                return;
            }

            // add callback to callback-collection
            var entry = this.callbacks.add({key: name, callback: callback});
            // get model from history-collection
            var model = this.getModel(name);
            // update browser state else forward & back browser button not working correct on page load
            console.log('HISTORY REGISTER:', name);
            this.replace(name, model.get('value'), getTitle(name, model.get('value')));
            // when key/value is located in browser url call callback
            if(model.get('value')){
                callback(model.get('value'), model, detectCache(name, model.get('value')));
            }

            //return callback collection entry
            return entry;
        },

        unregister: function(entry) {
            // remove entry from callback collection
            this.callbacks.remove(entry);
        },

        /*
         * Update model and update key/value pair in browser url
         *
         * Function Calls:
         * .update(deep<string>, uri<string>, title<string>, browserControls<boolean>)
         * .update(hashmap <object>, title<string>, browserControls<boolean>)
         */
        update: function(deep, uri) {
            var tmpCollection = getUpdatedTemporaryCollection(arguments, this);
            // Check if values are changed
            var currentCollection = getUpdatedTemporaryCollection({}, this);
            if(toString(currentCollection)==toString(tmpCollection)){
                return;
            }
            console.log('HISTORY UPDATE:', deep, uri);
            var newUrl = toString(tmpCollection);
            History.pushState(tmpCollection, getTitle(deep, uri, newUrl), newUrl);
        },

        /*
         * Replace model value and key/value pair in browser url
         *
         * Function Calls:
         * .replace(deep<string>, uri<string>, title<string>)
         * .replace(hashmap <object>, title<string>)
         */
        replace: function(deep, uri) {
            var tmpCollection = getUpdatedTemporaryCollection(arguments, this);
            console.log('HISTORY REPLACE:', deep, uri);
            var newUrl = toString(tmpCollection);
            History.replaceState(tmpCollection, getTitle(deep, uri, newUrl), newUrl);
        },

        /*
         * Remove model value and remove key/value pair from browser url
         *
         * Function Calls:
         * .remove(deep<string||array>, title<string>)
         */
        remove: function(deep) {
            var tmpCollection = getUpdatedTemporaryCollection([convertStringOrListToHashMap(deep)], this);
            var newUrl = toString(tmpCollection);
            History.pushState(tmpCollection, getTitle(deep, null, newUrl), newUrl);
        },

        reset: function(exclude, title, browserControls) {
            var list = [];
            _.each(this.models, function(model, index) {
                if(exclude) {
                    if(isString(exclude) && model.get('name') != exclude) {
                        list.push(model.get('name'));
                    } else if(exclude.indexOf(model.get('name')) == -1) {
                        list.push(model.get('name'));
                    }
                } else {
                    list.push(model.get('name'));
                }
            });
            this.remove(list, title, browserControls);
        },

        getModel: function(name) {
            var model = this.get(name);
            if(!model) {
                model = this.add(createEntry(name, null));
            }
            return model;
        },

        getValue: function(key) {
            var entry = this.get(key);
            if(entry) {
                return entry.get('value');
            } else {
                return null;
            }
        },

        isManualStateChange: function() {
            return browserControlsUsed;
        }
    }))();

    return h;

    function isString(obj) {
        return (typeof obj == 'string');
    }

    function getTitle(key, value, previewUrl) {
        if(!!value) {
            // get the title attribute of the clicked element or use the last history state title
            return $('[data-deep="' + key + '"][href="' + value + '"]').attr('title') || History.getState().title;
        } else {
            // search into history states of last equal url and extract the title of the previous state
            for(var i = History.getCurrentIndex(); i >= 0; i--) {
                if(History.getStateByIndex(i).url == previewUrl) {
                    return History.getStateByIndex(i).title;
                }
            }
            // when no result was found set the initial page title
            return defaultTitle;
        }
    }

    function detectCache(key, value) {
        return $('[data-deep="' + key + '"][href="' + value + '"]').data('prevent-cache') || false;
    }

    function toString(collection) {
        return url.convertCollectionToUrl(collection, module.config().pattern) || window.location.pathname;
    }

    function toCollection(string) {
        return url.convertUrlToCollection(string, module.config().pattern);
    }

    function getUpdatedTemporaryCollection(args, collection) {
        var tmpCollection = collection.toJSON();
        if(isString(args[0])) {
            tmpCollection = updateCollection(tmpCollection, args[0], args[1]);
        } else {
            _.each(args[0], function(value, key) {
                tmpCollection = updateCollection(tmpCollection, key, value);
            });
        }
        return tmpCollection;
    }

    function updateCollection(collection, deep, uri) {
        console.log(collection);
        var model = _.findWhere(collection, {name: deep});
        if(model) {
            model.value = normalizeValue(uri);
        } else {
            collection.push(createEntry(deep, normalizeValue(uri)));
        }
        return collection;
    }

    function normalizeValue(value) {
        var search = /#+/;
        if(search.test(value)) {
            return url.toObject(value).hash;
        } else if(!!!value) {
            return null;
        } else {
            return value;
        }
    }

    function createEntry(name, value) {
        return {
            name: name,
            value: value
        };
    }

    function getBrowserControlsFlag(args) {
        if(!isString(args[0])) {
            return args[2] || false;
        } else {
            return args[3] || false;
        }
    }

    function convertStringOrListToHashMap(stringOrList) {
        var hashmap = {};
        if(!isString(stringOrList)) {
            _.each(stringOrList, function(value) {
                hashmap[value] = null;
            })
        } else {
            hashmap[stringOrList] = null;
        }
        return hashmap;
    }
});