import { createApp, h } from 'vue'
import { createInertiaApp } from '@inertiajs/inertia-vue3'
import { Quasar, Notify, Dialog } from 'quasar'
import store from './store'
import 'quasar/dist/quasar.css'
import "@fontsource/roboto"
import 'material-icons/iconfont/material-icons.css'
import "../css/app.css"

import mitt from 'mitt';                  
const emitter = mitt();

createInertiaApp({
  resolve: name => require(`./pages/${name}`),
  setup({ el, App, props, plugin }) {
    let app = createApp({ render: () => h(App, props) })
    app
    .provide('emitter', emitter)
    .use(Quasar, {
      plugins: [
        Notify,
        Dialog
      ],
      config: {
        notify: { /* look at QuasarConfOptions from the API card */ }
      }
    })
    .use(store)
    .use(plugin)
    .mount(el)
  },
})