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

  });
