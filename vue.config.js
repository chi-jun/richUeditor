const Components = require('unplugin-vue-components/webpack')
const { ElementPlusResolver } = require('unplugin-vue-components/resolvers')


module.exports = {
  publicPath: process.env.NODE_ENV === 'production' ? '/richtexteditor/' : './',
  configureWebpack: {
    plugins: [
      require('unplugin-vue-components/webpack')({ /* options */
      }),
      Components({
          resolvers:[ElementPlusResolver()],
      }),
      // new CopyWebpackPlugin({
      //   patterns: [
      //     {
      //       from: './src/assets',
      //       to: './assets/'
      //     }
      //   ]
      // })
    ],

  },
  devServer: {
    proxy: {
      '/api': {
        target: 'https://saastest.qzriji.com',
        ws: true,
        changeOrigin: true,
        secure: false,
        pathRewrite: {
          '/api': ''
        }
      }
    }
  }
}
