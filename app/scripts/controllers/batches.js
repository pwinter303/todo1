'use strict';

angular.module('todoApp')
  .controller('BatchesCtrl', ['$scope', 'todoFactory', '$rootScope', function ($scope, todoFactory, $rootScope ) {

    $scope.getBatches = function (){
      todoFactory.getBatches().then(function (data) {
        if (data){
          $scope.batches = data;
        }
      }, function(error) {
        // promise rejected, could be because server returned 404, 500 error...
        todoFactory.msgError('Error Getting Batches:' + error);
      });
    };
    $scope.getBatches();

    $scope.deleteBatch = function (batch){
      todoFactory.deleteBatch(batch).then(function (data) {
        if (data){
          todoFactory.msgSuccess('Batch & Associated Todos Deleted!');
          $scope.getBatches();
        }
      }, function(error) {
        // promise rejected, could be because server returned 404, 500 error...
        todoFactory.msgError('Error Deleting Batch:' + error);
      });
    };

    /*jshint unused:false */
    $rootScope.$on('uploadProgress', function (e, call) {
      $scope.getBatches();
    });


  }]);
