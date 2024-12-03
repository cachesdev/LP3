package scoreboard

import "fmt"

type GameState int

const (
	RegularPlay GameState = iota
	GamePoint
	Deuce
	Advantage
	TiebreakGame
	TiebreakSet
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
		"TiebreakGame",
		"TebreakSet",
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
	nextState := sm.determineNextState(match, team)

	if !sm.IsValidTransition(sm.currentState, nextState) {
		return sm.currentState, fmt.Errorf("[Transition] transicion de estado invalido, %s a %s",
			sm.currentState, nextState)
	}

	sm.currentState = nextState
	return nextState, nil
}

func (sm *PadelStateMachine) determineNextState(match *Match, team int) GameState {
	if sm.rules.IsMatchOver(match) {
		return MatchOver
	}

	if sm.rules.IsSetOver(match.CurrentSet) {
		return SetOver
	}

	if sm.rules.IsGameOver(match.CurrentGame) {
		return GameOver
	}

	// Handle tiebreak
	if match.CurrentGame.IsTiebreak {
		return TiebreakGame
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
		TiebreakGame: {
			GameOver,
		},
		GameOver: {
			RegularPlay,
			TiebreakGame,
			SetOver,
		},
		SetOver: {
			RegularPlay,
			MatchOver,
		},
		MatchOver: {},
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
	case TiebreakGame:
		// Critical point in tiebreak when either player is one point from winning
		game := match.CurrentGame
		return (game.Team1TBScore >= 7 || game.Team2TBScore >= 7) &&
			abs(game.Team1TBScore-game.Team2TBScore) >= 2
	default:
		return false
	}
}
