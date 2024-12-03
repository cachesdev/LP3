package main

import (
	"examen-final/pkg/handlers"
	"fmt"

	"github.com/labstack/echo/v4"
	"github.com/rs/zerolog"
)

type Application struct {
	e      *echo.Echo
	logger zerolog.Logger
	h      *handlers.Handlers
}

func (app *Application) Start() error {
	app.addMiddleware()
	app.addRoutes()

	err := app.e.Start(":4000")
	if err != nil {
		return fmt.Errorf("[Start] Error durante ejecucion de servidor Echo: %w", err)
	}

	return nil
}
