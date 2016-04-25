coma.define(['underscore', 'jquery', '../../base/Controller', '../../base/DomModel', '../../services/history',
    'cm/lib/codemirror',
    'cm/mode/htmlmixed/htmlmixed',
    'cm/mode/css/css',
    'cm/mode/javascript/javascript',
    'cm/mode/php/php',
    'cm/mode/sql/sql',
    'cm/mode/xml/xml'], function (_, $, Controller, DomModel, history, CodeMirror) {

    return Controller.extend({

            model: DomModel.extend({

                defaults: function () {
                    return {
                        lineNumbers: true,
                        editor: null,
                        mode: 'php',
                        readOnly: false,
                        viewportMargin: 'Infinity'
                    };
                }

            }),

            events: function () {
                return {
                    'change .mode': onChangeMode
                }
            },

            initialize: function () {
                Controller.prototype.initialize.apply(this, arguments);
                this.$mode = this.$('.mode');
                this.$code = this.$('.code');
                setupEditor(this);
            }
        }
    );

    function setupEditor(scope) {

        scope.model.set('mode', scope.$mode.val());

        window.animationFrame.add(function () {
            var editor = CodeMirror.fromTextArea(this.$('textarea').get(0), {
                lineNumbers: this.model.get('lineNumbers'),
                mode: this.model.get('mode') == 'html' ? 'htmlmixed' : this.model.get('mode'),
                readOnly: this.model.get('readOnly')
            });
            editor.on("change", function (editor) {
                this.$code.val(editor.getDoc().getValue());
            }.bind(this));
            this.model.set('editor', editor);
        }.bind(scope));
    }

    function onChangeMode(e) {
        e.preventDefault();
        this.model.set('mode', $(e.currentTarget).val());
        $(this.model.get('editor').getWrapperElement()).remove();
        setupEditor(this);
    }

});