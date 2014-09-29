'use strict';

// kept this in case code is needed in the main page...
angular.module('todoApp')
  .controller('MainCtrl', function ($scope, $location, $anchorScroll) {
    $scope.awesomeThings = [
      'HTML5 Boilerplate',
      'AngularJS',
      'Karma'
    ];

    $scope.scrollTo = function(id) {
      $location.hash(id);
      $anchorScroll();
    }

  });
