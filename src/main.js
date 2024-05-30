import Vue from 'vue'
import './bootstrap.js'
import PermissionsModal from './PermissionsModal.vue'

import { generateOcsUrl, generateUrl, generateRemoteUrl } from '@nextcloud/router'
import { dirname } from '@nextcloud/paths'
import { showError } from '@nextcloud/dialogs'
import axios from '@nextcloud/axios'
import moment from '@nextcloud/moment'
import * as webdav from 'webdav'
import memoize from 'lodash/fp/memoize.js'
import { getCurrentUser } from '@nextcloud/auth'
import '../css/main.scss'
export const getClient = memoize((service) => {
	// Add this so the server knows it is a request from the browser
	axios.defaults.headers['X-Requested-With'] = 'XMLHttpRequest'

	// force our axios
	const patcher = webdav.getPatcher()
	patcher.patch('request', axios)

	return webdav.createClient(
		generateRemoteUrl(`dav/${service}/${getCurrentUser().uid}`),
	)
})
export function getFileId(filePath) {
	return getClient('files').stat(filePath, {
		details: true,
		data: `<?xml version="1.0"?>
			<d:propfind
			xmlns:d="DAV:"
			xmlns:oc="http://owncloud.org/ns"
			xmlns:nc="http://nextcloud.org/ns">
				<d:prop>
					<oc:fileid />
				</d:prop>
			</d:propfind>`,
	}).then((fileId) => {
		return fileId?.data?.props?.fileid
	}).catch((error) => {
		console.error(error)
	})
}
export function generateAbsoluteUrl(url, params, options) {
	const fullPath = generateUrl(url, params, options)
	return `${window.location.origin}${fullPath}`
}

let permVue
let lastPath = null
let webexApp
if (window.Webex?.Application) {
	webexApp = new window.Webex.Application()
	webexApp.onReady().then(() => {
		console.debug('[picker main] host app is ready', webexApp)
		// log('onReady()', { message: 'host app is ready' })
		/*
		webexApp.listen().then(() => {
			app.on('application:displayContextChanged', (payload) => log('application:displayContextChanged', payload));
			app.on('application:shareStateChanged', (payload) => log('application:shareStateChanged', payload));
			app.on('application:themeChanged', (payload) => log('application:themeChanged', payload));
			app.on('meeting:infoChanged', (payload) => log('meeting:infoChanged', payload));
			app.on('meeting:roleChanged', (payload) => log('meeting:roleChanged', payload));
			app.on('space:infoChanged', (payload) => log('space:infoChanged', payload));
		})
		*/
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
			// const token = response.data?.ocs?.data?.token
			// const fileId = response.data?.ocs?.data?.file_source
			// const urlForWebex = generateUrl('/apps/picker/webex-share/{token}?fileId={fileId}', { token, fileId })
			// webexApp.setShareUrl(urlForWebex, urlForWebex, t('picker', 'Nextcloud picker')).then(() => {
			webexApp.setShareUrl(publicLinkUrl, publicLinkUrl, t('picker', 'Nextcloud picker')).then(() => {
				console.debug('[picker main] setShareUrl.then => change location to', publicLinkUrl)
				window.location = publicLinkUrl
			}).catch((error) => {
				console.error(error)
			})
		} else if (action === 'open') {
			console.debug('[picker main] after edit, there is NO webex app => setShareUrl')
			window.location = publicLinkUrl
		} else if (action === 'copy') {
			console.debug('[picker main] after edit, there is NO webex app => copyShareLink')
			navigator.clipboard.writeText(publicLinkUrl).then(() => {
				console.debug('Link copied to clipboard successfully')
				window.opener.window.pickerWindow.close()
			})
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

function getInternalLink(filePath, action) {
	getFileId(filePath).then(fileId => {
		const internalLinkURL = generateAbsoluteUrl('/f/') + fileId
		console.debug('internalLinkURL is', internalLinkURL)
		if (webexApp) {
			console.debug('[picker main] after getting Internal Link, there is a webex app => setInternalLinkURL')
			// const token = response.data?.ocs?.data?.token
			// const fileId = response.data?.ocs?.data?.file_source
			// const urlForWebex = generateUrl('/apps/picker/webex-share/{token}?fileId={fileId}', { token, fileId })
			// webexApp.setShareUrl(urlForWebex, urlForWebex, t('picker', 'Nextcloud picker')).then(() => {
			webexApp.setInternalLinkURL(internalLinkURL, internalLinkURL, t('picker', 'Nextcloud picker')).then(() => {
				console.debug('[picker main] setinternalLinkURL.then => change location to', internalLinkURL)
				window.location = internalLinkURL
			}).catch((error) => {
				console.error(error)
			})
		} else if (action === 'open') {
			console.debug('[picker main] after getting Internal Link, there is NO webex app => setInternalLinkURL')
			window.location = internalLinkURL
		} else if (action === 'copy') {
			console.debug('[picker main] after getting Internal Link, there is NO webex app => copyInternalLinkURL')
			navigator.clipboard.writeText(internalLinkURL).then(() => {
				console.debug('Internal Link copied to clipboard successfully')
				window.opener.window.pickerWindow.close()
			})
		}
	}).catch(error => {
		console.error(error)
		showError(t('picker', 'Error getting fileId'))
	})
}

function onFileSelected(targetPath) {
	const url = generateUrl('/apps/picker/can-share?path={targetPath}', { targetPath })
	axios.get(url).then((response) => {
		if (response.data.allowed) {
			// createPublicLink(targetPath)
			permVue.setFilePath(targetPath)
			permVue.setOpen(true)
			lastPath = dirname(targetPath)
		} else {
			showError(t('picker', 'You are not allowed to share this file'))
			setTimeout(openFilePicker, 500)
		}
	}).catch((error) => {
		console.error(error)
		showError(t('picker', 'Error while checking if you are allowed to share this file'))
	})
}

function openFilePicker() {
	OC.dialogs.filepicker(
		t('picker', 'Choose a file and start collaborating'),
		(targetPath) => {
			onFileSelected(targetPath)
		},
		false, null, true, undefined, lastPath
	)
}

document.addEventListener('DOMContentLoaded', (event) => {
	const View = Vue.extend(PermissionsModal)
	permVue = new View().$mount('#picker')
	permVue.$on('closed', () => {
		openFilePicker()
	})

	permVue.$on('open', (filePath, permission) => {
		if (permission === 'internal') {
			getInternalLink(filePath, 'open')
		} else {
			createPublicLink(filePath, permission, 'open')
		}
	})

	permVue.$on('copy', (filePath, permission) => {
		if (permission === 'internal') {
			getInternalLink(filePath, 'copy')
		} else {
			createPublicLink(filePath, permission, 'copy')
		}
	})

	openFilePicker()
})
