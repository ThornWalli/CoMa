coma.define(['underscore', 'jquery', '../../base/Controller', '../../base/DomModel', '../../services/history'], function (_, $, Controller, DomModel, history) {

    return Controller.extend({

            model: DomModel.extend({

                defaults: function () {
                    return {
                        type: 'internal',
                        pageType: 'page'
                    };
                }

            }),

            events: function () {
                return {
                    'change .page-value': onChangeValue,
                    'change .post-value': onChangeValue,
                    'change .link-type': onChangeLinkType,
                    'change .link-page-type': onChangeLinkPageType
                }
            },

            initialize: function () {
                Controller.prototype.initialize.apply(this, arguments);
    setup(this);
            }

        }

    );

    function setup(scope){
        scope.$pageTypeValue = scope.$('.page-type-value');
        scope.$linkTypeValue = scope.$('.link-page-type');
        scope.$pageValue = scope.$('.page-value');
        scope.$postValue = scope.$('.post-value');
        scope.model.on('change:type', onChangeType.bind(scope));
        scope.model.on('change:pageType', onChangePageType.bind(scope));
        onChangeType.bind(scope)(scope.model, scope.model.get('type'));
        if (scope.$linkTypeValue.val()) {
            scope.model.set('pageType',scope.$linkTypeValue.val());
        }
        onChangePageType.bind(scope)(scope.model, scope.model.get('pageType'));

    }

    function onChangeValue(e) {
        if (this.model.get('type') == 'page') {
            // page
            this.$pageTypeValue.val(this.$pageValue.val());
        } else {
            // post
            this.$pageTypeValue.val(this.$postValue.val());
        }
    }

    function onChangeLinkType(e) {
        this.model.set('type', $(e.currentTarget).val());
    }

    function onChangeLinkPageType(e) {
        this.model.set('pageType', $(e.currentTarget).val());
    }

    function onChangePageType(model, pageType) {
        this.$pageTypeValue.val(pageType == 'post' ? this.$postValue.val() : this.$pageValue.val());
        if (pageType == 'post') {
            this.$('.page').hide();
            this.$('.post').show();
        } else {
            this.$('.page').show();
            this.$('.post').hide();
        }
    }

    function onChangeType(model, type) {
        if (type == 'external') {
            this.$('.link-page-type, .input.internal').hide();
            this.$('.input.external').show();
        } else {
            this.$('.link-page-type, .input.internal').show();
            this.$('.input.external').hide();
        }
    }


});