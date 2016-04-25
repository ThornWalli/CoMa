coma.define(['underscore', 'jquery', './DomModel'], function (_, $, DomModel) {

    function Controller(options) {
        this.initialize(options);
    };
    Controller.prototype.events = function () {
        return {};
    };
    Controller.prototype.model = DomModel;

    Controller.prototype.initialize = function (options) {

        this.el = options.el;
        this.$el = $(this.el);

        this.$el.data('interface', this);

        if (options.model && typeof options.model != 'function') {
            this.model = options.model;
        } else if (this.model && typeof this.model == 'function') {
            this.model = new this.model(options.data || {}, this.$el.data());
        }

        this.model.on('remove', function () {
            this.$el.remove();
        }.bind(this));

        this.delegateEvents();

        this.$ = function (selector) {
            return this.$el.find(selector);
        };

        this.$target = options.$target;

        if (this.$target) {
            if (!this.$target.data('interface')) {
                console.error('target has no controller', this.$el, $target);
                this.ready();
            } else {
                this.ready(this.$target.data('interface').model);
            }
        } else {
            this.ready();
        }


        function setupEvents(scope) {

            var events = scope.events;
            if (typeof events == 'function') {
                events = events();
            }

            var $el = scope.$el;
            _.each(events, function (callback, event) {

                var eventSplit = event.split(' ');
                var eventName = eventSplit[0];
                var selector = eventSplit.splice(1, eventSplit.length).join(' ');

                console.log(eventName, selector)

                $el.find(selector).on(eventName, callback.bind(scope))

            });

        }

    };
    Controller.prototype.__ = function (name, defaultValue) {
        return this.$el.attr('lang-' + name) || defaultValue || '';
    };
    Controller.prototype.render = function (targetModel) {
    };
    Controller.prototype.ready = function (targetModel) {
        if (targetModel) {
            this.targetModel = targetModel;
        }
        this.render(targetModel || this.model);
    };
    Controller.prototype.delegateEvents = function (events) {
        if (!(events || (events = _.result(this, 'events')))) return this;
        this.undelegateEvents();
        for (var key in events) {
            var method = events[key];
            if (!_.isFunction(method)) method = this[events[key]];
            if (!method) continue;

            var match = key.match(/^(\S+)\s*(.*)$/);
            var eventName = match[1], selector = match[2];
            method = method.bind(this);
            eventName += '.delegateEvents' + this.cid;
            if (selector === '') {
                this.$el.on(eventName, method);
            } else {
                this.$el.on(eventName, selector, method);
            }
        }
        return this;
    },
        Controller.prototype.undelegateEvents = function () {
            this.$el.off('.delegateEvents' + this.cid);
            return this;
        }

    Controller.extend = function (protoProps, staticProps) {
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

    return Controller;

});