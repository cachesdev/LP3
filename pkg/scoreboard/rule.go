package scoreboard

import (
	"fmt"

	"github.com/k0kubun/pp/v3"
)

type Rules struct {
	SetsToWin     int
	GamesPerSet   int
	PointsPerGame int
	UseTiebreak   bool
}

type PadelRulesEngine struct {
	rules Rules
}

func NewPadelRulesEngine() *PadelRulesEngine {
	return &PadelRulesEngine{
		rules: Rules{
			SetsToWin:     2,
			GamesPerSet:   6,
			PointsPerGame: 40,
			UseTiebreak:   true,
		},
	}
}

func (r *PadelRulesEngine) IsValidPoint(match *Match, team int) bool {
	if match == nil || match.CurrentGame == nil {
		return false
	}

	pp.Println("Antes MatchOver")
	// Can't score if match is over
	if r.IsMatchOver(match) {
		return false
	}

	pp.Println("Antes SetOver")
	// Can't score if set is over
	if r.IsSetOver(match.CurrentSet) {
		return false
	}

	pp.Println("Antes GameOver")
	// Can't score if game is over
	if r.IsGameOver(match.CurrentGame) {
		return false
	}

	return true
}

func (r *PadelRulesEngine) NextPoint(game *Game, team int) (int, error) {
	if game == nil {
		return 0, fmt.Errorf("game is nil")
	}

	currentPoints := game.Team1Score
	if team == 2 {
		currentPoints = game.Team2Score
	}

	if game.IsTiebreak {
		return currentPoints + 1, nil // In tiebreak, points increment normally
	}

	// Regular game scoring
	switch currentPoints {
	case 0:
		return 15, nil
	case 15:
		return 30, nil
	case 30:
		return 40, nil
	case 40:
		otherPoints := game.Team2Score
		if team == 2 {
			otherPoints = game.Team1Score
		}
		if otherPoints == 40 {
			return 41, nil // Advantage
		}
		return 50, nil // Game won
	case 41: // Has advantage
		return 50, nil // Game won
	default:
		return 0, fmt.Errorf("invalid point state")
	}
}

func (r *PadelRulesEngine) IsGameOver(game *Game) bool {
	if game == nil {
		return false
	}

	if game.IsTiebreak {
		return r.isTiebreakOver(game)
	}

	return game.Team1Score >= 50 || game.Team2Score >= 50
}

func (r *PadelRulesEngine) isTiebreakOver(game *Game) bool {
	// Must reach at least 7 points and win by 2
	if game.Team1Score >= 7 || game.Team2Score >= 7 {
		return abs(game.Team1Score-game.Team2Score) >= 2
	}
	return false
}

func (r *PadelRulesEngine) IsSetOver(set *Set) bool {
	if set == nil {
		return false
	}

	// Regular set win (6 games with 2 game difference)
	if (set.Team1Games >= r.rules.GamesPerSet ||
		set.Team2Games >= r.rules.GamesPerSet) &&
		abs(set.Team1Games-set.Team2Games) >= 2 {
		pp.Println("Returning true...")
		return true
	}

	// Tiebreak situation
	if r.rules.UseTiebreak &&
		set.Team1Games == r.rules.GamesPerSet &&
		set.Team2Games == r.rules.GamesPerSet {
		if set.Games == nil || len(set.Games) == 0 {
			return false
		}
		return r.IsGameOver(&set.Games[len(set.Games)-1])
	}

	// Extended set (7-5)
	if (set.Team1Games == 7 || set.Team2Games == 7) &&
		abs(set.Team1Games-set.Team2Games) >= 2 {
		return true
	}

	return false
}

func (r *PadelRulesEngine) IsMatchOver(match *Match) bool {
	if match == nil {
		return false
	}

	return match.Team1Sets >= r.rules.SetsToWin ||
		match.Team2Sets >= r.rules.SetsToWin
}

func (r *PadelRulesEngine) ShouldStartTiebreak(set *Set) bool {
	if set == nil {
		return false
	}

	return r.rules.UseTiebreak &&
		set.Team1Games == r.rules.GamesPerSet &&
		set.Team2Games == r.rules.GamesPerSet
}

// Helper function
func abs(n int) int {
	if n < 0 {
		return -n
	}
	return n
}
