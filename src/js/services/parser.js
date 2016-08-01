coma.define(['module', 'underscore', 'jquery'], function (module, _, $) {

    var controllerClasses = {};

    var parser = {
        parse: function (node) {

            node = node || $('html');

            if (node['length'] && node['length'] > 1) {
                $(node).each(function (i, node) {

                    this.parse(node);

                }.bind(this));
                return;
            }

            var nodes = $(node).find('.coma-controller[data-coma-controller][init!="true"]');
            if ($(node).is('.coma-controller[data-coma-controller][init!="true"]')) {
                nodes.push($(node));
            }

            var classes = getControllerClasses(nodes);

            Array.prototype.reverse.call(nodes);
            coma.require(classes, function () {

                for (var i = 0; i < classes.length; i++) {
                    controllerClasses[classes[i]] = arguments[i];
                }

                _.each(nodes, function (node) {

                    initController($(node));

                });

            });

        }
    };

    function initController($node) {

        if (!!$node.attr('init')) {
            return;
        }

        $node.attr('init', true);

        var $target, $items;
        var target = $node.data('target');
        var items = $node.data('items');

        if (target) {
            $target = $(target);
            if ($target.is('.coma-controller[data-coma-controller]')) {
                initController($target);
            }
        }

        if (items) {
            $items = $(items, $node);
            $items.each(function (index, item) {
                item = $(item);
                if (item.is('.coma-controller[data-coma-controller]')) {
                    initController(item);
                }
            });
        }
console.log('$target',$target);
        var controllerClass = controllerClasses[$node.data('comaController')];
        if (!!controllerClass) {
            var controller = new controllerClass({el: $node.get(0), $target: $target, $items: $items});
        } else {
            console.error('can\'t find controller "' + $node.data('comaController') + '"')
        }

    }


    function getControllerClasses(nodes) {

        var classes = _.map(nodes, function (node) {

            var controller = $(node).attr('data-coma-controller');

            if (controller && !(controller in controllerClasses)) {
                return controller;
            } else {

                initController($(node));
            }

        });

        return _.uniq(classes, false);

    }

    return parser;

});
