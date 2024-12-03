package handlers

import (
	"examen-final/pkg/scoreboard"

	"github.com/rs/zerolog"
)

type Handlers struct {
	Scoreboard *ScoreboardHandlers
}

func New(logger zerolog.Logger, board *scoreboard.Scoreboard) *Handlers {
	return &Handlers{
		Scoreboard: NewScoreboardHandlers(logger, board),
	}
}
