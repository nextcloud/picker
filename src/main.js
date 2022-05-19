import Vue from 'vue'
import PermissionsModal from './PermissionsModal'

import { generateUrl, generateOcsUrl } from '@nextcloud/router'
import { dirname } from '@nextcloud/paths'
import axios from '@nextcloud/axios'

let lastPath = null

function editShare(shareId) {
	const url = generateOcsUrl('/apps/files_sharing/api/v1/shares/{shareId}', { shareId })
	const req = {
		permissions: 3,
		expireDate: '',
	}
	axios.put(url, req).then((response) => {
		console.debug('EDIT SUCCESS', response.data?.ocs?.data)
		window.location = response.data?.ocs?.data?.url
	}).catch((error) => {
		console.debug(error)
	})
}

function createPublicLink(path) {
	const url = generateOcsUrl('/apps/files_sharing/api/v1/shares')
	const req = {
		path,
		shareType: 3,
		label: t('public_picker', 'Public picker link'),
	}
	axios.post(url, req).then((response) => {
		console.debug('ADD SUCCESS', response.data?.ocs?.data)
		const shareId = response.data?.ocs?.data?.id
		editShare(shareId)
	}).catch((error) => {
		console.error(error)
	})
}

function openFilePicker(permVue) {
	OC.dialogs.filepicker(
		t('public_picker', 'Choose a file and start collaborating'),
		(targetPath) => {
			// createPublicLink(targetPath)
			permVue.setFilePath(targetPath)
			permVue.setOpen(true)
			lastPath = dirname(targetPath)
		},
		false, null, true, undefined, lastPath
	)
}

document.addEventListener('DOMContentLoaded', (event) => {
	const View = Vue.extend(PermissionsModal)
	const permVue = new View().$mount('#public_picker')
	console.debug('mmmmmmmmmmmmmm', permVue)
	permVue.$on('closed', () => {
		console.debug('CCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCCClosed')
		openFilePicker(permVue)
	})

	openFilePicker(permVue)
})
