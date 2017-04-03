<?php
    require_once 'Game.php';
    require_once 'Player.php';
    /* 
     * To change this license header, choose License Headers in Project Properties.
     * To change this template file, choose Tools | Templates
     * and open the template in the editor.
     */
    if (!isset($_GET['gamecode'])) {
        die("Invalid Game Code");
    }
    if (!isset($_GET['playername'])) {
        die("Invalid Player Name");
    }

    $gamecode = strtoupper($_GET['gamecode']);
    $playerName = $_GET['playername'];

    $game = Game::findByCode($gamecode);
    if (is_null($game)) {
        die("Unable to Find Game");
    }
    if ($game->turn > 0) {
        die("Game already started");
    }
       
    $player = Player::findByGameAndName($game->id, $playerName);
    if (is_null($player)) {
        $player = Player::createNewPlayer($game->id, $playerName);
        if (is_null($player)) {
            die("Unable to find or create player");
        }
    }
    
    $game->getAllCurrentPlayers();
    
    $jsonResponse = array();
    array_push($jsonResponse, $game);
    array_push($jsonResponse, $player);
    
    echo json_encode($jsonResponse);

?> 
    

