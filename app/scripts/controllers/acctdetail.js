'use strict';

angular.module('todoApp')
  .controller('AcctdetailCtrl', function ($scope, todoFactory) {

    $scope.getAccountDetails = function (){
      todoFactory.getAccountDetails()
        .success(function (data) {
          if (data){
            $scope.accountType = data.accountType;
            $scope.paidThrough = data.paidThrough;
          }
        })
        .error(function (error) {
          $scope.status = 'Error Getting Account Details:' + error.message;
        });
    };


    $scope.processPayment = function (token){
      //$scope.mytoken = token;
      todoFactory.processPayment(token)
        .success(function (data) {
          if (data){
            todoFactory.msgSuccess('Payment Received!');
            $scope.getAccountDetails();
          }
        })
        .error(function (error) {
          $scope.status = 'Error Processing Payment:' + error.message;
        });
    };


  });
