package handlers

import (
	"encoding/json"
	"examen-final/pkg/scoreboard"
	"fmt"
	"net/http"
	"strconv"

	"github.com/labstack/echo/v4"
	"github.com/rs/zerolog"
)

type ScoreboardHandlers struct {
	logger zerolog.Logger
	board  *scoreboard.Scoreboard
}

func NewScoreboardHandlers(
	logger zerolog.Logger,
	board *scoreboard.Scoreboard,
) *ScoreboardHandlers {
	return &ScoreboardHandlers{
		logger: logger,
		board:  board,
	}
}

func (h *ScoreboardHandlers) Stream() echo.HandlerFunc {
	return func(c echo.Context) error {
		ch := make(chan *scoreboard.Match)
		h.board.AddClient(ch)
		defer h.board.RemoveClient(ch)

		h.logger.Info().Str("IP", c.RealIP()).Msg("Cliente conectado")

		c.Response().Header().Set(echo.HeaderContentType, "text/event-stream")
		c.Response().Header().Set(echo.HeaderCacheControl, "no-cache")
		c.Response().Header().Set(echo.HeaderConnection, "keep-alive")

		for msg := range ch {
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

		err := h.board.IncrementScore(team)
		if err != nil {
			h.logger.Error().
				Err(err).
				Int("team", team).
				Msg("[Increment] Error al aumentar puntaje")
		}
		return c.JSON(http.StatusOK, map[string]any{
			"message": "puntaje actualizado",
		})
	}
}

func (h *ScoreboardHandlers) TeamNames() echo.HandlerFunc {
	return func(c echo.Context) error {
		team1 := c.QueryParam("team1")
		team2 := c.QueryParam("team2")

		h.board.SetNames(team1, team2)
		return c.JSON(http.StatusOK, map[string]any{
			"message": "puntaje actualizado",
		})
	}
}

func (h *ScoreboardHandlers) SetRules() echo.HandlerFunc {
	return func(c echo.Context) error {
		var newRules scoreboard.Rules

		err := c.Bind(&newRules)
		if err != nil {
			h.logger.Error().Err(err).Msg("Error al bindear struct")
			return err
		}

		h.board.ConfigureMatch(newRules)

		rules := h.board.Rules()
		return c.JSON(200, rules)
	}
}

func (h *ScoreboardHandlers) GetRules() echo.HandlerFunc {
	return func(c echo.Context) error {
		rules := h.board.Rules()

		return c.JSON(200, rules)
	}
}
