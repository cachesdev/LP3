package scoreboard

import "time"

type ScoreEvent struct {
	ID        string    `json:"id"`
	Type      string    `json:"type"` // "POINT_SCORED", "SET_WON", etc
	Team      int       `json:"team"`
	Timestamp time.Time `json:"timestamp"`
	GameID    string    `json:"game_id"`
}

type EventStore interface {
	SaveEvent(event ScoreEvent) error
	GetEvents(gameID string) ([]ScoreEvent, error)
	RollbackToEvent(gameID string, eventID string) error
}
