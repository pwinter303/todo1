'use strict';

/**
 * @ngdoc function
 * @name todoApp.controller:WelcomeCtrl
 * @description
 * # WelcomeCtrl
 * Controller of the todoApp
 */
angular.module('todoApp')
  .controller('WelcomeCtrl', function ($scope) {
    $scope.awesomeThings = [
      'HTML5 Boilerplate',
      'AngularJS',
      'Karma'
    ];
  });
