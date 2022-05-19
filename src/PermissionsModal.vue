<template>
	<div>
		<Modal v-if="open"
			@close="onClose">
			Super modal
		</Modal>
	</div>
</template>

<script>
import Modal from '@nextcloud/vue/dist/Components/Modal'

export default {
	name: 'PermissionsModal',

	components: {
		Modal,
	},

	props: {
	},

	data() {
		return {
			open: false,
		}
	},

	watch: {
	},

	mounted() {
	},

	methods: {
		setOpen(val) {
			this.open = val
		},
		onClose() {
			this.open = false
			this.$emit('closed')
		}
	},
}
</script>
