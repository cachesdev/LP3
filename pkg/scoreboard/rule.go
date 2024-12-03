package scoreboard

type Rules struct {
	SetsToWin   int `json:"setsToWin"`
	GamesPerSet int `json:"gamesPerSet"`
}

type PadelRulesEngine struct {
	rules Rules
}

func NewPadelRulesEngine() *PadelRulesEngine {
	return &PadelRulesEngine{
		rules: Rules{
			SetsToWin:   2,
			GamesPerSet: 6,
		},
	}
}

func (r *PadelRulesEngine) Rules() Rules {
	return r.rules
}

func (r *PadelRulesEngine) Configure(rules Rules) {
	r.rules = rules
}

func (r *PadelRulesEngine) IsValidPoint(match *Match, team int) bool {
	if r.IsMatchOver(match) {
		return false
	}

	if r.IsSetOver(match.CurrentSet) {
		return false
	}

	if r.IsGameOver(match.CurrentGame) {
		return false
	}

	return true
}

// NextPoint se encarga de transicionar los puntos en un Game. en el caso de un
// tiebreak, el conteo de puntos pasa del conteo tradicional (0 15 30 40) a un
// conteo n + 1 (1 2 3 4 ... etc)
func (r *PadelRulesEngine) NextPoint(game *Game, team int) int {
	var currentPoints int
	var otherPoints int

	switch team {
	case 1:
		currentPoints = game.Team1Score
	case 2:
		currentPoints = game.Team2Score
	}

	if game.IsTiebreak {
		switch team {
		case 1:
			currentPoints = game.Team1Score
		case 2:
			currentPoints = game.Team2Score
		}

		return currentPoints + 1
	}

	switch currentPoints {
	case 0:
		return 15
	case 15:
		return 30
	case 30:
		return 40
	case 40:
		switch team {
		case 1:
			otherPoints = game.Team2Score
		case 2:
			otherPoints = game.Team1Score
		}

		if otherPoints == 40 {
			return 41 // Punto de Oro, GameOver
		}
	}

	return -1
}

func (r *PadelRulesEngine) IsGameOver(game *Game) bool {
	if game.IsTiebreak {
		return r.isTiebreakOver(game)
	}

	return game.Team1Score >= 41 || game.Team2Score >= 41
}

func (r *PadelRulesEngine) isTiebreakOver(game *Game) bool {
	// Debe de alcanzar como minimo 7 puntos con diferencia de 2
	if game.Team1TBScore >= 7 || game.Team2TBScore >= 7 {
		return abs(game.Team1TBScore-game.Team2TBScore) >= 2
	}
	return false
}

func (r *PadelRulesEngine) IsSetOver(set *Set) bool {
	game, ok := find(set.Games, func(e Game) bool { return e.IsTiebreak })
	if ok {
		return r.isTiebreakOver(&game)
	}

	// Regular set win (6 games with 2 game difference)
	if (set.Team1Games >= r.rules.GamesPerSet ||
		set.Team2Games >= r.rules.GamesPerSet) &&
		abs(set.Team1Games-set.Team2Games) >= 2 {
		return true
	}

	// Tiebreak situation
	if set.Team1Games == r.rules.GamesPerSet &&
		set.Team2Games == r.rules.GamesPerSet {
		return false
	}

	// Extended set (7-5)
	if (set.Team1Games == 7 || set.Team2Games == 7) &&
		abs(set.Team1Games-set.Team2Games) >= 2 {
		return true
	}

	return false
}

func (r *PadelRulesEngine) IsMatchOver(match *Match) bool {
	return match.Team1Sets >= r.rules.SetsToWin ||
		match.Team2Sets >= r.rules.SetsToWin
}

func (r *PadelRulesEngine) ShouldStartTiebreak(set *Set) bool {
	return set.Team1Games == r.rules.GamesPerSet &&
		set.Team2Games == r.rules.GamesPerSet
}

// abs es similar a `math.Abs`, pero solo para integer.
func abs(n int) int {
	if n < 0 {
		return -n
	}
	return n
}

// find encuentra el primer elemento en el cual el callback retorne verdadero.
func find[S ~[]E, E any](s S, f func(e E) bool) (E, bool) {
	var cond bool
	var ret E
	for _, v := range s {
		cond = f(v)
		if cond {
			return v, true
		}
	}

	return ret, false
}
