package main

import "log"

func main() {
	err := run()
	if err != nil {
		log.Fatalf("[Main] Error fatal al iniciar aplicacion: %s", err)
	}
}
