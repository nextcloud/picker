// import Vue from 'vue'
import './bootstrap'
import { loadState } from '@nextcloud/initial-state'
// import WebexShare from './WebexShare'

document.addEventListener('DOMContentLoaded', (event) => {
	const state = loadState('picker', 'webex-share')

	if (window.Webex?.Application) {
		const webexApp = new window.Webex.Application()
		webexApp.onReady().then(() => {
			console.debug('host app is ready!!!')
			webexApp.context.getUser().then((user) => {
				console.debug('getUser() promise resolved. User', user)
				/*
				const View = Vue.extend(WebexShare)
				new View({
					propsData: {
						userId: 'TODO GET it from sdk',
					},
				}).$mount('#webex_share')
				*/
			}).catch((error) => {
				console.debug('getUser() promise failed ' + error.message)
			})
		})
	} else {
		window.location = state.publicLink
		/*
		// TODO DELETE
		const View = Vue.extend(WebexShare)
		new View({
			propsData: {
				userId: 'robert',
			},
		}).$mount('#webex_share')
		*/
	}
})
