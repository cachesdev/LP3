package main

import (
	embed "examen-final"

	"github.com/labstack/echo/v4"
)

func (app *Application) addRoutes() {
	// frontend
	app.e.StaticFS("/", echo.MustSubFS(embed.VueFS, "frontend/dist"))

	// backend
	api := app.e.Group("/api/v1")

	api.GET("/scoreboard/stream", app.h.Scoreboard.Stream())
	api.PATCH("/scoreboard/increment", app.h.Scoreboard.Increment())
	api.PATCH("/scoreboard/teams", app.h.Scoreboard.TeamNames())
}
