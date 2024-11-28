package scoreboard

import "sync"

type Game struct {
	Team1Score int  `json:"team1Score"`
	Team2Score int  `json:"team2Score"`
	IsTiebreak bool `json:"isTiebreak"`
}

type Set struct {
	Games      []Game `json:"games"`
	Team1Games int    `json:"team1Games"`
	Team2Games int    `json:"team2Games"`
}

type Match struct {
	Sets        []Set  `json:"sets"`
	CurrentSet  *Set   `json:"currentSet"`
	CurrentGame *Game  `json:"currentGame"`
	Team1       string `json:"team1"`
	Team2       string `json:"team2"`
	Team1Sets   int    `json:"team1Sets"`
	Team2Sets   int    `json:"team2Sets"`
}

type Scoreboard struct {
	mu          sync.RWMutex
	scoreKeeper *ScoreKeeperImpl
	clients     map[chan *Match]struct{}
}

func NewScoreboard() *Scoreboard {
	return &Scoreboard{
		// Inicializamos con nombres de equipo vacio
		scoreKeeper: NewScoreKeeper("", ""),
		clients:     make(map[chan *Match]struct{}),
	}
}

// AddClient registra un nuevo cliente al servicio de Scoreboard
func (s *Scoreboard) AddClient(ch chan *Match) {
	s.mu.Lock()
	defer s.mu.Unlock()
	s.clients[ch] = struct{}{}

	// Inmediatamente enviamos datos para actualizar la UI del cliente
	go func() {
		ch <- s.scoreKeeper.GetCurrentScore()
	}()
}

// RemoveClient de-registra un cliente
func (s *Scoreboard) RemoveClient(ch chan *Match) {
	s.mu.Lock()
	defer s.mu.Unlock()
	delete(s.clients, ch)
	close(ch)
}

// broadcast envia el estado del partido a todos los clientes registrados
func (s *Scoreboard) broadcast(match *Match) {
	s.mu.RLock()
	defer s.mu.RUnlock()
	for ch := range s.clients {
		// Select dentro del range crea un loop non-blocking
		select {
		case ch <- match:
		}
	}
}

// IncrementScore aumenta la puntuacion para el equipo especificado
func (s *Scoreboard) IncrementScore(team int) error {
	err := s.scoreKeeper.IncrementScore(team)
	if err != nil {
		return err
	}

	// Enviamos la puntuacion actualizada a todos los clientes
	s.broadcast(s.scoreKeeper.GetCurrentScore())
	return nil
}

// SetNames actualiza los nombres de equipo
func (s *Scoreboard) SetNames(team1, team2 string) {
	s.mu.Lock()
	// Creamos un nuevo ScoreKeeper
	newKeeper := NewScoreKeeper(team1, team2)
	// Copiamos el partido actual al ScoreKeeper nuevo
	if s.scoreKeeper != nil {
		currentMatch := s.scoreKeeper.GetCurrentScore()
		if currentMatch != nil {
			newKeeper.match.CurrentSet = currentMatch.CurrentSet
			newKeeper.match.CurrentGame = currentMatch.CurrentGame
			newKeeper.match.Sets = currentMatch.Sets
			newKeeper.match.Team1Sets = currentMatch.Team1Sets
			newKeeper.match.Team2Sets = currentMatch.Team2Sets
		}
	}
	s.scoreKeeper = newKeeper
	s.mu.Unlock()

	// Actualizamos los clientes
	s.broadcast(s.scoreKeeper.GetCurrentScore())
}

func (s *Scoreboard) ResetGame() {
	s.scoreKeeper.ResetGame()
	s.broadcast(s.scoreKeeper.GetCurrentScore())
}

func (s *Scoreboard) ResetSet() {
	s.scoreKeeper.ResetSet()
	s.broadcast(s.scoreKeeper.GetCurrentScore())
}

func (s *Scoreboard) ResetMatch() {
	s.scoreKeeper.ResetMatch()
	s.broadcast(s.scoreKeeper.GetCurrentScore())
}

// Inicializamos un observador
func (s *Scoreboard) Initialize() {
	s.scoreKeeper.AddObserver(func(match *Match) {
		s.broadcast(match)
	})
}
