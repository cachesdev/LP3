package handlers

import (
	"examen-final/pkg/scoreboard"

	"go.uber.org/zap"
)

type Handlers struct {
	Scoreboard *ScoreboardHandlers
}

func New(logger *zap.SugaredLogger, board *scoreboard.Scoreboard) *Handlers {
	return &Handlers{
		Scoreboard: NewScoreboardHandlers(logger, board),
	}
}
