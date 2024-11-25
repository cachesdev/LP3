package handlers

import (
	"encoding/json"
	"examen-final/pkg/scoreboard"
	"fmt"
	"net/http"

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
		h.logger.Info("Incrementing...")

		pp.Println("Antes increment")
		go h.board.IncrementScore()
		pp.Println("Despues increment")
		return c.JSON(http.StatusOK, map[string]any{
			"message": "puntaje actualizado",
		})
	}
}

func (h *ScoreboardHandlers) Decrement() echo.HandlerFunc {
	return func(c echo.Context) error {
		input := scoreboard.Game{}

		err := c.Bind(&input)
		if err != nil {
			h.logger.Errorw("[Send] Error when binding struct", "Err", err)
			return err
		}

		h.board.DecrementScore()
		return c.JSON(http.StatusOK, map[string]any{
			"message": "puntaje actualizado",
		})
	}
}
