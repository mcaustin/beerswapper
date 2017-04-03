<?php
/**
 * Description of Player
 *
 * @author mcaustin
 */
class Player {
    public $id;
    public $name;
    public $image;
    public $turnorder;
    public $game_id;
    
    function __construct($p_id, $p_name, $p_image, $p_turnorder, $p_game_id) {
        $this->id = $p_id;
        $this->name = $p_name;
        $this->image = $p_image;
        $this->turnorder = $p_turnorder;
        $this->game_id = $p_game_id;
    }
    
    public function update() {
        include 'dao.php';
        
        $sql = "UPDATE `player` SET `name` = ?, "
                . "`image` = ?, "
                . "`turnorder` = ?"
                . " WHERE `id` = ?";
        
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("ssii", $this->name, $this->image, $this->turnorder, $this->id);
            
            if (!$stmt->execute()) {
                echo "Unable to update player: (" . $stmt->errno . ") " . $stmt->error;
            }

            $stmt->close();
        } else {
            echo "Unable to update player: (" . $conn->errno . ") " . $conn->error;
        }
        $conn->close();
        return true;
    }
    
    public function createNewPlayer($game_id, $p_name, $p_turn_order = 0, $p_image = "") {
        include 'dao.php';
        //Create new Player
        $sql = "INSERT INTO player (`name`, `image`, `turnorder`, `game_id`) VALUES (?, ?, ?, ?)";
  
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("ssii", $p_name, $p_image, $p_turn_order, $game_id);
            
            if (!$stmt->execute()) {
                echo "Unable to create player: (" . $stmt->errno . ") " . $stmt->error;
            }
            $newId = $conn->insert_id;
            $stmt->close();
            $conn->close();
            return new Player($newId, $p_name, $p_image, $p_turn_order, $game_id);
        }
        $conn->close();
        return null;
    }
    
    public function delete() {
        include 'dao.php';
        $sql = "DELETE FROM player "
                . "WHERE `game_id` = ? and name = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("ss", $this->game_id, $this->name);
            
            if (!$stmt->execute()) {
                $conn->close();
                die("Unable to delete player");
            }
            $stmt->close();
        } else {
            echo "Unable to delete player: (" . $stmt->errno . ") " . $stmt->error;
        }
        $conn->close();
        return true;
    }
    
    public function findById($player_id) {
        include 'dao.php';
        $sql = "SELECT `id`, `name`, `image`, `turnorder`, `game_id` FROM `player` "
                . "WHERE `id` = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("i", $player_id);
            if (!$stmt->execute()) {
                echo "Unable to query player: (" . $stmt->errno . ") " . $stmt->error;
            }

            $stmt->bind_result($playerid, $playerName, $playerimage, $playerturnorder, $gameid);
            if ($stmt->fetch()) {
                $conn->close();
                return new Player($playerid, $playerName, $playerimage, $playerturnorder, $gameid);
            }

            $stmt->close();
        } else {
            echo "Player search failed: (" . $conn->errno . ") " . $conn->error;
        }
        $conn->close();
        return null;
    }
    
    public function findByGameAndName($game_id, $p_name) {
        include 'dao.php';
        //Now See if the Player Already exists
        $sql = "SELECT id, name, image, turnorder, game_id FROM player "
                . "WHERE `game_id` = ? and name = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("is", $game_id, $p_name);
            
            if (!$stmt->execute()) {
                die("Unable to query player");
            }

            $stmt->bind_result($playerid, $playerName, $playerimage, $playerturnorder, $gameid);
            if ($stmt->fetch()) {
                $conn->close();
                return new Player($playerid, $playerName, $playerimage, $playerturnorder, $gameid);
            }

            $stmt->close();
        } else {
            echo "Player search failed: (" . $conn->errno . ") " . $conn->error;
        }
        $conn->close();
        return null;
    }
}
