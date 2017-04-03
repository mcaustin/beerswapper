<?php
    /**
     * Returns the current state of the game and all players
     */
    require_once 'Game.php';
    require_once 'Player.php';

    if (!isset($_GET['gamecode'])) {
        die("Invalid Game Code");
    }
    $gamecode = $_GET['gamecode'];
    
    $game = Game::findByCode($gamecode);
    
    if (is_null($game)) {
        die("No game found");
    }
    
    $game->getAllCurrentPlayers();
   
    echo json_encode($game);
    

