<template>
	<div>
		<Modal v-if="open"
			size="full"
			@close="onClose">
			<div class="modal-inner-content">
				<img :src="imageSrc"
					class="file-image" />
				<label>
					{{ this.fileName }}
				</label>
				<button @click="onValidate">
					<CheckIcon
						class="check-icon"
						:size="16" />
					<span>
						{{ t('public_picker', 'Start collaboration') }}
					</span>
				</button>
			</div>
		</Modal>
	</div>
</template>

<script>
import CheckIcon from 'vue-material-design-icons/Check'
import Modal from '@nextcloud/vue/dist/Components/Modal'
import { generateUrl } from '@nextcloud/router'
import { basename } from '@nextcloud/paths'

export default {
	name: 'PermissionsModal',

	components: {
		Modal,
		CheckIcon,
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

	computed: {
		imageSrc() {
			return generateUrl('/apps/public_picker/preview?path={filePath}&x=100&y=100', { filePath: this.filePath })
		},
		fileName() {
			return this.filePath
				? basename(this.filePath)
				: ''
		},
	},

	methods: {
		setOpen(val) {
			this.open = val
		},
		setFilePath(val) {
			this.filePath = val
		},
		onClose() {
			this.open = false
			this.$emit('closed')
		},
		onValidate() {
			this.open = false
			this.$emit('validate', this.filePath)
		},
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

.modal-inner-content {
	display: flex;
	flex-direction: column;
	align-items: center;
	width: 100%;
	height: 100%;
	.file-image {
		width: auto;
		height: 100px;
	}
	button {
		display: flex;
		align-items: center;
		.check-icon {
			color: var(--color-success);
			margin-right: 8px;
		}
	}
}
</style>
