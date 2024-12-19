/**
 * SPDX-FileCopyrightText: 2022 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

import './bootstrap.js'
import { loadState } from '@nextcloud/initial-state'

document.addEventListener('DOMContentLoaded', (event) => {
	const state = loadState('picker', 'webex-share')

	if (window.Webex?.Application) {
		const webexApp = new window.Webex.Application()
		webexApp.onReady().then(() => {
			console.debug('host app is ready!!!')
			webexApp.context.getUser().then((user) => {
				console.debug('getUser() promise resolved. User', user)
			}).catch((error) => {
				console.debug('getUser() promise failed ' + error.message)
			})
		})
	} else {
		window.location = state.publicLink
	}
})
