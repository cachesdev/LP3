<script>
	let objs = $state([1]);
</script>

<button onclick={() => objs.push(1)} class="border-2">Agregar Objeto</button>
<div class="items- flex h-96 justify-between bg-red-300">
	{#each objs as _}
		<div class="bg-green-300">Hola Mundo</div>
	{/each}
</div>

<style>
</style>
