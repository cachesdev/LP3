<script lang="ts">
	import { api, apiUrl } from '$lib/app/api';
	import { onMount } from 'svelte';
	import { Button } from '$lib/components/ui/button';
	import * as Dialog from '$lib/components/ui/dialog/index.js';

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

	let score = $state<Match>();
	let eventSource = $state<EventSource>();
	let eventSourceState = $derived.by(() => {
		switch (eventSource?.readyState) {
			case EventSource.CONNECTING:
				return 'CONNECTING';
			case EventSource.OPEN:
				return 'OPEN';
			case EventSource.CLOSED:
				return 'CLOSED';
			default:
				return 'UNKNOWN';
		}
	});
	$inspect(score);

	// warning por set
	// determinar WO won over
	// game mejor de 6, a veces mejor de 9
	// n-sets, mejor de 2, debe de ser programable.
	// super tie
	// tipo de set
	// tie break de 10, 11 y 7
	// game, 15, 30, 40, punto de oro si es empate. O por ventaja, advantage y views

	onMount(() => {
		eventSource = new EventSource(`${apiUrl}/scoreboard/stream`);

		eventSource.onmessage = (event: MessageEvent) => {
			score = JSON.parse(event.data) as Match;
		};

		eventSource.onerror = (error) => {
			console.error('Error SSE:', error);
			eventSource!.close();
		};

		api
			.patch('scoreboard/teams', {
				searchParams: { team1: 'Equipo 1', team2: 'Equipo 2' }
			})
			.then();

		return () => {
			eventSource!.close();
		};
	});

	async function increment(team: number) {
		console.log(await api.patch('scoreboard/increment', { searchParams: { team } }).json());
	}
</script>

<main class="flex h-dvh w-dvw items-center justify-center gap-4">
	<div class="prose max-h-96 w-96">
		<pre class="max-h-96 overflow-auto">{JSON.stringify(score, null, 4)}</pre>
	</div>
	<div class="flex h-full w-full max-w-2xl flex-col items-center justify-center">
		{@render botonera()}
		{@render scoreBoard()}
	</div>
</main>

{#snippet scoreBoard()}
	<div class="flex h-96 w-full max-w-2xl bg-blue-300">
		<!-- nombres teams -->
		<ul class="full flex w-full flex-col border bg-green-300">
			<li>Equipos</li>
			<li>
				{score?.team1}
			</li>
			<li>
				{score?.team2}
			</li>
		</ul>
		<!-- sets -->
		<div class="flex bg-red-300">
			{#each score?.sets || [] as set, id}
				<ul class="flex flex-col w-16">
					<li>set: {id+1}</li>
					<li>
						{set.team1Games}
					</li>
					<li>
						{set.team2Games}
					</li>
				</ul>
			{/each}
		</div>
		<!-- set en progreso -->
		<ul class="flex w-16 flex-col bg-slate-300">
			<li>Set</li>
			<li>{score?.currentSet?.team1Games}</li>
			<li>{score?.currentSet?.team2Games}</li>
		</ul>
		<!-- game en progreso -->
		<ul class="flex w-16 flex-col bg-orange-300">
			<li>Game</li>
			<li>{score?.currentGame?.team1Score}</li>
			<li>{score?.currentGame?.team2Score}</li>
		</ul>
	</div>
{/snippet}

{#snippet botonera()}
	<div class="flex gap-2">
		<Button onclick={() => increment(1)}>increment team 1</Button>
		<Button onclick={() => increment(2)}>increment team 2</Button>
		<Dialog.Root>
			<Dialog.Trigger><Button>Debug Info</Button></Dialog.Trigger>
			<Dialog.Content>
				<Dialog.Header>
					<Dialog.Title>estado de eventSource: {eventSourceState}</Dialog.Title>
					<Dialog.Description class="prose max-h-96 overflow-auto">
						<pre>{JSON.stringify(score, null, 4)}</pre>
					</Dialog.Description>
				</Dialog.Header>
			</Dialog.Content>
		</Dialog.Root>
	</div>
{/snippet}
