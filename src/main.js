import Vue from 'vue'
import './bootstrap'
import PermissionsModal from './PermissionsModal'

import { generateOcsUrl, generateUrl } from '@nextcloud/router'
import { dirname } from '@nextcloud/paths'
import { showError } from '@nextcloud/dialogs'
import axios from '@nextcloud/axios'
import moment from '@nextcloud/moment'
import '../css/main.scss'

let permVue
let lastPath = null
let webexApp
if (window.Webex?.Application) {
	webexApp = new window.Webex.Application()
	webexApp.onReady().then(() => {
		console.debug('host app is ready!!!')
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
	console.debug('Yes we are in a webex app!!!!!!', webexApp)
} else {
	console.debug('No webex app!!!!!!')
}

function editShare(shareId, permission) {
	const url = generateOcsUrl('/apps/files_sharing/api/v1/shares/{shareId}', { shareId })
	const req = {
		permissions: permission === 'write' ? 3 : undefined,
		expireDate: '',
	}
	axios.put(url, req).then((response) => {
		console.debug('EDIT SUCCESS', response.data?.ocs?.data)
		const publicLinkUrl = response.data?.ocs?.data?.url
		if (webexApp) {
			webexApp.setShareUrl(publicLinkUrl, publicLinkUrl, t('picker', 'Nextcloud picker')).then(() => {
				window.location = publicLinkUrl
			}).catch((error) => {
				console.error(error)
			})
		} else {
			window.location = publicLinkUrl
		}
	}).catch((error) => {
		console.debug(error)
		showError(t('picker', 'Error while editing the shared access'))
	})
}

function createPublicLink(path, permission) {
	const url = generateOcsUrl('/apps/files_sharing/api/v1/shares')
	const req = {
		path,
		shareType: 3,
		label: '[P] ' + t('picker', 'Picker link') + ' ' + moment().format('YYYY-MM-DD HH:mm:ss'),
	}
	axios.post(url, req).then((response) => {
		console.debug('ADD SUCCESS', response.data?.ocs?.data)
		const shareId = response.data?.ocs?.data?.id
		editShare(shareId, permission)
	}).catch((error) => {
		console.error(error)
		showError(t('picker', 'Error while creating the shared access'))
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

	permVue.$on('validate', (filePath, permission) => {
		createPublicLink(filePath, permission)
	})

	openFilePicker()
})
