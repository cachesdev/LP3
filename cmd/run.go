package main

import (
	"examen-final/pkg/handlers"
	"examen-final/pkg/scoreboard"
	"fmt"

	"github.com/labstack/echo/v4"
	"go.uber.org/zap"
)

// run se debe considerar como un Main, pero que retorna un error. principalmente
// usado para injeccion de dependencias.
func run() error {
	// logger
	z, _ := zap.NewDevelopment(zap.AddCaller())
	logger := z.Sugar()

	// echo
	e := echo.New()

	//scoreboard
	board := scoreboard.New()

	// handlers
	handlers := handlers.New(logger, board)

	app := &Application{
		e:      e,
		logger: logger,
		h:      handlers,
	}

	err := app.Start()
	if err != nil {
		return fmt.Errorf("[run] Error durante inicializacion: %w", err)
	}
	return nil
}
