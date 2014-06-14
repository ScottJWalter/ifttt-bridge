module.exports = function(grunt) {

  grunt.initConfig({
    pkg: grunt.file.readJSON('package.json'),
    pot: {
      options: {
        text_domain: 'ifttt-wordpress-bridge',
        dest: 'build/ifttt-wordpress-bridge.pot',
        keywords: ['__', '_e', '_x:1,2c', '_ex:1,2c'],
        encoding: 'UTF-8'
      },
      files: {
        src:  [ 'src/**/*.php' ],
        expand: true
      }
    },
    replace: {
      plugin_version_const: {
        src: ['src/public/class-ifttt-wordpress-bridge.php'],
        overwrite: true,
        replacements: [{
          from: /(const VERSION = ')([^']*)(';)/,
          to: '$1<%= grunt.config.get("pkg").version %>$3'
        }]
      },
      plugin_version_comment: {
        src: ['src/ifttt-wordpress-bridge.php'],
        overwrite: true,
        replacements: [{
          from: /(\* Version:[ ]*)([^ \n]*)/,
          to: '$1<%= grunt.config.get("pkg").version %>'
        }]
      },
      project_description_pot: {
        src: ['src/ifttt-wordpress-bridge.php'],
        dest: 'build/project_description_pot.txt',             // destination directory or file
        replacements: [{
          from: /[\s\S]*\* Description:       (.*)[\s\S]*/g,
          to: '\n#: Project description\nmsgid "$1"\nmsgstr ""\n'
        }]
      }
    },
    concat: {
      dist: {
        src: ['build/ifttt-wordpress-bridge.pot', 'build/project_description_pot.txt'],
        dest: 'src/languages/ifttt-wordpress-bridge.pot'
      }
    },
  });
  grunt.loadNpmTasks('grunt-pot');
  grunt.loadNpmTasks('grunt-text-replace');
  grunt.loadNpmTasks('grunt-contrib-concat');
  grunt.registerTask('default', ['pot', 'replace', 'concat']);
};