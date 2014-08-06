'use strict';

angular.module('todoApp')
  .controller('ContactCtrl', ['$scope', 'todoFactory', function ($scope, todoFactory) {
    $scope.getContactTypes = function (){
      $scope.contactTypes = [
        {'cd':'1', 'name':'Feature Request'},
        {'cd':'2', 'name':'Feedback'},
        {'cd':'3', 'name':'Problem Report'},
        {'cd':'4', 'name':'Other'}
      ];
    };
    $scope.getContactTypes();


    $scope.contactSubmit = function (passedData){
      todoFactory.contactSubmit(passedData).then(function (data) {
        if (data){
          if (data.msg){
            todoFactory.msgSuccess(data.msg);
          }
          if (data.err){
            todoFactory.msgError(data.msg);
          }
        }
      }, function(error) {
        // promise rejected, could be because server returned 404, 500 error...
        todoFactory.msgError(error);
      });
    };

  }]);
