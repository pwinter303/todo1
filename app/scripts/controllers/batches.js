'use strict';

angular.module('todoApp')
  .controller('BatchesCtrl', function ($scope, todoFactory, $rootScope ) {

    $scope.getBatches = function (){
      todoFactory.getBatches().then(function(data) {
        //this will execute when the AJAX call completes.
        $scope.batches = data;
      });
    };

    $scope.getBatches();

    $scope.deleteBatch = function (batch){
      todoFactory.deleteBatch(batch).then(function(data) {
        //this will execute when the AJAX call completes.
        if (data){
          todoFactory.msgSuccess('Batch & Associated Todos Deleted!');
          $scope.getBatches();
        }
      });
    };

    $rootScope.$on('uploadProgress', function (e, call) {
      $scope.getBatches();
    });


  });
