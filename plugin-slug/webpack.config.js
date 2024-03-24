const webpack                   = require( 'webpack' );

const path                     = require( 'path' );
const MiniCssExtractPlugin     = require( 'mini-css-extract-plugin' );
const RemoveEmptyScriptsPlugin = require( 'webpack-remove-empty-scripts' );

const NODE_ENV = process.env.NODE_ENV || 'development';

// Main configuration.
const config = {
	mode: NODE_ENV,
	entry: {
		'admin-page': [
			'./admin/assets/src/js/admin-page.js',
			'./admin/assets/src/scss/admin-page.scss'
		],
	},
	output: {
		filename: 'js/[name].min.js',
		path: path.resolve( __dirname, 'admin/assets/dist/' ),
	},
    devtool: 'development' === NODE_ENV ? 'eval-source-map' : false,
	module: {
		rules: [
			{
				test: /\.js$/,
				exclude: /node_modules/,
				use: {
					loader: "babel-loader",
					options: {
						presets: [ '@babel/preset-env' ],
						plugins: [
							'@babel/plugin-transform-async-to-generator',
							'@babel/plugin-transform-object-rest-spread',
							[
								'@babel/plugin-transform-react-jsx', {
									'pragma': 'wp.element.createElement'
								}
							]
						]
					}
				}
			},
			{
				test: /\.(css|scss)$/,
				use: [ {
					loader: MiniCssExtractPlugin.loader
				},
				'css-loader',
				{
					loader: 'postcss-loader',
					options: {
						postcssOptions: {
							plugins: [
								[ "postcss-preset-env" ],
								[ "autoprefixer" ],
							],
						},
					}
				},
				{
					loader: 'sass-loader',
					options: {
					  	implementation: require( 'sass' ),
					},
				}
				]
			}
		]
	},
	plugins: [
		new webpack.DefinePlugin( {
			'process.env.NODE_ENV': JSON.stringify( NODE_ENV )
		} ),
        new RemoveEmptyScriptsPlugin(),
		new MiniCssExtractPlugin( {
			filename: 'css/[name].min.css',
		} )
	]
};

module.exports = config;