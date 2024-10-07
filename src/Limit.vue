<template>
	<div class="update">
		<p>
			{{ state.message }}
		</p>
		<a :href="state.supportLink"
			target="_blank"
			style="margin-top: 12px;">
			<NcButton type="primary">
				<template #icon>
					<OpenInNewIcon />
				</template>
				{{ state.supportLinkMessage }}
			</NcButton>
		</a>
	</div>
</template>

<script>
import OpenInNewIcon from 'vue-material-design-icons/OpenInNew.vue'
import { NcButton } from '@nextcloud/vue'
import { loadState } from '@nextcloud/initial-state'

export default {
	name: 'Limit',

	components: {
		NcButton,
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
