<?php
    //Randomize turn order then return the game state
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
   
    $playercount = count($game->getAllCurrentPlayers());
    
    if($playercount > 1) {
        $turnArray = array();
        
        for ($i = 0; $i < $playercount; $i++) {
            array_push($turnArray, $i*10);
        }
        //randomize!
        shuffle($turnArray);
        
        $index = 0;
        foreach ($game->getAllCurrentPlayers() as &$player) {
            $player->turnorder = $turnArray[$index];
            $player->update();
            $index++;
        }
        
    }
    $game->getAllCurrentPlayers();
    
    
    echo json_encode($game);

