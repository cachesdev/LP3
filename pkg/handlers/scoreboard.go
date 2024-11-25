package handlers

import (
	"encoding/json"
	"examen-final/pkg/scoreboard"
	"fmt"
	"net/http"
	"strconv"

	"github.com/k0kubun/pp/v3"
	"github.com/labstack/echo/v4"
	"go.uber.org/zap"
)

type ScoreboardHandlers struct {
	logger *zap.SugaredLogger
	board  *scoreboard.Scoreboard
}

func NewScoreboardHandlers(
	logger *zap.SugaredLogger,
	board *scoreboard.Scoreboard,
) *ScoreboardHandlers {
	return &ScoreboardHandlers{
		logger: logger,
		board:  board,
	}
}

func (h *ScoreboardHandlers) Stream() echo.HandlerFunc {
	return func(c echo.Context) error {
		ch := make(chan scoreboard.Game)
		h.board.AddClient(ch)
		defer h.board.RemoveClient(ch)

		h.logger.Infow("Cliente conectado", "IP", c.RealIP())

		c.Response().Header().Set(echo.HeaderContentType, "text/event-stream")
		c.Response().Header().Set(echo.HeaderCacheControl, "no-cache")
		c.Response().Header().Set(echo.HeaderConnection, "keep-alive")

		for msg := range ch {
			pp.Print(msg)
			jsonMsg, _ := json.Marshal(msg)
			fmt.Fprintf(c.Response(), "data: %s\n\n", jsonMsg)
			c.Response().Flush()
		}
		return nil
	}
}

func (h *ScoreboardHandlers) Increment() echo.HandlerFunc {
	return func(c echo.Context) error {
		team, _ := strconv.Atoi(c.QueryParam("team"))

		go h.board.IncrementScore(team)
		return c.JSON(http.StatusOK, map[string]any{
			"message": "puntaje actualizado",
		})
	}
}

func (h *ScoreboardHandlers) TeamNames() echo.HandlerFunc {
	return func(c echo.Context) error {
		team1 := c.QueryParam("team1")
		team2 := c.QueryParam("team2")

		go h.board.SetNames(team1, team2)
		return c.JSON(http.StatusOK, map[string]any{
			"message": "puntaje actualizado",
		})
	}
}
