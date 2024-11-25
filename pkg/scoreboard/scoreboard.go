package scoreboard

import (
	"sync"
	"time"
)

type ScoreEvent struct {
	ID        string    `json:"id"`
	Type      string    `json:"type"` // "POINT_SCORED", "SET_WON", etc
	Team      int       `json:"team"`
	Timestamp time.Time `json:"timestamp"`
	GameID    string    `json:"game_id"`
	// Can add more metadata as needed
}

type EventStore interface {
	SaveEvent(event ScoreEvent) error
	GetEvents(gameID string) ([]ScoreEvent, error)
	RollbackToEvent(gameID string, eventID string) error
}

type Score struct {
	Team1 int `json:"team1"`
	Team2 int `json:"team2"`
}

type Game struct {
	Teams struct {
		Team1 string `json:"team1"`
		Team2 string `json:"team2"`
	} `json:"teams"`
	CurrentScore Score   `json:"currentScore"`
	SetScores    []Score `json:"setScores"`
	CurrentSet   int     `json:"currentSet"`
}

type Scoreboard struct {
	sync.Mutex
	game    Game
	clients map[chan Game]struct{}
	store   EventStore
}

func NewScoreboard(store EventStore) *Scoreboard {
	return &Scoreboard{
		store:   store,
		clients: make(map[chan Game]struct{}),
	}
}

func (s *Scoreboard) AddClient(ch chan Game) {
	s.Lock()
	defer s.Unlock()
	s.clients[ch] = struct{}{}
}

func (s *Scoreboard) RemoveClient(ch chan Game) {
	s.Lock()
	defer s.Unlock()
	delete(s.clients, ch)
	close(ch)
}

func (s *Scoreboard) Broadcast(message Game) {
	s.Lock()
	defer s.Unlock()
	for ch := range s.clients {
		ch <- message
	}
}

func (s *Scoreboard) UpdateScore(newScore Game) {
	s.Lock()
	s.game = newScore
	s.Unlock()
	s.Broadcast(newScore)
}

func (s *Scoreboard) IncrementScore() {
	s.Lock()
	s.game.CurrentScore.Team1.Score += 1
	s.game.CurrentScore.Team2.Score += 1
	s.Unlock()
	s.Broadcast(s.game)
}

func (s *Scoreboard) DecrementScore() {
	s.Lock()
	s.game.CurrentScore.Team1.Score = 0
	s.game.CurrentScore.Team2.Score = 0
	s.Unlock()
	s.Broadcast(s.game)
}
