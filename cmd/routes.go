package main

import (
	"net/http"
	"time"

	"github.com/labstack/echo/v4"
)

func (app *Application) addRoutes() {

	// healthcheck
	app.e.GET("/ping", func(c echo.Context) error {
		return c.JSON(http.StatusOK, map[string]any{
			"time":    time.Now(),
			"service": "go-api",
		})
	})

	// api
	api := app.e.Group("/api/v1")

	api.GET("/scoreboard/stream", app.h.Scoreboard.Stream())
	api.GET("/scoreboard/rules", app.h.Scoreboard.GetRules())
	api.POST("/scoreboard/rules", app.h.Scoreboard.SetRules())
	api.PATCH("/scoreboard/increment", app.h.Scoreboard.Increment())
	api.PATCH("/scoreboard/teams", app.h.Scoreboard.TeamNames())
}
