<script setup lang="ts">
import { api, apiUrl } from '@/lib/api'
import { ref, onMounted, onUnmounted, watchEffect } from 'vue'
import { Button } from '@/components/ui/button'

type Set = {
  team1: number
  team2: number
}

type Team = {
  name: string;
  score: number;
};

type Game = {
  currentScore: {
    team1: Team
    team2: Team
  }
  currentSet: number;
  sets: Array<Set>;
};

const score = ref<Game>()
watchEffect(() => {
  console.log(score.value)
})

// warning por set
// determinar WO won over
// game mejor de 6, a veces mejor de 9
// n-sets, mejor de 2, debe de ser programable.
// super tie
// tipo de set
// tie break de 10, 11 y 7
// game, 15, 30, 40, punto de oro si es empate. O por ventaja, advantage y views

onMounted(() => {
	const eventSource = new EventSource(`${apiUrl}/scoreboard/stream`)

	eventSource.onmessage = (event: MessageEvent<Game>) => {
		score.value = event.data
	}

	eventSource.onerror = (error) => {
		console.error('Error SSE:', error)
		eventSource.close()
	}

	onUnmounted(() => {
		eventSource.close()
	})
})

async function increment() {
	console.log(await api.patch('scoreboard/increment').json())
}
async function decrement() {
	console.log(await api.patch('scoreboard/decrement').json())
}
</script>

<template>
	<main>
		<Button @click="increment">increment</Button>
		<Button @click="decrement">decrement</Button>
		<ul>
      <li>{{ score }}</li>
		</ul>
	</main>
</template>
