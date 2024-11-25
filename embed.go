package embed

import "embed"

//go:embed frontend/dist/*
var VueFS embed.FS
