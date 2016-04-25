function CoMa_Controller(options) {
    this.initialize(options);

};
CoMa_Controller.prototype.events = function () {
    return {};
};

CoMa_Controller.prototype.initialize = function (options) {

    this.el = options.el;
    this.$el = jQuery(this.el);

    setupEvents(this);

    this.$ = function (selector) {
        return this.$el.find(selector);
    };


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
CoMa_Controller.extend = function (protoProps, staticProps) {
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
