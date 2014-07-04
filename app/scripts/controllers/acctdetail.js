'use strict';

angular.module('todoApp')
  .controller('AcctdetailCtrl', function ($scope, todoFactory) {

    $scope.getAccountDetails = function (){
      todoFactory.getAccountDetails().then(function(data){
        if (data){
          $scope.accountType = data.accountType;
          $scope.paidThrough = data.paidThrough;
        }
      });
    };



    $scope.processPayment = function (token){
      //$scope.mytoken = token;
      todoFactory.processPayment(token).then(function(data){
        if (data){
          todoFactory.msgSuccess('Payment Received!');
          $scope.getAccountDetails();
        }
      });
    };


  });
