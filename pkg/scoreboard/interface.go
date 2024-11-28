package scoreboard

type RulesEngine interface {
	IsValidPoint(match *Match, team int) bool
	IsGameOver(game *Game) bool
	IsSetOver(set *Set) bool
	IsMatchOver(match *Match) bool
	NextPoint(game *Game, team int) (int, error)
}

type StateMachine interface {
	GetCurrentState() GameState
	Transition(match *Match, team int) (GameState, error)
	IsValidTransition(from GameState, to GameState) bool
}

type ScoreKeeper interface {
	IncrementScore(team int) error
	GetCurrentScore() *Match
	ResetGame()
	ResetSet()
	ResetMatch()
}
