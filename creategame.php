<?php
    require_once 'Game.php';

    $game = Game::createNewGame();
    
    echo json_encode($game);
?> 