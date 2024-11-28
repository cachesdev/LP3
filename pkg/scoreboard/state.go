package scoreboard

import "fmt"

type GameState int

const (
	RegularPlay GameState = iota
	GamePoint
	Deuce
	Advantage
	TiebreakPlay
	GameOver
	SetOver
	MatchOver
)

func (s GameState) String() string {
	return [...]string{
		"RegularPlay",
		"GamePoint",
		"Deuce",
		"Advantage",
		"TiebreakPlay",
		"GameOver",
		"SetOver",
		"MatchOver",
	}[s]
}

type PadelStateMachine struct {
	currentState GameState
	rules        *PadelRulesEngine
}

func NewPadelStateMachine(rules *PadelRulesEngine) *PadelStateMachine {
	return &PadelStateMachine{
		currentState: RegularPlay,
		rules:        rules,
	}
}

func (sm *PadelStateMachine) GetCurrentState() GameState {
	return sm.currentState
}

func (sm *PadelStateMachine) Transition(match *Match, team int) (GameState, error) {
	if match == nil || match.CurrentGame == nil {
		return sm.currentState, fmt.Errorf("invalid match state")
	}

	nextState := sm.determineNextState(match, team)

	if !sm.IsValidTransition(sm.currentState, nextState) {
		return sm.currentState, fmt.Errorf("invalid state transition from %s to %s",
			sm.currentState, nextState)
	}

	sm.currentState = nextState
	return nextState, nil
}

func (sm *PadelStateMachine) determineNextState(match *Match, team int) GameState {
	// Check match over first
	if sm.rules.IsMatchOver(match) {
		return MatchOver
	}

	// Check set over
	if sm.rules.IsSetOver(match.CurrentSet) {
		return SetOver
	}

	// Check game over
	if sm.rules.IsGameOver(match.CurrentGame) {
		return GameOver
	}

	// Handle tiebreak
	if match.CurrentGame.IsTiebreak {
		return TiebreakPlay
	}

	// Regular game state determination
	game := match.CurrentGame
	teamScore := game.Team1Score
	otherScore := game.Team2Score
	if team == 2 {
		teamScore = game.Team2Score
		otherScore = game.Team1Score
	}

	switch {
	case teamScore == 40 && otherScore == 40:
		return Deuce
	case teamScore == 41 || otherScore == 41:
		return Advantage
	case teamScore == 40 || otherScore == 40:
		return GamePoint
	default:
		return RegularPlay
	}
}

func (sm *PadelStateMachine) IsValidTransition(from, to GameState) bool {
	// Define valid transitions
	transitions := map[GameState][]GameState{
		RegularPlay: {
			GamePoint,
			Deuce,
			TiebreakPlay,
		},
		GamePoint: {
			GameOver,
			Deuce,
			RegularPlay,
		},
		Deuce: {
			Advantage,
			RegularPlay,
		},
		Advantage: {
			GameOver,
			Deuce,
		},
		TiebreakPlay: {
			GameOver,
		},
		GameOver: {
			RegularPlay,
			SetOver,
		},
		SetOver: {
			RegularPlay,
			MatchOver,
		},
		MatchOver: {}, // No valid transitions from match over
	}

	// Check if the transition is valid
	validStates, exists := transitions[from]
	if !exists {
		return false
	}

	// Special case: same state is always valid
	if from == to {
		return true
	}

	// Check if the transition is in the valid states list
	for _, validState := range validStates {
		if to == validState {
			return true
		}
	}

	return false
}

// Helper method to determine if we're at a critical point
func (sm *PadelStateMachine) IsCriticalPoint(match *Match) bool {
	switch sm.currentState {
	case GamePoint, Deuce, Advantage:
		return true
	case TiebreakPlay:
		// Critical point in tiebreak when either player is one point from winning
		game := match.CurrentGame
		return (game.Team1Score >= 6 || game.Team2Score >= 6) &&
			abs(game.Team1Score-game.Team2Score) >= 1
	default:
		return false
	}
}
