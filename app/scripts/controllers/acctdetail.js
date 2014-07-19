'use strict';

angular.module('todoApp')
  .controller('AcctdetailCtrl', ['$scope', 'todoFactory', function ($scope, todoFactory) {

    $scope.getAccountDetails = function (){
      todoFactory.getAccountDetails().then(function (data) {
        if (data){
          $scope.accountType = data.accountType;
          $scope.paidThrough = data.paidThrough;
        }
      }, function(error) {
        // promise rejected, could be because server returned 404, 500 error...
        todoFactory.msgError(error);
      });
    };

    $scope.processPayment = function (token){
      todoFactory.processPayment(token).then(function (data) {
        if (data){
          todoFactory.msgSuccess(data.msg);
          $scope.getAccountDetails();
        }
      }, function(error) {
        // promise rejected, could be because server returned 404, 500 error...
        todoFactory.msgError(error);
      });
    };


  }]);
