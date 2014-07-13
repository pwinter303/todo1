'use strict';

angular.module('todoApp')
  .controller('BatchesCtrl', ['$scope', 'todoFactory', '$rootScope', function ($scope, todoFactory, $rootScope ) {

    $scope.getBatches = function (){
      todoFactory.getBatches()
        .success(function (data) {
          if (data){
            $scope.batches = data;
          }
        })
        .error(function (error) {
          $scope.status = 'Error Getting Batches:' + error.message;
        });
    };

    $scope.getBatches();

    $scope.deleteBatch = function (batch){
      todoFactory.deleteBatch(batch)
        .success(function (data) {
          if (data){
            todoFactory.msgSuccess('Batch & Associated Todos Deleted!');
            $scope.getBatches();
          }
        })
        .error(function (error) {
          $scope.status = 'Error Deleting Batch:' + error.message;
        });
    };

    /*jshint unused:false */
    $rootScope.$on('uploadProgress', function (e, call) {
      $scope.getBatches();
    });


  }]);
