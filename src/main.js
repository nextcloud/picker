/**
 * SPDX-FileCopyrightText: 2022 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

import './bootstrap.js'
import { generateOcsUrl, generateUrl } from '@nextcloud/router'
import { showError, getFilePickerBuilder } from '@nextcloud/dialogs'
import '@nextcloud/dialogs/style.css'
import axios from '@nextcloud/axios'
import moment from '@nextcloud/moment'
import EyeIcon from '@mdi/svg/svg/eye.svg?raw'
import PencilIcon from '@mdi/svg/svg/pencil.svg?raw'
import InternalIcon from '@mdi/svg/svg/open-in-new.svg?raw'
import '../css/main.scss'
export function generateAbsoluteUrl(url, params, options) {
	const fullPath = generateUrl(url, params, options)
	return `${window.location.origin}${fullPath}`
}

let webexApp
if (window.Webex?.Application) {
	webexApp = new window.Webex.Application()
	webexApp.onReady().then(() => {
		console.debug('[picker main] host app is ready', webexApp)
	})
	console.debug('[picker main] Yes we are in a webex app', webexApp)
} else {
	console.debug('[picker main] No webex app')
}

function editShare(shareId, permission, action) {
	const url = generateOcsUrl('/apps/files_sharing/api/v1/shares/{shareId}', { shareId })
	const req = {
		permissions: permission === 'write' ? 3 : undefined,
		expireDate: '',
	}
	axios.put(url, req).then((response) => {
		console.debug('[picker main] edit share link success', response.data?.ocs?.data)
		const publicLinkUrl = response.data?.ocs?.data?.url
		if (webexApp) {
			console.debug('[picker main] after edit, there is a webex app => setShareUrl')
			webexApp.setShareUrl(publicLinkUrl, publicLinkUrl, t('picker', 'Nextcloud picker')).then(() => {
				console.debug('[picker main] setShareUrl.then => change location to', publicLinkUrl)
				window.location = publicLinkUrl
			}).catch((error) => {
				console.error(error)
			})
		} else if (action === 'copy') {
			console.debug('[picker main] after edit, there is NO webex app => copyShareLink')
			navigator.clipboard.writeText(publicLinkUrl).then(() => {
				console.debug('Link copied to clipboard successfully')
				if (window.opener) {
					window.opener.window.pickerWindow.close()
				} else {
					openFilePickerClipboard()
					window.parent.closePickerIframe()
				}
			})
		} else {
			console.debug('[picker main] after edit, there is NO webex app => setShareUrl')
			window.location = publicLinkUrl
		}
	}).catch((error) => {
		console.debug(error)
		showError(t('picker', 'Error while editing the shared access'))
	})
}

function createPublicLink(path, permission, action) {
	const url = generateOcsUrl('/apps/files_sharing/api/v1/shares')
	const req = {
		path,
		shareType: 3,
		label: '[P] ' + t('picker', 'Picker link') + ' ' + moment().format('YYYY-MM-DD HH:mm:ss'),
	}
	axios.post(url, req).then((response) => {
		console.debug('ADD SUCCESS', response.data?.ocs?.data)
		const shareId = response.data?.ocs?.data?.id
		editShare(shareId, permission, action)
	}).catch((error) => {
		console.error(error)
		showError(t('picker', 'Error while creating the shared access'))
	})
}

function copyInternalLink(targetId) {
	const internalLinkURL = generateAbsoluteUrl('/f/') + targetId
	console.debug('internalLinkURL is', internalLinkURL)
	if (webexApp) {
		console.debug('[picker main] after getting Internal Link, there is a webex app => setInternalLinkURL')
		webexApp.setInternalLinkURL(internalLinkURL, internalLinkURL, t('picker', 'Nextcloud picker')).then(() => {
			console.debug('[picker main] setinternalLinkURL.then => change location to', internalLinkURL)
			window.location = internalLinkURL
		}).catch((error) => {
			console.error(error)
		})
	} else {
		console.debug('[picker main] after getting Internal Link, there is NO webex app => copyInternalLinkURL')
		navigator.clipboard.writeText(internalLinkURL).then(() => {
			console.debug('Internal Link copied to clipboard successfully')
			if (window.opener) {
				window.opener.window.pickerWindow.close()
			} else {
				openFilePickerClipboard()
				window.parent.closePickerIframe()
			}
		})
	}
}

function openFilePicker() {
	const filePicker = getFilePickerBuilder(t('picker', 'Choose a file and start collaborating'))
		.setMultiSelect(false)
		.allowDirectories(true)
		.addButton({
			label: t('picker', 'View only'),
			callback: (nodes) => {
				createPublicLink(nodes[0].path, 'read')
			},
			type: 'primary',
			icon: PencilIcon,
		})
		.addButton({
			label: t('picker', 'Edit'),
			callback: (nodes) => {
				createPublicLink(nodes[0].path, 'write')
			},
			type: 'primary',
			icon: EyeIcon,
		})
		.build()
	filePicker.pick()
}

function openFilePickerClipboard() {
	const filePicker = getFilePickerBuilder(t('picker', 'Choose a file you want to copy a link from'))
		.setMultiSelect(false)
		.allowDirectories(true)
		.addButton({
			label: t('picker', 'Copy Read Only public link'),
			callback: (nodes) => {
				createPublicLink(nodes[0].path, 'read', 'copy')
			},
			type: 'primary',
			icon: EyeIcon,
		})
		.addButton({
			label: t('picker', 'Copy Editable public link'),
			callback: (nodes) => {
				createPublicLink(nodes[0].path, 'write', 'copy')
			},
			type: 'primary',
			icon: PencilIcon,
		})
		.addButton({
			label: t('picker', 'Copy Internal link'),
			callback: (nodes) => {
				copyInternalLink(nodes[0].fileid)
			},
			type: 'primary',
			icon: InternalIcon,
		})
		.build()
	filePicker.pick()
}

document.addEventListener('DOMContentLoaded', (event) => {
	const queryString = window.location.search
	const urlParams = new URLSearchParams(queryString)
	const option = urlParams.get('option')
	if (option === 'Clipboard') {
		console.debug('Display openFilePickerClipboard')
		openFilePickerClipboard()
	} else {
		console.debug('Display openFilePicker')
		openFilePicker()
	}
})
