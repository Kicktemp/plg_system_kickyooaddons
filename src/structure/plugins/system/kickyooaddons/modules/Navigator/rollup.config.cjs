/* eslint-env node */
module.exports = {

  input: 'assets/src/customizer/index.js',

  output: {
    file: 'assets/customizer.min.js',
    format: 'iife',
    globals: {
      vue: 'Vue',
      uikit: 'UIkit',
      'uikit-util': 'UIkit.util'
    }
  },

  external: ['vue', 'uikit', 'uikit-util']

};
