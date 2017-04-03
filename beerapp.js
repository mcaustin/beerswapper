var app = angular.module('angularApp', []);

app.directive('capitalize', function() {
    return {
      require: 'ngModel',
      link: function(scope, element, attrs, modelCtrl) {
        var capitalize = function(inputValue) {
          if (inputValue == undefined) inputValue = '';
          var capitalized = inputValue.toUpperCase();
          if (capitalized !== inputValue) {
            modelCtrl.$setViewValue(capitalized);
            modelCtrl.$render();
          }
          return capitalized;
        }
        modelCtrl.$parsers.push(capitalize);
        capitalize(scope[attrs.ngModel]); // capitalize initial value
      }
    };
  });

app.controller('lineUpController', function($scope, $http, $interval) {

    $scope.gameMode = 0; //1 = Host 2 = Participant
    $scope.playerName = '';
    
    $scope.player;
    $scope.game;
    $scope.codeInput;
    
    $scope.counter =0;
    $scope.newName = "";
    $scope.newImage = "";
    $scope.newTurnOrder=0;
    
    $scope.roundLength = 6;
    $scope.defaultImage = 'toast.png';
    
    $scope.previousImage;
    $scope.currentImage;
    $scope.nextImage;
    
    $scope.previousName;
    $scope.currentName;
    $scope.nextName;
    
    $scope.superPreviousName = "N/A";
    $scope.superNextName= "N/A";
    
    $scope.addStockImage = function(name, url) {
        var image = {"name": name, "url": url};
        $scope.stockimages.push(image)
    }
    
    $scope.stockimages = [];
    $scope.addStockImage("Bee", "http://img.clipartall.com/bee-clipart-images-clip-art-bee.jpg");
    $scope.addStockImage("Super", "http://img.clipartall.com/superman-logo-clip-art-superman-img03.png");
    $scope.addStockImage("Beer", "beer.jpg");
    $scope.addStockImage("Steve Fredette", "https://pos.toasttab.com/hubfs/new_headshots/august_2016_-_white_background/steve-fredette-white-background.jpg");
    $scope.addStockImage("Aman Narang", "https://pos.toasttab.com/hubfs/new_headshots/toast_20160602_0123.jpg");
    $scope.addStockImage("Chris Comparato", "https://pos.toasttab.com/hubfs/new_headshots/toast_20160602_0116.jpg");
    $scope.addStockImage("Jon Grimm", "https://pos.toasttab.com/hubfs/new_headshots/august_2016_-_white_background/jon-grimm-white-background.jpg");
    $scope.addStockImage("Tim Barash", "https://pos.toasttab.com/hubfs/new_headshots/august_2016_-_white_background/tim-barash-white-background.jpg");
    $scope.addStockImage("Yi Chen", "https://pos.toasttab.com/hubfs/new_headshots/toast_20160602_0292.jpg");
    $scope.addStockImage("Hugh Scandrett", "https://pos.toasttab.com/hubfs/hugh-scandrett-424179-edited.jpg");
    $scope.addStockImage("Rick Lamy", "https://pos.toasttab.com/hubfs/images/headshots/Rick-Lamy.jpg");
    $scope.addStockImage("Barry Hartunian", "https://pos.toasttab.com/hubfs/new_headshots/barry-hr.jpg");
    $scope.addStockImage("John Morrison ", "https://pos.toasttab.com/hubfs/new_headshots/john-morrison.jpg");
    $scope.addStockImage("Debra Fleig", "https://pos.toasttab.com/hubfs/new_headshots/toast_20160602_0307.jpg");
    $scope.addStockImage("Brian Elworthy", "https://pos.toasttab.com/hubfs/new_headshots/brian-elworthy.jpg");
    $scope.addStockImage("Mary Casinelli", "https://pos.toasttab.com/hubfs/new_headshots/Mary.jpg");
    $scope.addStockImage("Ellery Fink", "https://pos.toasttab.com/hubfs/new_headshots/august_2016_-_white_background/ellery_fink.jpg");
    $scope.addStockImage("Elizabeth Jalbert", "https://pos.toasttab.com/hubfs/new_headshots/e_jalbert_headshot_small.jpg");
    $scope.addStockImage("Brook Stevens", "https://pos.toasttab.com/hubfs/new_headshots/toast_20160602_0050-1.jpg");
    $scope.addStockImage("Emil Sit", "https://pos.toasttab.com/hubfs/new_headshots/toast_20160602_0276.jpg");
  
    $scope.switchToPlayMode = function() {
        $scope.gameMode = 3;
        $scope.updatePortraits();
    }

    $scope.updateTurn = function(delta) {
        var newTurnNumber = parseInt($scope.game.turn);
        newTurnNumber = newTurnNumber + delta;
        if (newTurnNumber < 1) {
            newTurnNumber = 0;
        }
        if (newTurnNumber >= $scope.game.currentPlayers.length * $scope.roundLength) {
            newTurnNumber = $scope.game.currentPlayers.length * $scope.roundLength;
        }
        
        $http({
            method: 'GET',
            url: '/setgameturn.php',
            params: {'gamecode': $scope.game.code,
                     'turn': newTurnNumber}
          }).then(function successCallback(response) {
              // this callback will be called asynchronously
              // when the response is available
              console.log(response);
              $scope.game = response.data;
              $scope.updatePortraits();
            }, function errorCallback(response) {
              // called asynchronously if an error occurs
              // or server returns response with an error status.
              console.log(response);
            });
    }

    $scope.randomizeTurnOrder = function() {
        $http({
            method: 'GET',
            url: '/randomizeorder.php',
            params: {'gamecode': $scope.game.code}
          }).then(function successCallback(response) {
              // this callback will be called asynchronously
              // when the response is available
              console.log(response);
              $scope.game = response.data;
           
            }, function errorCallback(response) {
              // called asynchronously if an error occurs
              // or server returns response with an error status.
              console.log(response);
            });
    }
    
    $scope.getTrueIndex = function(turn) {
        var currentTurn = turn;
        
        if (currentTurn < 0) return -1;
        if (currentTurn > $scope.game.currentPlayers.length*$scope.roundLength) return -1;
        
        var length = $scope.game.currentPlayers.length;
        //Snake draft
        var div = currentTurn/length>>0;
        var mod = currentTurn % length;
        var trueIndex;
        if (div % 2 == 0) {
            trueIndex = mod;
        } else {
            trueIndex = length - 1 - mod;
        }
        return trueIndex;
    }
    
    $scope.updatePortraits = function() {
        if ($scope.game.currentPlayers.size < 1) return;
        
        var currentTurn = parseInt($scope.game.turn);
        var superPreviousIndex = $scope.getTrueIndex(currentTurn-2);
        var previousIndex = $scope.getTrueIndex(currentTurn-1);
        var currentIndex = $scope.getTrueIndex(currentTurn);
        var nextIndex = $scope.getTrueIndex(currentTurn+1);
        var superNextIndex = $scope.getTrueIndex(currentTurn+2);
        
        if (superPreviousIndex == -1) {
            $scope.superPreviousName = "N/A";
        } else {
            var player = $scope.game.currentPlayers[superPreviousIndex];
            $scope.superPreviousName = player.name;
        }
        
        if (superNextIndex == -1) {
            $scope.superNextName = "N/A";
        } else {
            var player = $scope.game.currentPlayers[superNextIndex];
            $scope.superNextName = player.name;
        }
        
        if (previousIndex == -1) {
            $scope.previousName = "N/A";
            $scope.previousImage = $scope.defaultImage;
        } else {
            var previousPlayer = $scope.game.currentPlayers[previousIndex];
            $scope.previousName = previousPlayer.name;
            if (previousPlayer.image.length > 0) {
                $scope.previousImage = previousPlayer.image;
            } else {
                $scope.previousImage = $scope.defaultImage;
            }
        }
       
        var currentPlayer = $scope.game.currentPlayers[currentIndex];
        if (currentPlayer) {
            $scope.currentName = currentPlayer.name;
            if (currentPlayer.image.length > 0) {
                $scope.currentImage = currentPlayer.image;
            } else {
                $scope.currentImage = $scope.defaultImage;
            }
        } else {
             $scope.currentName = "N/A";
             $scope.currentImage = $scope.defaultImage;
        }
        
        if (nextIndex == -1) {
            $scope.nextName = "N/A";
            $scope.nextImage = $scope.defaultImage;
        } else {
           var nextPlayer = $scope.game.currentPlayers[nextIndex]; 
           $scope.nextName = nextPlayer.name;
           if (nextPlayer.image.length > 0) {
               $scope.nextImage = nextPlayer.image;
           } else {
               $scope.nextImage = $scope.defaultImage;
           }
        }
         
    }
    
    $scope.updatePlayer = function() {
        $http({
            method: 'POST',
            url: '/upsertplayer.php',
            data: {'gamecode': $scope.game.code,
                     'playername':$scope.player.name,
                     'playerimage': $scope.player.image,
                     'turnorder': $scope.newTurnOrder}
          }).then(function successCallback(response) {
              console.log(response);
              $scope.game = response.data[0];
              $scope.player = response.data[1];
            }, function errorCallback(response) {
              console.log(response);
            });  
    }

    $scope.addPlayer = function() {
        $http({
            method: 'POST',
            url: '/upsertplayer.php',
            data: {'gamecode': $scope.game.code,
                     'playername':$scope.newName,
                     'playerimage': $scope.newImage,
                     'turnorder': $scope.newTurnOrder}
          }).then(function successCallback(response) {
              // this callback will be called asynchronously
              // when the response is available
              console.log(response);
              $scope.game = response.data[0];
              
              $scope.newName = "";
              $scope.newImage = "";
              $scope.newTurnOrder = 0;
            }, function errorCallback(response) {
              console.log(response);
            });
    }

    $scope.startGame = function() {
        $http({
            method: 'GET',
            url: '/creategame.php'
          }).then(function successCallback(response) {
              // this callback will be called asynchronously
              // when the response is available
              console.log(response);
              $scope.game = response.data;
              $scope.gameMode = 1; //You are now the host.
              $interval($scope.refreshGameState, 5000);
              
            }, function errorCallback(response) {
              // called asynchronously if an error occurs
              // or server returns response with an error status.
              console.log(response);
            });
    }
    
    $scope.refreshGameState = function() {
        $scope.counter ++;
        $http({
            method: 'GET',
            url: '/gamestate.php',
            params: {'gamecode': $scope.game.code}
          }).then(function successCallback(response) {
              console.log(response);
              $scope.game = response.data; 
         
              $scope.updatePortraits();
                      
            }, function errorCallback(response) {
              // called asynchronously if an error occurs
              // or server returns response with an error status.
              console.log(response);
            });
    }
    
    $scope.kickPlayer = function(player) {
        $http({
            method: 'GET',
            url: '/removeplayer.php',
            params: {'gamecode': $scope.game.code, 'playerid' : player.id}
          }).then(function successCallback(response) {
              console.log(response);

              $scope.game = response.data; 
                      
            }, function errorCallback(response) {
              // called asynchronously if an error occurs
              // or server returns response with an error status.
              console.log(response);
            });
    }
    
    $scope.joinGame = function() {
        $http({
            method: 'GET',
            url: '/joingame.php',
            params: {'gamecode': $scope.codeInput, 'playername' : $scope.playerName}
          }).then(function successCallback(response) {
              console.log(response);
              $scope.gameMode = 2; //You are now a participant
              $scope.game = response.data[0];
              $scope.player = response.data[1];
              
              $interval($scope.refreshGameState, 5000);
                      
            }, function errorCallback(response) {
              // called asynchronously if an error occurs
              // or server returns response with an error status.
              console.log(response);
            });
    }
  


});

