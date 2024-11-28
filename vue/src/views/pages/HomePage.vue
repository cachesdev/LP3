<script setup lang="ts">
import { api, apiUrl } from '@/lib/api'
import { ref, onMounted, onUnmounted, watchEffect, computed } from 'vue'
import { Button } from '@/components/ui/button'

interface Game {
    team1Score: number;
    team2Score: number;
    isTiebreak: boolean;
}

interface Set {
    games: Game[];
    team1Games: number;
    team2Games: number;
}

interface Match {
    sets: Set[];
    currentSet: Set | null;
    currentGame: Game | null;
    team1: string;
    team2: string;
    team1Sets: number;
    team2Sets: number;
}

const score = ref<Match>()
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

	eventSource.onmessage = (event: MessageEvent) => {
		score.value = JSON.parse(event.data) as Match
	}

	eventSource.onerror = (error) => {
		console.error('Error SSE:', error)
		eventSource.close()
	}

	api.patch('scoreboard/teams', {
		searchParams: { team1: 'Equipo Cañon', team2: 'Equipo Trompeta' },
	}).then()

	onUnmounted(() => {
		eventSource.close()
	})
})

const prettyScore = computed(() => {
	return JSON.stringify(score.value, null, 4)
})

async function increment(team: number) {
	console.log(await api.patch('scoreboard/increment', { searchParams: { team } }).json())
}
</script>

<template>
	<main>
		<div class="sticky top-0">
		<Button @click="increment(1)">increment team 1</Button>
		<Button @click="increment(2)">increment team 2</Button>
		</div>
		<div class="prose">
			<pre v-html="prettyScore"></pre>
		</div>
	</main>
</template>
