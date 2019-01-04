//require('./single-recipe.js');

import Vue from 'vue'
import VueRouter from 'vue-router';
import VModal from 'vue-js-modal';
import VueCookie from 'vue-cookie';


Vue.use(VueCookie);
Vue.use(VueRouter);
Vue.use(VModal);

window.Event = new Vue();

Vue.component('ingredients', require('./ingredients.vue'));
Vue.component('whatsapp', require('./Whatapp.vue'));
Vue.component('toastlog', require('./ToastLog.vue'));
Vue.component('cookingstep', require('./CookingStep.vue'));
Vue.component('shoppinglistwidget', require('./ShoppingListWidget.vue'));



var toast = new Vue({
    el: '#toast'

});

var app = new Vue({
    el: '#app',
});
