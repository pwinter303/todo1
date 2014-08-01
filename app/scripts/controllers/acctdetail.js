'use strict';

angular.module('todoApp')
  .controller('AcctdetailCtrl', ['$scope', 'todoFactory', function ($scope, todoFactory) {

    $scope.getAccountPeriod = function (){
      todoFactory.getAccountPeriod().then(function (data) {
        if (data){
          $scope.accountPeriods = data;
        }
      }, function(error) {
        // promise rejected, could be because server returned 404, 500 error...
        todoFactory.msgError(error);
      });
    };


    $scope.getAccountPeriod();

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
