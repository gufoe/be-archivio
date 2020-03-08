import VueI18n from 'vue-i18n'
import messages from './messages'
import { BootstrapVue, IconsPlugin } from 'bootstrap-vue'
import vSelect from 'vue-select'
import 'vue-select/dist/vue-select.css';

require('./bootstrap');
window.Vue = require('vue');

Vue.component('v-select', vSelect)
Vue.use(BootstrapVue)
Vue.use(IconsPlugin)

window._ = Vue.prototype._ = require('lodash');

// Vue.component('example-component', require('./components/ExampleComponent.vue').default);
// Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
const files = require.context('./', true, /\.vue$/i)
files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))


// Create VueI18n instance with options
const i18n = new VueI18n({
  locale: 'it', // set locale
  messages, // set locale messages
  dateTimeFormats: {
    it: {
      dm: {
        day: 'numeric',
        month: 'long',
      }
    }
  }
})


window.to_form_data = (data, name, fd) => {
  fd = fd || new FormData()
  name = name || ''
  if (data && typeof data === 'object' && data.constructor.name != 'File') {
    if (Object.values(data).length == 0) {
      to_form_data('', name, fd)
    } else {
      for (var index in data) {
        var value = data[index]
        if (name == '') {
          to_form_data(value, index, fd)
        } else {
          to_form_data(value, name + '[' + index + ']', fd)
        }
      }
    }
  } else if (data !== undefined && typeof data != 'function') {
    fd.append(name, data)
  }
  return fd
},


Vue.prototype.$utc = str => {
  if (!str || typeof str != 'string') return new Date()
  let pieces = str.match(/\d+/g)
  // console.log(pieces)
  return new Date(Date.UTC(Number(pieces[0]), Number(pieces[1])-1, Number(pieces[2]),
      Number(pieces[3]) || 0, Number(pieces[4]) || 0, Number(pieces[5]) || 0))
}
Date.prototype.toSql = function() {
  return this.toISOString().slice(0, 19).replace('T', ' ').slice(0, 10);
}



axios.interceptors.response.use(function (res) {
  return res
}, function (err, data) {
  console.log(err)
  if (err.response) {
    let res = err.response
    if (res.status == 400) {
      alert(res.data.message)
    } else if (res.status == 500) {
      alert('Errore interno')
    } else {
      alert('Errore '+res.status)
    }
    console.log(err.response)
  } else {
    alert('Errore di connessione')
  }
  return Promise.reject(err)
});


const app = new Vue({
    i18n,
    el: '#app',
})
