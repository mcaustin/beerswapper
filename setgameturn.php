<?php
    require_once 'Game.php';
    require_once 'Player.php';
    
    if (!isset($_GET['gamecode'])) {
        die("Invalid Game Code");
    }
    if (!isset($_GET['turn'])) {
        die("turn is required");
    }
    $gamecode = $_GET['gamecode'];
    $gameturn = filter_var($_GET['turn'], FILTER_SANITIZE_NUMBER_INT);
    
    $game = Game::findByCode($gamecode);
    $game->turn = $gameturn;
    $game->update();
    
    if (is_null($game)) {
        die("No game found");
    }
    
    $game->getAllCurrentPlayers();
    
    echo json_encode($game);

