window.Vue = require('vue');
window._ = require('lodash');
window.axios = require('axios');

import ElementUI from 'element-ui';
import 'element-ui/lib/theme-chalk/index.css';

import App from './components/App';

Vue.use(ElementUI);

const app = new Vue({
    el: '#app',
    render: r => r(App)
});
