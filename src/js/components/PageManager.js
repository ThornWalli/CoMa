coma.define(['underscore','jquery', '../base/Controller', '../base/DomModel', '../base/Cache', '../services/history', '../services/parser'], function(_, $, Controller, DomModel, Cache, history, js) {

    return Controller.extend({

        model: DomModel.extend({
            defaults: function() {
                return {
                    hasCache: true,
                    cache: new Cache(),
                    deep: null,
                    ajax: null,
                    contentContainer: null,
                    historyMethod: 'update'
                }
            }
        }),

        initialize: function() {
            Controller.prototype.initialize.apply(this, arguments);

            $(document).on('click', 'a[data-deep="' + this.model.get('deep') + '"]', function(e) {
                e.preventDefault();
                history[this.model.get('historyMethod')](this.model.get('deep'), $(e.currentTarget).attr('href'));
            }.bind(this));

            history.register(this.model.get('deep'), function(value, model) {
                this.update(model);
            }.bind(this));
        },

        update: function(model) {
            this.container = getContentContainer(this.$el, this.model.get('contentContainer'));
            var value = model.get('value');
            var ajax = this.model.get('ajax');

            if(!!value) {
                if(!!ajax) {
                    changeContent.bind(this)(ajax + value);
                }
                return true;
            }
            return false;
        },

        onAddContent: function() {
            js.parse(this.container);
        },

        onRemoveContent: function(callback) {
            callback();
        }
    });

    function getContentContainer(node, selector) {
        if(!!selector) {
            return $(selector, node);
        }
        return node;
    }

    function changeContent(url) {
        this.onRemoveContent(function() {
            removeContent(this.container);
            var data = {};
            if (this.targetModel) {
                var targetModel = this.targetModel;
                data = targetModel.getModalData();
            }
            this.model.get('cache').getContent(url, addContent.bind(this), !this.model.get('hasCache'), data);
        }.bind(this));
    }

    function addContent(content) {
        if(typeof content == 'string') {
            this.container.html(content);
            this.onAddContent();
            return $('> *', this.container);
        }
        this.container.html(content);
        this.onAddContent();
        return content;
    }

    function removeContent(node) {
        $('> *', node).detach();
    }
});