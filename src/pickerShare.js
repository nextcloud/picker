import { generateUrl } from '@nextcloud/router'
import { translate as t } from '@nextcloud/l10n'

// react on "stop for all" click, ask if user wants to select another file
// which means going back to the webex-single-link page
const webexScript = document.createElement('script')
webexScript.src = 'https://binaries.webex.com/static-content-pipeline/webex-embedded-app/v1/webex-embedded-app-sdk.js'
webexScript.onload = () => {
	console.debug('[picker pickerShare] loaded the webex script')
	if (window.Webex?.Application) {
		const webexApp = new window.Webex.Application()
		webexApp.onReady().then(() => {
			console.debug('[picker pickerShare] host app is ready', webexApp)
			webexApp.listen().then(() => {
				webexApp.on('application:shareStateChanged', (e) => {
					console.debug('[picker pickerShare] application:shareStateChanged', e)
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
	} else {
		console.debug('[picker pickerShare] No webex app')
	}
}
document.head.append(webexScript)
