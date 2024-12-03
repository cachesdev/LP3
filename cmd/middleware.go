package main

import (
	"github.com/labstack/echo/v4"
	"github.com/labstack/echo/v4/middleware"
)

func (app *Application) addMiddleware() {
	// Pre Router
	app.e.Pre(middleware.RemoveTrailingSlash())

	app.e.Use(middleware.CORSWithConfig(middleware.CORSConfig{
		AllowOrigins: []string{"*"},
		AllowMethods: []string{echo.GET, echo.POST, echo.PUT, echo.DELETE, echo.PATCH, echo.OPTIONS},
		AllowHeaders: []string{"*"},
	}))

	app.e.Use(middleware.RequestLoggerWithConfig(middleware.RequestLoggerConfig{
		LogURI:    true,
		LogStatus: true,
		LogMethod: true,
		LogValuesFunc: func(c echo.Context, v middleware.RequestLoggerValues) error {
			app.logger.Info().
				Str("Method", v.Method).
				Int("status", v.Status).
				Str("URI", v.URI).
				Msg("Request")

			return nil
		},
	}))
}
