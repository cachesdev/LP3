package embed

import "embed"

//go:embed vue/dist/*
var VueFS embed.FS

//go:embed svelte/build/*
var SvelteFS embed.FS
