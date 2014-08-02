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

    //fixme: should this be done elsewhere?  login? is it needed in other places?
    $scope.getEmail = function (){
        todoFactory.getEmail().then(function (data) {
            if (data){
                $scope.email = data.email;
            }
        }, function(error) {
            // promise rejected, could be because server returned 404, 500 error...
            todoFactory.msgError(error);
        });
    };
    $scope.getEmail();


    $scope.processPayment = function (token){
      todoFactory.processPayment(token).then(function (data) {
        if (data){
            if (data.msg){
              todoFactory.msgSuccess(data.msg);
            }
            if (data.err){
                todoFactory.msgError(data.msg);
            }
            $scope.getAccountPeriod();
        }
      }, function(error) {
        // promise rejected, could be because server returned 404, 500 error...
        todoFactory.msgError(error);
      });
    };


  }]);
