<template>
	<div class="update">
		<p>
			{{ state.message }}
		</p>
		<a :href="state.supportLink"
			target="_blank"
			style="margin-top: 12px;">
			<Button type="primary">
				<template #icon>
					<OpenInNewIcon />
				</template>
				{{ state.supportLinkMessage }}
			</Button>
		</a>
	</div>
</template>

<script>
import OpenInNewIcon from 'vue-material-design-icons/OpenInNew'
import Button from '@nextcloud/vue/dist/Components/Button'
import { loadState } from '@nextcloud/initial-state'

export default {
	name: 'Limit',

	components: {
		Button,
		OpenInNewIcon,
	},

	props: {
	},

	data() {
		return {
			state: loadState('picker', 'limit'),
		}
	},

	computed: {
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
