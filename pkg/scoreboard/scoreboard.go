package scoreboard

import (
	"fmt"
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

type Game struct {
	Team1 int `json:"team1"`
	Team2 int `json:"team2"`
}

type Set struct {
	Games []Game `json:"games"`
}

type Match struct {
	Teams struct {
		Team1 string `json:"team1"`
		Team2 string `json:"team2"`
	} `json:"teams"`
	Sets       []Set `json:"sets"`
	CurrentSet struct {
		Set         int    `json:"set"`
		Games       []Game `json:"games"`
		CurrentGame Game   `json:"game"`
	} `json:"currentSet"`
}

type Scoreboard struct {
	sync.Mutex
	match   Match
	clients map[chan Match]struct{}
	store   EventStore
}

func NewScoreboard(store EventStore) *Scoreboard {
	return &Scoreboard{
		store:   store,
		clients: make(map[chan Match]struct{}),
	}
}

func (s *Scoreboard) AddClient(ch chan Match) {
	s.Lock()
	defer s.Unlock()
	s.clients[ch] = struct{}{}
}

func (s *Scoreboard) RemoveClient(ch chan Match) {
	s.Lock()
	defer s.Unlock()
	delete(s.clients, ch)
	close(ch)
}

func (s *Scoreboard) Broadcast(message Match) {
	s.Lock()
	defer s.Unlock()
	for ch := range s.clients {
		ch <- message
	}
}

func (s *Scoreboard) UpdateScore(newScore Match) {
	s.Lock()
	s.match = newScore
	s.Unlock()
	s.Broadcast(newScore)
}

func (s *Scoreboard) SetNames(team1 string, team2 string) {
	s.Lock()
	s.match.Teams.Team1 = team1
	s.match.Teams.Team2 = team2
	s.Unlock()
	s.Broadcast(s.match)
}

func (s *Scoreboard) IncrementScore(team int) error {
	s.Lock()

	machine := &ScoreMachine{CurrentState: determineCurrentState(s.match.CurrentScore)}
	transition := machine.ValidateAndTransition(s.match.CurrentScore, team)

	if !transition.IsValid {
		return fmt.Errorf("[IncrementScore] Transicion de puntos invalida")
	}

	// event := ScoreEvent{
	// 	ID:        uuid.New().String(),
	// 	Type:      "POINT_SCORED",
	// 	Team:      team,
	// 	Timestamp: time.Now(),
	// 	GameID:    s.game.ID,
	// }

	// if err := s.store.SaveEvent(event); err != nil {
	// 	return fmt.Errorf("failed to save score event: %w", err)
	// }

	// Actualizar puntos en base a la transicion
	if team == 1 {
		s.match.CurrentScore.Team1 = transition.Points
	} else {
		s.match.CurrentScore.Team2 = transition.Points
	}

	if transition.SetWon {
		s.match.Sets = append(s.match.Sets, s.game.CurrentScore)
		s.match.CurrentScore = Score{} // Reiniciar puntos
		s.match.CurrentSet++
	}

	s.Unlock()
	s.Broadcast(s.match)
	return nil
}

func determineCurrentState(match Match) GameState {
	switch {
	case match.CurrentSet.Set == 4:
		return MatchOver
	case len(match.CurrentSet.Games) == 6:
		return GameOver
	case len(match.CurrentSet.Games) == 5 &&
		match.CurrentSet.CurrentGame.Team1 == 40 ||
		match.CurrentSet.CurrentGame.Team2 == 40:
		if determineDeuce(match) {
			return SetPointDeuce
		}
		return SetPoint
	case match.CurrentSet.CurrentGame.Team1 == 40 &&
		match.CurrentSet.CurrentGame.Team2 == 40:
		return Deuce
	case match.CurrentSet.CurrentGame.Team1 == 40 ||
		match.CurrentSet.CurrentGame.Team2 == 40:
		return GamePoint
	default:
		return RegularPlay
	}
}

func determineDeuce(match Match) bool {
	if match.CurrentSet.CurrentGame.Team1 == 40 &&
		match.CurrentSet.CurrentGame.Team2 == 40 {
		return true
	}
	return false
}
