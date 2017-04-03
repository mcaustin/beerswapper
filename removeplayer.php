<?php
    /**
     * Remove a player from the swap, return the game state
     */
    require_once 'Game.php';
    require_once 'Player.php';

    if (!isset($_GET['gamecode'])) {
        die("Invalid Game Code");
    }
    if (!isset($_GET['playerid'])) {
        die("Invalid Player Name");
    }

    $gamecode = $_GET['gamecode'];
    $playerId = $_GET['playerid'];
    
    $game = Game::findByCode($gamecode);    
    if (is_null($game)) {
        die("No game found");
    }

    $player = Player::findById($playerId);
    if (is_null($player)) {
        die("No player found");
    }
    
    $player->delete();
    
    $game->getAllCurrentPlayers();
   
    echo json_encode($game);
