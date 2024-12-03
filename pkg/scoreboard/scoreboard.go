package scoreboard

import (
	"sync"
)

type Game struct {
	Team1Score   int  `json:"team1Score"`
	Team2Score   int  `json:"team2Score"`
	IsTiebreak   bool `json:"isTiebreak"`
	Team1TBScore int  `json:"team1TBScore"`
	Team2TBScore int  `json:"team2TBScore"`
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
	// protege el mapa de clientes. keeper es seguro para concurrencia, ya que
	// tiene su propio mutex.
	mu          sync.RWMutex
	scoreKeeper *ScoreKeeper
	clients     map[chan *Match]struct{}
}

func NewScoreboard() *Scoreboard {
	return &Scoreboard{
		scoreKeeper: NewScoreKeeper("", ""),
		clients:     make(map[chan *Match]struct{}),
	}
}

// AddClient registra un nuevo cliente al servicio de Scoreboard
func (s *Scoreboard) AddClient(ch chan *Match) {
	s.mu.Lock()
	s.clients[ch] = struct{}{}

	// Inmediatamente enviamos datos para actualizar la UI del cliente
	go func() {
		ch <- s.scoreKeeper.GetCurrentScore()
	}()
	s.mu.Unlock()
}

// RemoveClient de-registra un cliente
func (s *Scoreboard) RemoveClient(ch chan *Match) {
	s.mu.Lock()
	delete(s.clients, ch)
	close(ch)
	s.mu.Unlock()
}

// broadcast envia el estado del partido a todos los clientes registrados
func (s *Scoreboard) broadcast(match *Match) {
	s.mu.Lock()
	for ch := range s.clients {
		// Select dentro del range crea un loop non-blocking
		select {
		case ch <- match:
		}
	}
	s.mu.Unlock()
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
	s.scoreKeeper.SetNames(team1, team2)
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

// TODO: Cambiar configuracion en medio de una partida no actualiza todos los datos
func (s *Scoreboard) ConfigureMatch(rules Rules) {
	s.scoreKeeper.rules.Configure(rules)
	s.broadcast(s.scoreKeeper.GetCurrentScore())
}

func (s *Scoreboard) Rules() Rules {
	return s.scoreKeeper.rules.Rules()
}

// Inicializamos un observador
func (s *Scoreboard) Initialize() {
	s.scoreKeeper.AddObserver(func(match *Match) {
		s.broadcast(match)
	})
}
