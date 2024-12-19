/**
 * SPDX-FileCopyrightText: 2022 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

import Vue from 'vue'
import './bootstrap.js'
import Limit from './Limit.vue'

document.addEventListener('DOMContentLoaded', (event) => {
	const View = Vue.extend(Limit)
	new View().$mount('#limit')
})
