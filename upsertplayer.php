<?php
    //Add or Update a player in the game
    require_once 'Game.php';
    require_once 'Player.php';
    
    $postdata = file_get_contents("php://input");
    $request = json_decode($postdata);
    $playerimage = "";
    $turnorder = 0;
    if (isset($request->gamecode)) {
        $gamecode = filter_var($request->gamecode, FILTER_SANITIZE_STRING);
    } else {
        die("Game code required");
    }
    if (isset($request->playername)) {
        $playername = filter_var($request->playername, FILTER_SANITIZE_STRING);
    } else {
        die("Player Name required");
    }
    if (isset($request->playerimage)) {
        $playerimage = filter_var($request->playerimage, FILTER_SANITIZE_URL);
    }
    if (isset($request->turnorder)) {
        $turnorder = filter_var($request->turnorder, FILTER_SANITIZE_NUMBER_INT);
    }

    $game = Game::findByCode($gamecode);
    if (is_null($game)) {
        die("Unable to Find Game");
    }
    
    $player = Player::findByGameAndName($game->id, $playername);
    if (is_null($player)) {
        $player = Player::createNewPlayer($game->id, $playername, $turnorder, $playerimage);
        if (is_null($player)) {
            die("Unable to find or create player");
        }
    } else {
        //Update player
        if (!is_null($playername)) {
            $player->name = $playername;
        }
        if (!is_null($playerimage) && $game->turn == 0) {
            $player->image = $playerimage;
        }
        if (!is_null($turnorder)) {
            $player->turnorder = $turnorder;
        }
        $player->update();
    }
    
    $game->getAllCurrentPlayers();
    
    $jsonResponse = array();
    array_push($jsonResponse, $game);
    array_push($jsonResponse, $player);
    
    echo json_encode($jsonResponse);
