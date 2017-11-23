/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */
import Vue from 'vue'
import App from './App.vue'
import AppUser from './AppUser.vue'

if ($('#app').length) {
  new Vue({
    el: '#app',
    render: h => h(App)
  })
}
if ($('#app-user').length) {
  new Vue({
    el: '#app-user',
    render: h => h(AppUser)
  })
}
