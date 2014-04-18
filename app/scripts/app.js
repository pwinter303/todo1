'use strict';

//var app = angular.module("app", []);

angular.module('todoApp', [
  'ngSanitize',
  'ngRoute',
  'ui.bootstrap'
])
  .config(function ($routeProvider) {
    $routeProvider
      .when('/', {
        templateUrl: 'views/main.html',
        controller: 'TodolistCtrl'
      })
      .when('/todolist', {
        templateUrl: 'views/todolist.html',
        controller: 'TodolistCtrl'
      })
      .when('/tododetail/:id', {
        templateUrl: 'views/tododetail.html',
        controller: 'TodolistCtrl'
      })
      .when('/settings', {
        templateUrl: 'views/settings.html',
        controller: 'TodolistCtrl'
      })
      .when('/todolistfull', {
        templateUrl: 'views/todolist_fulledit.html',
        controller: 'TodolistCtrl'
      })
      .when('/login', {
        templateUrl: 'views/login.html'
      })
      .when('/register', {
        templateUrl: 'views/register.html'
      })
      .when('/import', {
        templateUrl: 'views/import.html',
        controller: 'TodolistCtrl'
      })
      .when('/junk', {
        templateUrl: 'views/junk.html',
        controller: 'TodolistCtrl'
      })
      .otherwise({
        redirectTo: '/'
      });
  });