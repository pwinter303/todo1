'use strict';

// prevent toastr is not defined error in grunt/jshint
/*global toastr */

angular.module('todoApp', [
  'ngSanitize',
  'ngRoute',
  'ui.bootstrap',
])
  .config(function ($routeProvider) {
    $routeProvider
      .when('/', {templateUrl: 'views/main.html',  controller: 'MainCtrl'})
      .when('/todolist', {templateUrl: 'views/todolist.html',  controller: 'TodolistCtrl' })
      .when('/tododetail/:id', {templateUrl: 'views/tododetail.html',controller: 'TodolistCtrl'})
      .when('/settings', {templateUrl: 'views/settings.html', controller: 'TodolistCtrl'})
      .when('/login', {templateUrl: 'views/login.html'})
      .when('/register', {templateUrl: 'views/register.html'})
      .when('/import', {templateUrl: 'views/import.html',controller: 'TodolistCtrl'})
      .otherwise({redirectTo: '/'});
  })

  .factory('authHttpResponseInterceptor',['$q','$location',function($q,$location){
    return {
      response: function(response){
        if (response.status === 401) {
          console.log('Response 401');
        }
        return response || $q.when(response);
      },
      responseError: function(rejection) {
        if (rejection.status === 401) {
          console.log('Response Error 401',rejection);
          //$location.path('/').search('returnTo', $location.path());
          $location.path('/');
          // cant access the factory from here.
          //todoFactory.msgError('Please Login or Register');
          toastr.error('Please Login or Register');
        }
        return $q.reject(rejection);
      }
    };
  }])
  .config(['$httpProvider',function($httpProvider) {
    //Http Intercpetor to check auth failures for xhr requests
    $httpProvider.interceptors.push('authHttpResponseInterceptor');
  }]);