const path = require('path');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');

module.exports = {
    // Entry point for JavaScript and SCSS/CSS
    entry: './src/js/main.js',

    // Output for bundled files (JS and CSS)
    output: {
        path: path.resolve(__dirname, 'dist'),
        filename: 'bundle.js', // JavaScript bundle
    },

    mode: 'development', // You can change to 'production' for minified assets

    // Module rules for loaders
    module: {
        rules: [
            {
                // JavaScript: Transpile ES6+ using Babel
                test: /\.js$/,
                exclude: /node_modules/,
                use: {
                    loader: 'babel-loader',
                    options: {
                        presets: ['@babel/preset-env'],
                    },
                },
            },
            {
                // SCSS to CSS
                test: /\.scss$/,
                use: [
                    MiniCssExtractPlugin.loader, // Extracts CSS into a separate file
                    'css-loader', // Translates CSS into CommonJS
                    'sass-loader', // Compiles Sass to CSS
                ],
            },
            {
                // Regular CSS handling
                test: /\.css$/,
                use: [
                    MiniCssExtractPlugin.loader, // Extract CSS into a separate file
                    'css-loader', // Translates CSS into CommonJS
                ],
            },
        ],
    },

    // Plugins
    plugins: [
        new MiniCssExtractPlugin({
            filename: 'bundle.css', // Output CSS file
        }),
    ],
};
