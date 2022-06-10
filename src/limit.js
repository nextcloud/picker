import Vue from 'vue'
import './bootstrap'
import Limit from './Limit'

document.addEventListener('DOMContentLoaded', (event) => {
	const View = Vue.extend(Limit)
	new View().$mount('#limit')
})
