import Vue from 'vue'
import './bootstrap.js'
import Limit from './Limit.vue'

document.addEventListener('DOMContentLoaded', (event) => {
	const View = Vue.extend(Limit)
	new View().$mount('#limit')
})
