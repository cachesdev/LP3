<script lang="ts">
	import { api, apiUrl } from '$lib/app/api';
	import { onMount } from 'svelte';
	import { Button, buttonVariants } from '$lib/components/ui/button';
	import { Input } from '$lib/components/ui/input/index.js';
	import { Label } from '$lib/components/ui/label/index.js';
	import * as Dialog from '$lib/components/ui/dialog/index.js';

	interface Game {
		team1Score: number;
		team2Score: number;
		isTiebreak: boolean;
		team1TBScore: number;
		team2TBScore: number;
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

	type Rules = {
		setsToWin: number;
		gamesPerSet: number;
	};

	let rules = $state<Rules>();
	let rulesDialogOpen = $state(false);

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
			.get<Rules>('scoreboard/rules')
			.json()
			.then((data) => (rules = data));

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

	async function updateRules() {
		await api.post('scoreboard/rules', { json: rules });
		rules = await api.get<Rules>('scoreboard/rules').json();
		rulesDialogOpen = false;
	}
</script>

<main class="flex h-dvh w-dvw items-center justify-center gap-4">
	<div class="flex h-full w-full max-w-2xl flex-col items-center justify-center">
		<!-- {@render botonera()} -->
		{@render scoreBoard()}
	</div>
</main>

{#snippet scoreBoard()}
	<div class="flex h-32 w-full max-w-2xl bg-blue-300">
		<!-- nombres teams -->
		<ul class="full flex w-full flex-col border bg-green-300">
			<li class="font-bold">Equipos</li>
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
				<ul class="flex w-16 flex-col">
					<li class="font-bold">set: {id + 1}</li>
					{#if set.games.some((game) => game.isTiebreak)}
						<li>
							{set.team1Games}<sup>{set.games.find((game) => game.isTiebreak)?.team1TBScore}</sup>
						</li>
						<li>
							{set.team2Games}<sup>{set.games.find((game) => game.isTiebreak)?.team2TBScore}</sup>
						</li>
					{:else}
						<li>
							{set.team1Games}
						</li>
						<li>
							{set.team2Games}
						</li>
					{/if}
				</ul>
			{/each}
		</div>
		<!-- set en progreso -->
		<ul class="flex w-16 flex-col bg-slate-300">
			<li class="font-bold">Set</li>
			<li>{score?.currentSet?.team1Games}</li>
			<li>{score?.currentSet?.team2Games}</li>
		</ul>
		<!-- game en progreso -->
		<ul class="flex w-16 flex-col bg-orange-300">
			<li class="font-bold">Game</li>
			<li>{score?.currentGame?.team1Score}</li>
			<li>{score?.currentGame?.team2Score}</li>
		</ul>
		<ul class="flex w-16 flex-col bg-teal-300">
			<li class="font-bold">TB</li>
			<li>{score?.currentGame?.team1TBScore}</li>
			<li>{score?.currentGame?.team2TBScore}</li>
		</ul>
	</div>
{/snippet}

{#snippet botonera()}
	<div class="flex gap-2">
		<Button onclick={() => increment(1)}>incrementar {score?.team1}</Button>
		<Button onclick={() => increment(2)}>incrementar {score?.team2}</Button>
		{@render configuracion()}
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

{#snippet configuracion()}
	<Dialog.Root bind:open={rulesDialogOpen}>
		<Dialog.Trigger class={buttonVariants({ variant: 'default' })}>Configuracion</Dialog.Trigger>
		<Dialog.Content class="sm:max-w-[425px]">
			<Dialog.Header>
				<Dialog.Title>Editar Configuracion</Dialog.Title>
				<Dialog.Description>Esto modifica la configuracion del partido.</Dialog.Description>
			</Dialog.Header>
			{#if rules}
				<div class="grid gap-4 py-4">
					<div class="grid grid-cols-4 items-center gap-4">
						<Label for="setsToWin" class="text-right">Sets a Ganar</Label>
						<Input id="setsToWin" bind:value={rules.setsToWin} type="number" class="col-span-3" />
					</div>
					<div class="grid grid-cols-4 items-center gap-4">
						<Label for="gamesPerSet" class="text-right">Juegos por Set</Label>
						<Input
							id="GamesPerSet"
							bind:value={rules.gamesPerSet}
							type="number"
							class="col-span-3"
						/>
					</div>
				</div>
				<Dialog.Footer>
					<Button onclick={updateRules}>Save changes</Button>
				</Dialog.Footer>
			{/if}
		</Dialog.Content>
	</Dialog.Root>
{/snippet}
