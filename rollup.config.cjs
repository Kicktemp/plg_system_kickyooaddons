/* eslint-env node */

process.env.NODE_ENV = 'production';

const vue = require('rollup-plugin-vue');
const {babel} = require('@rollup/plugin-babel');
const json = require('@rollup/plugin-json');
const replace = require('@rollup/plugin-replace');
const commonjs = require('@rollup/plugin-commonjs');
const nodeResolve = require('@rollup/plugin-node-resolve').default;
const {merge, isObject, castArray} = require('lodash');
const {resolve, relative, dirname} = require('path');

module.exports = [
  './src/structure/plugins/system/kickyooaddons/modules/Navigator/rollup.config.cjs',
  './src/structure/plugins/system/kickyooaddons/modules/Brevo/rollup.config.cjs',
  './src/structure/plugins/system/kickyooaddons/modules/HubSpot/rollup.config.cjs',
  './src/structure/plugins/system/kickyooaddons/modules/RapidMail/rollup.config.cjs',
].reduce((carry, config) => {

  if (isObject(config)) {
    return [...carry, config];
  }

  for (const $config of castArray(require(config))) {

    const path = dirname(config);
    const {input, output} = $config;

    merge($config, {

      input: resolvePath(path, input),

      output: {
        ...output, file: resolvePath(path, output.file)
      }

    });

    carry.push($config);

  }

  return carry;

}, []).map(config => merge({

  plugins: [

    nodeResolve({
      browser: true
    }),

    vue(),

    json(),

    babel({
      plugins: ['lodash'],
      extensions: ['.js', '.vue'],
      babelHelpers: 'bundled'
    }),

    commonjs(),

    replace({
      preventAssignment: true,
      'process.env.NODE_ENV': JSON.stringify(process.env.NODE_ENV)
    }),
  ]

}, config));

function resolvePath(...paths) {
  return relative(__dirname, resolve(__dirname, ...paths));
}
