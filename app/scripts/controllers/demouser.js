'use strict';

angular.module('todoApp')
  .controller('DemoUserCtrl', ['$scope', 'todoFactory', function($scope, todoFactory){

    //================================================================================
    $scope.getDemoUser = function (){
      todoFactory.getDemoUser().then(function (data) {
        // Pass back to user object and then the page will use the individual fields (email, password)
        $scope.user = data;
      }, function(error) {
        // promise rejected, could be because server returned 404, 500 error...
        todoFactory.msgError('Error Getting Demo User:' + error);
      });
    };

    $scope.getDemoUser();

  }]);
