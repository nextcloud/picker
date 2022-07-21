import { generateUrl } from '@nextcloud/router'

// get stop event: load single-link again
const webexScript = document.createElement('script')
webexScript.src = 'https://binaries.webex.com/static-content-pipeline/webex-embedded-app/v1/webex-embedded-app-sdk.js'
webexScript.onload = () => {
	console.debug('loaded the webex script!')
	if (window.Webex?.Application) {
		const webexApp = new window.Webex.Application()
		webexApp.onReady().then(() => {
			console.debug('host app is ready!!!', webexApp)
			webexApp.listen().then(() => {
				webexApp.on('application:shareStateChanged', (e) => {
					console.debug('shareStateChanged', e)
					console.debug('we could go back to ', generateUrl('/apps/picker/webex-single-link'))
				})
			})
		})
		console.debug('Yes we are in a webex app!!!!!!', webexApp)
	} else {
		console.debug('No webex app!!!!!!')
	}
}
document.head.append(webexScript)
