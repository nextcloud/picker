<template>
	<div>
		<Modal v-if="open"
			size="full"
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
			filePath: null,
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

<style scoped lang="scss">
::v-deep .modal-header {
	background: var(--color-main-background);
	&.invisible {
		visibility: unset !important;
	}
	.close-icon {
		color: var(--color-main-text);
	}
}
</style>
}
