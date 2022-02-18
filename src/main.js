import { createApp } from 'vue'
import App from './App.vue'
import router from './router'
import store from './store'

// import { ElButton } from 'element-plus'
// import 'element-plus/packages/theme-chalk/src/base'
import 'element-plus/es/components/message/style/css'

import VueUeditoeWrap from 'vue-ueditor-wrap'

createApp(App).use(VueUeditoeWrap).use(store).use(router).mount('#app')
