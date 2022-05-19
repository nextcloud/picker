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
				<fieldset class="perm-list">
					<label v-for="p in permissions"
						:key="p.id"
						:class="{ permission: true, selected: selectedPermission === p.id }"
						:for="'perm-' + p.id">
						<input :id="'perm-' + p.id"
							   v-model="selectedPermission"
							   name="permission"
							   :value="p.id"
							   type="radio">
						<EyeIcon v-if="p.id === 'read'"
							 class="perm-icon"
							:size="20" />
						<PencilIcon v-else-if="p.id === 'write'"
							class="perm-icon"
							:size="20" />
						<span class="perm-title">
							{{ p.label }}
						</span>
					</label>
				</fieldset>
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
import PencilIcon from 'vue-material-design-icons/Pencil'
import EyeIcon from 'vue-material-design-icons/Eye'
import Modal from '@nextcloud/vue/dist/Components/Modal'
import { generateUrl } from '@nextcloud/router'
import { basename } from '@nextcloud/paths'

const permissions = [
	{
		id: 'read',
		label: t('public_picker', 'View only'),
	},
	{
		id: 'write',
		label: t('public_picker', 'Edit'),
	},
	/*
	{
		id: 'plop',
		label: t('public_picker', 'plop'),
	},
	*/
]

export default {
	name: 'PermissionsModal',

	components: {
		Modal,
		CheckIcon,
		PencilIcon,
		EyeIcon,
	},

	props: {
	},

	data() {
		return {
			open: false,
			filePath: null,
			selectedPermission: 'read',
			permissions,
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
			this.$emit('validate', this.filePath, this.selectedPermission)
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
	.perm-list {
		display: flex;
		flex-direction: column;
		.permission {
			display: flex;
			align-items: center;
			width: 300px;
			border: 2px solid var(--color-border-dark);
			border-bottom: 0;
			&:first-child {
				border-top-left-radius: var(--border-radius);
				border-top-right-radius: var(--border-radius);
			}
			&:last-child {
				border-bottom: 2px solid var(--color-border-dark);
				border-bottom-left-radius: var(--border-radius);
				border-bottom-right-radius: var(--border-radius);
			}
			&:focus,
			&:hover {
				background-color: var(--color-background-hover);
			}
			&.selected {
				background: var(--color-primary-light-hover);
				border-color: var(--color-primary);
				border-bottom: 2px solid var(--color-primary);
			}
			&.selected + .permission {
				border-top: 0;
			}
			input {
				// display: none;
				opacity: 0;
				width: 0;
			}
			.perm-icon {
				margin: 0 10px 0 10px;
			}
		}
	}
}
</style>
