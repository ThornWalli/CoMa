module.exports = function (grunt) {

    grunt.initConfig({

        pkg: grunt.file.readJSON('package.json'),

        watch: {
            options: {
                forever: true,
                livereload: {
                    port: 35730
                },
                atBegin: true
            },
            sass: {
                options: {
                    livereload: false
                },
                files: [
                    '../src/scss/**/*.scss'
                ],
                tasks: ['sass', 'postcss:dist']
            },

            css: {
                files: ['../css/style.css', '../css/debug.css', '../css/wp.css'],
                tasks: []
            }

        },

        requirejs: {
            require: {
                options: require('./build/require.build.json')
            },
            codemirror: {
                options: require('./build/codemirror.build.json')
            },
            libs: {
                options: require('./build/libs.build.json')
            },
            main: {
                options: require('./build/main.build.json')
            }
        },

        sass: {
            dist: {
                options: {
                    spawn: false,
                    style: 'compressed',
                    sourcemap: 'none'
                },
                files: {
                    '../css/style.css': '../src/scss/style.scss',
                    '../css/debug.css': '../src/scss/debug.scss',
                    '../css/wp.css': '../src/scss/wp.scss'
                }
            }
        },

        postcss: {
            options: {
                map: false,
                processors: [
                    require('autoprefixer')({
                        browsers: ['> 5%', 'last 2 versions']
                    })
                ]
            },
            dist: {
                src: '../css/*.css'
            }
        },

        po2mo: {
            files: {
                src: '../src/languages/coma-de_DE.po',
                dest: '../languages/coma-de_DE.mo'
            }
        }

    });

    // npm tasks

    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-contrib-sass');
    grunt.loadNpmTasks('grunt-postcss');
    grunt.loadNpmTasks('grunt-contrib-requirejs');

    // grunt tasks

    grunt.registerMultiTask('po2mo', require('./tasks/po2mo.js')(grunt));
    grunt.registerTask('default', ['watch']);
    grunt.registerTask('build', ['build-js', 'build-sass', 'build-language']);
    grunt.registerTask('build-sass', ['sass', 'postcss:dist']);
    grunt.registerTask('build-language', ['po2mo']);


    grunt.registerTask('build-js', ['requirejs:main', 'remove-coma-var-defined']);
    grunt.registerTask('build-js-libs', ["build-require", "requirejs:codemirror", "requirejs:libs"]);
    grunt.registerTask('build-js-complete', ['build-js-libs', 'build-js']);


    grunt.registerTask('build-require', ['requirejs:require','remove-coma-var-defined']);
    grunt.registerTask('remove-coma-var-defined', function () {
        // remove var coma;
        var replace = require("replace");
        replace({
            regex: /var coma;/g,
            replacement: '',
            paths: ['../js/require.js','../js/main.js'],
            recursive: true,
            silent: true
        });
    });

};
