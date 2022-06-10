<template>
	<div>
		<Modal v-if="open"
			size="full"
			:out-transition="false"
			@close="onClose">
			<div class="modal-inner-content">
				<div class="header">
					<img :src="imageSrc"
						class="file-image">
					<div class="labels">
						<label class="file-name">
							{{ fileName }}
						</label>
						<label v-if="fileDir"
							class="file-dir">
							{{ t('picker', 'in') + ' ' + fileDir }}
						</label>
					</div>
				</div>
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
				<div class="buttons">
					<Button @click="onClose">
						{{ t('picker', 'Cancel') }}
					</Button>
					<Button type="primary"
						@click="onValidate">
						<template #icon>
							<CheckIcon
								:size="20" />
						</template>
						{{ t('picker', 'Start collaborating') }}
					</Button>
				</div>
			</div>
		</Modal>
	</div>
</template>

<script>
import CheckIcon from 'vue-material-design-icons/Check'
import PencilIcon from 'vue-material-design-icons/Pencil'
import EyeIcon from 'vue-material-design-icons/Eye'
import Button from '@nextcloud/vue/dist/Components/Button'
import Modal from '@nextcloud/vue/dist/Components/Modal'
import { generateUrl } from '@nextcloud/router'
import { basename, dirname } from '@nextcloud/paths'

const permissions = [
	{
		id: 'read',
		label: t('picker', 'View only'),
	},
	{
		id: 'write',
		label: t('picker', 'Edit'),
	},
	/*
	{
		id: 'plop',
		label: t('picker', 'plop'),
	},
	*/
]

export default {
	name: 'PermissionsModal',

	components: {
		Modal,
		Button,
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

	computed: {
		imageSrc() {
			return generateUrl('/apps/picker/preview?path={filePath}&x=100&y=100', { filePath: this.filePath })
		},
		fileName() {
			return this.filePath
				? basename(this.filePath)
				: ''
		},
		fileDir() {
			return this.filePath
				? basename(dirname(this.filePath))
				: ''
		},
	},

	watch: {
	},

	mounted() {
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
	.header {
		display: flex;
		align-items: center;
		width: 300px;
		.file-image {
			width: auto;
			height: 70px;
		}
		.labels {
			display: flex;
			flex-direction: column;
			margin-left: 12px;
			.file-name {
				font-weight: bold;
				font-size: 1.2em;
			}
			.file-dir {
				opacity: 0.7;
			}
		}
	}
	.buttons {
		display: flex;
		> * {
			margin: 0 10px 0 10px;
		}
	}
	.perm-list {
		display: flex;
		flex-direction: column;
		margin: 44px 0 44px 0;
		.permission {
			display: flex;
			align-items: center;
			width: 300px;
			height: 44px;
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
				background: var(--color-background-hover);
			}
			&.selected {
				background: var(--color-primary-light-hover);
				border-color: var(--color-primary);
				border-bottom: 2px solid var(--color-primary);
				font-weight: bold;
			}
			&.selected + .permission {
				border-top: 0;
			}
			input {
				// display: none;
				opacity: 0;
				width: 0;
				margin: 0;
			}
			.perm-icon {
				margin: 0 12px 0 12px;
			}
		}
	}
}
</style>
