<?php

return [
	'name' => 'Ticket',
	'states' => [
		1 => 'New',
		2 => 'In Process',
		3 => 'Closed'
	],
	'types' => [
		1 => 'General',
		2 => 'Technical',
		3 => 'Accounting' 
	],
	'priorities' => [
		1 => 'Trivial', // allgemeine Fragen zur Benutzung eines Produktes, Anregungen für Produkterweiterungen oder -modifizierungen
		2 => 'Minor', // Störung mit teilweisem, nichtkritischen Verlust von Funktionalität
		3 => 'Major', // Störung mit erheblichem Ausmaß auf den Geschäftsbetrieb des Kunden -> Weiterarbeit möglich
		4 => 'Critical'	// Störung katastrophalen Ausmaßes mit kritischen Auswirkungen auf den Geschäftsbetrieb -> kein Weiterarbeiten
	] 
];