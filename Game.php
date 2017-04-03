<?php

/**
 * Description of Game
 *
 * @author mcaustin
 */
class Game {   
    public $id;
    public $turn;
    public $active;
    public $code;
    public $currentPlayers;
    
    public $currentPlayer;
    
    function __construct($game_id, $game_turn, $game_active, $game_code) {          
        settype($turn, "int");
        
        $this->id = $game_id;
        $this->turn = $game_turn;
        $this->active = $game_active;
        $this->code = $game_code;
        $this->currentPlayers = array();
    }
    
    private static function comp(Player $playera, Player $playerb) {
        if ($playera->turnorder > $playerb->turnorder) return 1;
        if ($playera->turnorder < $playerb->turnorder) return -1;
        return 0;
    }
    
    public function update() {
        include 'dao.php';
        
        $sql = "UPDATE `game` SET `turn` = ?, "
                . "`active` = ? "
                . " WHERE `id` = ?";
        
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("iii", $this->turn, $this->active, $this->id);
            
            if (!$stmt->execute()) {
                echo "Unable to update game: (" . $stmt->errno . ") " . $stmt->error;
            }

            $stmt->close();
        } else {
            echo "Unable to update game: (" . $conn->errno . ") " . $conn->error;
        }
        $conn->close();
        return true;
    }
    
    public function getAllCurrentPlayers() {
        include 'dao.php';
        $this->currentPlayers = array();
        $sql = "SELECT id, name, image, turnorder, game_id FROM player WHERE `game_id` = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("s", $this->id);
            $stmt->execute();

            $stmt->bind_result($p_id, $p_name, $p_image, $p_turnorder, $p_game_id);
            
            while($stmt->fetch()) {
                array_push($this->currentPlayers, new Player($p_id, $p_name, $p_image, $p_turnorder, $p_game_id));
            }
            $stmt->close();
            
            usort($this->currentPlayers, "Game::comp");
            $conn->close();

            return $this->currentPlayers;
        } else {
            $conn->close();
            die("Unable to Find Game");
        }  
    }
    
    public function createNewGame() {
        include_once 'utilities.php';
        include_once 'dao.php';

        $usedCodes = array();
        $sql = "SELECT code FROM game WHERE active = ?";
        $active = 1;
        $turn = 0;
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("i", $active);
            $stmt->execute();

            $stmt->bind_result($activeCode);
            while($stmt->fetch()) {
                array_push($usedCodes, $activeCode);
            }
            $stmt->close();
            
        } else {
            echo "Unable to update player: (" . $conn->errno . ") " . $conn->error;
        } 
         
        $newcode = random_str(4);
        while (in_array($newcode, $usedCodes)) {
            $newcode = random_str(4);
        }
    
        $sql = "INSERT INTO game (`turn`, `active`, `code`) VALUES (?, ?, ?)";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("iis", $turn, $active, $newcode);
            
            if ($stmt->execute()) {
                $gameId = $conn->insert_id;
                $conn->close();
                return new Game($gameId, $turn, $active, $newcode);
            }
        }
   
        $conn->close();
        return null;
    }
    
    public function findByCode($gamecode) {
        include 'dao.php';
        $sql = "SELECT `id`, `turn`, `active`, `code` FROM game WHERE `code` = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("s", $gamecode);
            $stmt->execute();

            $stmt->bind_result($gid, $gturn, $gactive, $gcode);
            $stmt->fetch();
            //Now use variable $column_1 one as if it were any other PHP variable
            $stmt->close();
            
            $newGame = new Game($gid, $gturn, $gactive, $gcode);
            $conn->close();

            return $newGame;
        } else {
            $conn->close();

            die("Unable to Find Game");
        }
        $conn->close();

        return null;
    }
     
    
}
