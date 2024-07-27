import Vue from 'vue'
import './bootstrap.js'
// import PermissionsModal from './PermissionsModal.vue'

import { generateOcsUrl } from '@nextcloud/router'
// import { dirname } from '@nextcloud/paths'
import { showError, getFilePickerBuilder } from '@nextcloud/dialogs'
import '@nextcloud/dialogs/style.css'
import axios from '@nextcloud/axios'
import moment from '@nextcloud/moment'
// import { PencilIcon, EyeIcon } from 'vue-material-design-icons/OpenInNew.vue'
import InternalIcon from 'vue-material-design-icons/OpenInNew.vue'
// import '../css/main.scss'

// let permVue
// let lastPath = ''
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

function editShare(shareId, permission) {
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
		} else {
			console.debug('[picker main] after edit, there is NO webex app => setShareUrl')
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

/* function onFileSelected(targetPath) {
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
} */

function openFilePicker() {
	const filePicker = getFilePickerBuilder(t('picker', 'Choose a file and start collaborating'))
		.setMultiSelect(false)
		.allowDirectories(true)
		// .startAt(lastPath)
		.addButton({
			label: t('picker', 'View only'),
			callback: (nodes) => {
				const target = nodes[0]
				const targetPath = target.path
				const permission = 'read'
				createPublicLink(targetPath, permission)
			},
			type: 'primary',
			icon: InternalIcon,
		})
		.addButton({
			label: t('picker', 'Edit'),
			callback: (nodes) => {
				const target = nodes[0]
				const targetPath = target.path
				const permission = 'write'
				createPublicLink(targetPath, permission)
			},
			type: 'primary',
			icon: InternalIcon,
		})
		.build()
	filePicker.pick()
}

document.addEventListener('DOMContentLoaded', (event) => {
	// const View = Vue.extend(PermissionsModal)
	const View = Vue
	// permVue = new View().$mount('#picker')
	new View().$mount('#picker')
	// permVue.$on('closed', () => {
	// openFilePicker()
	// })

	// permVue.$on('validate', (filePath, permission) => {
	// createPublicLink(filePath, permission)
	// })

	openFilePicker()
})
