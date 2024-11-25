package scoreboard

type GameState string

const (
	MatchOver     GameState = "MATCH_OVER"
	GameOver      GameState = "GAME_OVER"
	SetPoint      GameState = "SET_POINT"
	SetPointDeuce GameState = "SET_POINT_DEUCE"
	Deuce         GameState = "DEUCE"
	GamePoint     GameState = "GAME_POINT"
	RegularPlay   GameState = "REGULAR_PLAY"
)

type ScoreTransition struct {
	Team     int
	Points   int
	NewState GameState
	IsValid  bool
	SetWon   bool
	GameWon  bool
}

type ScoreMachine struct {
	CurrentState GameState
}

func (sm *ScoreMachine) ValidateAndTransition(match Match, team int) ScoreTransition {
	switch sm.CurrentState {
	case RegularPlay:
		return sm.handleRegularPlay(currentScore, team)
	case Deuce:
		return sm.handleDeuce(currentScore, team)
	case GamePoint:
		return sm.handleGamePoint(currentScore, team)
	case SetPoint:
		return sm.handleSetPoint(currentScore, team)
	case SetPointDeuce:
	case GameOver:
	case MatchOver:
		return ScoreTransition{IsValid: false}
	default:
		return ScoreTransition{IsValid: false}
	}
}

func (sm *ScoreMachine) handleRegularPlay(score Score, team int) ScoreTransition {
	var teamScore int
	if team == 1 {
		teamScore = score.Team1
	} else {
		teamScore = score.Team2
	}

	// Regular scoring until 40
	switch teamScore {
	case 0:
		return ScoreTransition{Team: team, Points: 15, NewState: RegularPlay, IsValid: true}
	case 15:
		return ScoreTransition{Team: team, Points: 30, NewState: RegularPlay, IsValid: true}
	case 30:
		return ScoreTransition{Team: team, Points: 40, NewState: RegularPlay, IsValid: true}
	case 40:
		otherTeamScore := score.Team2
		if team == 2 {
			otherTeamScore = score.Team1
		}

		if otherTeamScore < 40 {
			return ScoreTransition{Team: team, Points: 0, NewState: MatchOver, IsValid: true, SetWon: true}
		} else if otherTeamScore == 40 {
			return ScoreTransition{Team: team, Points: 41, NewState: Deuce, IsValid: true}
		}
	}

	return ScoreTransition{IsValid: false}
}

func (sm *ScoreMachine) handleDeuce(score Score, team int) ScoreTransition {
	var teamScore, otherTeamScore int
	if team == 1 {
		teamScore = score.Team1
		otherTeamScore = score.Team2
	} else {
		teamScore = score.Team2
		otherTeamScore = score.Team1
	}

	if teamScore == 40 && otherTeamScore == 41 {
		return ScoreTransition{Team: team, Points: 40, NewState: Deuce, IsValid: true}
	} else if teamScore == 40 {
		return ScoreTransition{Team: team, Points: 41, NewState: SetPoint, IsValid: true}
	}

	return ScoreTransition{IsValid: false}
}

func (sm *ScoreMachine) handleSetPoint(score Score, team int) ScoreTransition {
	var teamScore, otherTeamScore int
	if team == 1 {
		teamScore = score.Team1
		otherTeamScore = score.Team2
	} else {
		teamScore = score.Team2
		otherTeamScore = score.Team1
	}

	if teamScore == 41 {
		return ScoreTransition{Team: team, Points: 0, NewState: MatchOver, IsValid: true, SetWon: true}
	} else if otherTeamScore == 41 {
		return ScoreTransition{Team: team, Points: 40, NewState: Deuce, IsValid: true}
	}

	return ScoreTransition{IsValid: false}
}
