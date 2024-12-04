package main

import (
	"examen-final/pkg/handlers"
	"examen-final/pkg/scoreboard"
	"fmt"
	"os"
	"strings"
	"time"

	"github.com/labstack/echo/v4"
	"github.com/rs/zerolog"
)

// run se debe considerar como un Main, pero que retorna un error. principalmente
// usado para injeccion de dependencias.
func run() error {
	// logger
	logWriter := zerolog.ConsoleWriter{
		Out:        os.Stderr,
		NoColor:    false,
		TimeFormat: time.RFC3339,
		FormatErrFieldValue: func(i interface{}) string {
			// Imprime errores en rojo y con newline
			return "\033[31m" + strings.ReplaceAll(fmt.Sprintf("%v", i), "\\n", "\n") + "\033[0m"
		},
	}

	logger := zerolog.New(logWriter).With().Timestamp().Caller().Logger()

	// echo
	e := echo.New()

	// scoreboard
	board := scoreboard.NewScoreboard()
	board.Initialize()

	// handlers
	handlers := handlers.New(logger, board)

	app := &Application{
		e:      e,
		logger: logger,
		h:      handlers,
	}

	pid := os.Getpid()
	logger.Info().Int("PID", pid).Msg("PID")

	err := app.Start()
	if err != nil {
		return fmt.Errorf("[run] Error durante inicializacion: %w", err)
	}

	return nil
}
