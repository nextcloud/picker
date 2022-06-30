<template>
	<div class="update">
		<p>
			{{ t('picker', 'Would you like to log in?') }}
		</p>
		<a :href="state.publicLink"
			style="margin-top: 12px;">
			<Button type="primary">
				<template #icon>
					<LinkVariantIcon />
				</template>
				{{ t('picker', 'Public link') }}
			</Button>
		</a>
		<a :href="internalLink"
			style="margin-top: 12px;">
			<Button type="primary">
				<template #icon>
					<OpenInNewIcon />
				</template>
				{{ t('picker', 'View the file as a user') }}
			</Button>
		</a>
	</div>
</template>

<script>
import LinkVariantIcon from 'vue-material-design-icons/LinkVariant'
import OpenInNewIcon from 'vue-material-design-icons/OpenInNew'
import Button from '@nextcloud/vue/dist/Components/Button'
import { loadState } from '@nextcloud/initial-state'
import { generateUrl } from '@nextcloud/router'

export default {
	name: 'WebexShare',

	components: {
		Button,
		LinkVariantIcon,
		OpenInNewIcon,
	},

	props: {
		userId: {
			type: String,
			required: true,
		},
	},

	data() {
		return {
			state: loadState('picker', 'webex-share'),
		}
	},

	computed: {
		internalLink() {
			console.debug('this.userId', this.userId)
			// return generateUrl('/login?user={userId}', { userId: this.userId })
			return generateUrl('/f/{fileId}', { fileId: this.state.fileId })
		},
	},

	watch: {
	},

	mounted() {
	},

	methods: {
	},
}
</script>

<style scoped lang="scss">
.update {
	display: flex;
	flex-direction: column;
	align-items: center;
	a {
		text-decoration: darkviolet;
	}
}
</style>
