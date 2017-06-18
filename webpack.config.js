const webpack = require('webpack');
var CommonsChunkPlugin = require("webpack/lib/optimize/CommonsChunkPlugin");

var path = require( 'path' );
var now  = +new Date;
var root = path.join( __dirname, 'tpl_src' );
var dist = path.join( __dirname, 'tpl' );

//var src = './tpl_src/Public/js',
//	dist = './tpl/Public/js';

module.exports = {
	entry  : [path.join( root, 'index.js' )],
    output : {
        path              : dist,
        publicPath        : './',
        filename          : `[name].js`
    },
//  entry: {
//    index: src+'/index.js',
//    main: src+'/main.js',
//  },
//  output: {
//    filename: 'Public/js/[name].js'
//  },
  module: {
    loaders: [{
      test: /\.js$/,
      exclude: /node_modules/,
      loader: 'babel-loader',
    }]
  },

  plugins: [
    /*
    new webpack.optimize.UglifyJsPlugin({
      compress: {
        warnings: false,
      },
      output: {
        comments: false,
      },
    }),//压缩和丑化，开发阶段关闭
    */

    new webpack.ProvidePlugin({
      $: 'jquery'
    }),//直接定义第三方库

//    new CommonsChunkPlugin({
//      name: "commons",
//      // (the commons chunk name)
//
//      filename: "Public/js/commons.js",
//      // (the filename of the commons chunk)
//
//       minChunks: 2,
//      // (Modules must be shared between 3 entries)
//
//      chunks: ["index", "main"]
//      // (Only use these entries)
//    })//定义公共chunk

  ]
};