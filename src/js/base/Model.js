coma.define(['underscore', 'jquery', './Events'], function (_, $, Events) {

    function Model(attributes, options) {
        this.cid = _.uniqueId('model');
        this._changing = false;
        this._pending = false;
        this._previousAttributes = null;
        this.initialize(attributes, options);
    };
    Model.prototype.idAttribute = null;
    Model.prototype.defaults = function () {
        return {};
    };
    Model.prototype.get = function (name) {
        return this.attributes[name];
    };
    Model.prototype.has = function (name) {
        return typeof this.attributes[name] != 'undefined';
    };
    Model.prototype.set = function (name, value, options) {
        var current, changing, prev, attrs, attr, unset, silent, changes;
        changing = this._changing;
        this._changing = true;

        if (typeof name === 'object') {
            attrs = name;
            options = value;
        } else {
            (attrs = {})[name] = value;
        }

        options || (options = {});
        changes = [];
        unset = options.unset;
        silent = options.silent;

        if (!changing) {
            this._previousAttributes = _.clone(this.attributes);
            this.changed = {};
        }
        current = this.attributes, prev = this._previousAttributes;

        // For each `set` attribute, update or delete the current value.
        for (attr in attrs) {
            value = attrs[attr];
            if (!_.isEqual(current[attr], value)) changes.push(attr);
            if (!_.isEqual(prev[attr], value)) {
                this.changed[attr] = value;
            } else {
                delete this.changed[attr];
            }
            unset ? delete current[attr] : current[attr] = value;
        }

        if (!silent) {
            if (changes.length) this._pending = options;
            for (var i = 0, l = changes.length; i < l; i++) {
                this.trigger('change:' + changes[i], this, current[changes[i]], options);
            }
        }

        // Tiggert nur wenn es keine ver�nderrungen gab.
        if (changing) return this;
        if (!silent) {
            while (this._pending) {
                options = this._pending;
                this._pending = false;
                this.trigger('change', this, options);
            }
        }

        this._pending = false;
        this._changing = false;

    };

    Model.prototype.initialize = function (attributes, options) {
        this.attributes = _.extend(this.defaults(), attributes);
        Events.prototype.constructor.apply(this, arguments);


    };
    Model.prototype.remove = function () {

    };
    Model.prototype.getId = function () {
        if (!!this.idAttribute) {
            return this.attributes[this.idAttribute];
        } else {
            return this.cid;
        }
    };

    Model.prototype.previous = function (attr) {
        if (attr == null || !this._previousAttributes) return null;
        return this._previousAttributes[attr];
    };

    Model.prototype.previousAttributes = function () {
        return _.clone(this._previousAttributes);
    };
    Model.prototype.toJSON = function (options) {
        return _.clone(this.attributes);
    };
    Model.prototype.compare = function (model) {
        return this.cid == model.cid;
    };

    Model.extend = function (protoProps, staticProps) {
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

    return Model;

});