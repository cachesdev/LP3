package scoreboard

import (
	"fmt"
	"sync"
)

type ScoreKeeperImpl struct {
	mu           sync.RWMutex
	match        *Match
	rules        *PadelRulesEngine
	stateMachine *PadelStateMachine
	observers    []func(*Match)
}

func NewScoreKeeper(team1, team2 string) *ScoreKeeperImpl {
	rules := NewPadelRulesEngine()
	match := &Match{
		Team1: team1,
		Team2: team2,
		CurrentSet: &Set{
			Games: make([]Game, 0),
		},
		CurrentGame: &Game{},
		Sets:        make([]Set, 0),
	}

	return &ScoreKeeperImpl{
		match:        match,
		rules:        rules,
		stateMachine: NewPadelStateMachine(rules),
		observers:    make([]func(*Match), 0),
	}
}

func (sk *ScoreKeeperImpl) IncrementScore(team int) error {
	sk.mu.Lock()
	defer sk.mu.Unlock()

	if !sk.rules.IsValidPoint(sk.match, team) {
		return fmt.Errorf("[IncrementScore] Punto invalido")
	}

	nextPoints, err := sk.rules.NextPoint(sk.match.CurrentGame, team)
	if err != nil {
		return fmt.Errorf("[IncrementScore] Error calculando siguiente punto: %w", err)
	}

	if team == 1 {
		sk.match.CurrentGame.Team1Score = nextPoints
	} else {
		sk.match.CurrentGame.Team2Score = nextPoints
	}

	newState, err := sk.stateMachine.Transition(sk.match, team)
	if err != nil {
		return fmt.Errorf("[IncrementScore] Error transicionando estado: %w", err)
	}

	if err := sk.handleStateChange(newState, team); err != nil {
		return fmt.Errorf("[IncrementScore] Error manejando cambio de estado: %w", err)
	}

	sk.notifyObservers()

	return nil
}

func (sk *ScoreKeeperImpl) handleStateChange(state GameState, team int) error {
	switch state {
	case GameOver:
		if err := sk.handleGameOver(team); err != nil {
			return err
		}
		// Despues de manejar un juego finalizado, checkeamos si el set termino
		if sk.rules.IsSetOver(sk.match.CurrentSet) {
			if err := sk.handleSetOver(team); err != nil {
				return err
			}
			// Si el set termino, checkeamos si la partida termino
			if sk.rules.IsMatchOver(sk.match) {
				return sk.handleMatchOver()
			}
		}
		return nil
	case SetOver:
		return sk.handleSetOver(team)
	case MatchOver:
		return sk.handleMatchOver()
	default:
		return nil
	}
}

func (sk *ScoreKeeperImpl) handleGameOver(team int) error {
	completedGame := *sk.match.CurrentGame
	sk.match.CurrentSet.Games = append(sk.match.CurrentSet.Games, completedGame)

	if team == 1 {
		sk.match.CurrentSet.Team1Games++
	} else {
		sk.match.CurrentSet.Team2Games++
	}

	nextGameIsTiebreak := sk.rules.ShouldStartTiebreak(sk.match.CurrentSet)

	sk.match.CurrentGame = &Game{
		IsTiebreak: nextGameIsTiebreak,
	}

	return nil
}

func (sk *ScoreKeeperImpl) handleSetOver(team int) error {
	completedSet := *sk.match.CurrentSet
	sk.match.Sets = append(sk.match.Sets, completedSet)

	if team == 1 {
		sk.match.Team1Sets++
	} else {
		sk.match.Team2Sets++
	}

	sk.match.CurrentSet = &Set{
		Games: make([]Game, 0),
	}
	sk.match.CurrentGame = &Game{}

	return nil
}

// TODO: Implement me
func (sk *ScoreKeeperImpl) handleMatchOver() error {
	return nil
}

func (sk *ScoreKeeperImpl) GetCurrentScore() *Match {
	sk.mu.RLock()
	defer sk.mu.RUnlock()
	return sk.match
}

func (sk *ScoreKeeperImpl) ResetGame() {
	sk.mu.Lock()
	defer sk.mu.Unlock()

	sk.match.CurrentGame = &Game{}
	sk.notifyObservers()
}

func (sk *ScoreKeeperImpl) ResetSet() {
	sk.mu.Lock()
	defer sk.mu.Unlock()

	sk.match.CurrentSet = &Set{
		Games: make([]Game, 0),
	}
	sk.match.CurrentGame = &Game{}
	sk.notifyObservers()
}

func (sk *ScoreKeeperImpl) ResetMatch() {
	sk.mu.Lock()
	defer sk.mu.Unlock()

	sk.match = &Match{
		Team1: sk.match.Team1,
		Team2: sk.match.Team2,
		CurrentSet: &Set{
			Games: make([]Game, 0),
		},
		CurrentGame: &Game{},
		Sets:        make([]Set, 0),
	}
	sk.notifyObservers()
}

// metodos patron observador
func (sk *ScoreKeeperImpl) AddObserver(observer func(*Match)) {
	sk.mu.Lock()
	defer sk.mu.Unlock()
	sk.observers = append(sk.observers, observer)
}

func (sk *ScoreKeeperImpl) RemoveObserver(observer func(*Match)) {
	sk.mu.Lock()
	defer sk.mu.Unlock()
	for i, obs := range sk.observers {
		if fmt.Sprintf("%p", obs) == fmt.Sprintf("%p", observer) {
			sk.observers = append(sk.observers[:i], sk.observers[i+1:]...)
			break
		}
	}
}

func (sk *ScoreKeeperImpl) notifyObservers() {
	for _, observer := range sk.observers {
		observer(sk.match)
	}
}
