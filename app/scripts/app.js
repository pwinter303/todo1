'use strict';

// prevent toastr is not defined error in grunt/jshint
/*global toastr */



angular.module('todoApp', [
  'ngRoute',
  'angulartics',
  'angulartics.google.analytics'
])
  .config(function ($routeProvider, $locationProvider) {
    $routeProvider
      .when('/', {templateUrl: 'views/main.html',  controller: 'MainCtrl'})
      .when('/todolist', {templateUrl: 'views/todolist.html',  controller: 'TodolistCtrl' })
      .when('/account', {templateUrl: 'views/account.html', controller: 'AcctdetailCtrl'})
      .when('/groups', {templateUrl: 'views/groups.html'})//No need for controller.. defined in Index.Html
      .when('/login', {templateUrl: 'views/login.html'})
      .when('/contact', {templateUrl: 'views/contact.html', controller: 'ContactCtrl'})
      .when('/forgotpassword', {templateUrl: 'views/forgotpassword.html'})
      .when('/register', {templateUrl: 'views/register.html'})
      .when('/import', {templateUrl: 'views/import.html',controller: 'FileUploadCtrl'})
      .when('/social', {templateUrl: 'views/social.html',controller: 'SocialCtrl'})
      .when('/demouser', {templateUrl: 'views/demouser.html', controller: 'DemoUserCtrl'})
      .when('/welcome', {templateUrl: 'views/welcome.html',  controller: 'WelcomeCtrl'})
      .when('/faq', {templateUrl: 'views/faq.html',   controller: 'FaqCtrl'})
      .when('/faq_accordian', {templateUrl: 'views/faq_accordian.html', controller: 'FaqAccordianCtrl'})
      .otherwise({redirectTo: '/'});
    $locationProvider.html5Mode(false).hashPrefix('!');
  })
  .factory('authHttpResponseInterceptor',['$q','$location','$rootScope', function($q,$location, $rootScope){
    return {
      response: function(response){
        if (response.status === 401) {
          //console.log('Response 401');
        }
        return response || $q.when(response);
      },
      responseError: function(rejection) {
        if (rejection.status === 401) {
          //console.log('Response Error 401',rejection);
          //$location.path('/').search('returnTo', $location.path());
          $location.path('/');
          $rootScope.loggedIn = 0;
          $rootScope.$broadcast('LogOut', []);
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


//  .config(['AngularyticsProvider', function(AngularyticsProvider) {
//    AngularyticsProvider.setEventHandlers(['Console', 'GoogleUniversal']);
//  }]).run(['Angularytics',function(Angularytics) {
//    Angularytics.init();
//  }]);



