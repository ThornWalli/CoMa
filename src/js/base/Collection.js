coma.define(['underscore', 'jquery', './Events'], function (_, $, Events) {

    function Collection(options, data) {
        Events.prototype.constructor.apply(this, arguments);
        this.models = [];
        this._byId = [];
        this.initialize(options, data);
    };

    Collection.prototype.add = function (models, options) {
        if (!_.isArray(models)) {
            models = [models];
        }

        var toAdd = [];
        for (var i = 0; i < models.length; i++) {
            var modelAttributes = models[i];
            if (modelAttributes.cid) {
                modelAttributes = modelAttributes.attributes;
            }
            var model = new this.model();

            var id = modelAttributes[model.idAttribute];
            if (typeof id == 'undefined') {
                id = model.getId();
            }

            // Überprüfen ob Model schon existiert
            if (!!this._byId[id]) {
                this._byId[id].set(modelAttributes, {
                    merge: true
                });
            } else {

                model.set(modelAttributes, options);
                this.models.push(model);
                this._addReference(model);
                this.trigger('add', toAdd);

            }

            toAdd.push(model);
        }


        if (toAdd.length == 1) {
            return toAdd[0];
        }
        return toAdd;

    };
    Collection.prototype.remove = function (model) {
        if (!_.isArray(model)) {
            model = [model];
        }
        for (var i = 0; i < model.length; i++) {
            delete this.models[model.get(model.idAttribute)];
        }
    };
    Collection.prototype.toJSON = function (options) {

        var models = [];
        this.each(function (model) {
            models.push(model);
        });
        return _.map(models, function (model) {
            return model.toJSON(options);
        });

    };
    Collection.prototype.initialize = function (options, data) {
        this.attributes = {};
        Events.prototype.constructor.apply(this, arguments);
    };

    Collection.prototype.get = function (obj) {
        if (obj == null) return void 0;
        return this._byId[obj] || this._byId[obj[this.idAttribute]] || this._byId[obj.cid];
    };
    Collection.prototype.each = function (callback) {
        for (var i = 0; i < this.models.length; i++) {
            callback(this.models[i], this);
        }
    };
    Collection.prototype.where = function (attrs, first) {
        if (_.isEmpty(attrs)) return first ? void 0 : [];
        return _[first ? 'find' : 'filter'](this.models, function (model) {
            for (var key in attrs) {
                if (attrs[key] !== model.get(key)) return false;
            }
            return true;
        });
    };

    // Internal method to create a model's ties to a collection.
    Collection.prototype._addReference = function (model) {
        this._byId[model.getId()] = model;
        if (!model.collection) {
            model.collection = this;
        }
        model.on('all', this._onModelEvent, this);
    };
    Collection.prototype._removeReference = function (model) {
        if (this === model.collection) {
            delete model.collection;
        }
        model.off('all', this._onModelEvent, this);
    };
    Collection.prototype._onModelEvent = function (event, model, collection, options) {
        if ((event === 'add' || event === 'remove') && collection !== this) {
            return;
        }
        if (event === 'remove') {
            this.remove(model, options);
        }
        if (model && event === 'change:' + model.idAttribute) {
            delete this._byId[model.previous(model.idAttribute)];
            if (model.id != null) this._byId[model.id] = model;
        }
        this.trigger.apply(this, arguments);
    };
    Collection.extend = function (protoProps, staticProps) {
        var parent = this;
        var child;

        // The constructor function for the new subclass is either defined by you
        // (the "constructor" property in your `extend` definition), or defaulted
        // by us to simply call the parent's constructor.
        if (protoProps && _.has(protoProps, 'constructor')) {
            child = protoProps.constructor;
        } else {
            child = function () {
                return parent.apply(this, arguments);
            };
        }

        // Add static properties to the constructor function, if supplied.
        _.extend(child, parent, staticProps);

        // Set the prototype chain to inherit from `parent`, without calling
        // `parent`'s constructor function.
        var Surrogate = function () {
            this.constructor = child;
        };
        Surrogate.prototype = parent.prototype;
        child.prototype = new Surrogate;

        // Add prototype properties (instance properties) to the subclass,
        // if supplied.
        if (protoProps) _.extend(child.prototype, protoProps);

        // Set a convenience property in case the parent's prototype is needed
        // later.
        child.__super__ = parent.prototype;

        return child;
    };

    return Collection;

});