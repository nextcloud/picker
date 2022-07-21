import { generateUrl } from '@nextcloud/router'
import { translate as t } from '@nextcloud/l10n'

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
					if (e === false) {
						OC.dialogs.message(
							t('picker', 'Do you want to choose another file?'),
							t('picker', 'Choose another file?'),
							'none',
							{
								type: OC.dialogs.YES_NO_BUTTONS,
								confirm: t('picker', 'Yes'),
								confirmClasses: 'success',
								cancel: t('picker', 'No'),
							},
							(result) => {
								if (result) {
									window.location.replace(generateUrl('/apps/picker/webex-single-link'))
								}
							},
							true,
							true,
						)
					}
				})
			})
		})
		console.debug('Yes we are in a webex app!!!!!!', webexApp)
	} else {
		console.debug('No webex app!!!!!!')
	}
}
document.head.append(webexScript)
