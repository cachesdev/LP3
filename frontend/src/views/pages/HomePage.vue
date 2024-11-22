<script setup lang="ts">
import { Button } from '@/components/ui/button'
</script>
<template>
	<main>
		<div class="text-6xl">Hello World</div>
		<RouterLink to="/about">
			<Button>Click Me</Button>
		</RouterLink>
	</main>
</template>
