const webpack = require('webpack');
const path = require('path');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const CopyPlugin = require("copy-webpack-plugin");

const MomentLocalesPlugin = require('moment-locales-webpack-plugin');


module.exports = {
  mode: "production",
  watch: true,
  watchOptions: {
    ignored: /node_modules/,
    poll: 1000, // Check for changes every second
  },
  performance: {
    maxEntrypointSize: 1024000,
    maxAssetSize: 1024000,
  },
  resolve: {
    alias: {
      "@sass": path.resolve(__dirname, "./src/sass/"),
      "@js": path.resolve(__dirname, "./src/js/"),
      "@node_modules": path.resolve(__dirname, "./node_modules/"),
    },
  },

  entry: {
    trivia: "./src/trivia.js",
    site: "./src/app_old.js",
  },
  output: {
    path: path.resolve(__dirname, "../site/"),
    filename: "js/[name].bundle.js",
    chunkFilename: "js/[name].bundle.[id].js",
  },
  externals: {
    jquery: "jquery",
  },

  plugins: [
    new MiniCssExtractPlugin({
      filename: "css/[name].bundle.css",
      chunkFilename: "css/[name].bundle.[id].css",
    }),
    new MomentLocalesPlugin(),
    new MomentLocalesPlugin({
      localesToKeep: ["es-us"],
    }),
  ],

  module: {
    rules: [
      {
        test: /\.s[ac]ss$/i,
        use: [
          // 'style-loader',
          MiniCssExtractPlugin.loader,
          {
            loader: "css-loader",
            options: {
              url: false,
              // sourceMap: true,
            },
          },
          {
            loader: "sass-loader",
            options: {
              // Prefer `dart-sass`
              implementation: require("sass"),
              sourceMap: true,
              // sourceMapContents: false,
              sassOptions: {
                outputStyle: "compressed",
              },
            },
          },
        ],
      },
    ],
  },
};
