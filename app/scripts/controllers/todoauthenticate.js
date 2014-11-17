'use strict';

angular.module('todoApp')
  .controller('todoAuthenticateCtrl', ['$rootScope', '$scope', 'authentication', 'todoFactory', '$location', function($rootScope, $scope, authentication, todoFactory, $location){

        $scope.pwd = {};
        $rootScope.loggedIn = 0;


        //================================================================================
        $scope.getfrequencies = function (){
          todoFactory.getfrequencies().then(function (data) {
            $scope.frequencies = data;
          }, function(error) {
            // promise rejected, could be because server returned 404, 500 error...
            todoFactory.msgError('Error Getting Frequencies:' + error);
          });
        };

        //================================================================================
        $scope.getpriorities = function (){
          todoFactory.getpriorities().then(function (data) {
            $scope.priorities = data;
          }, function(error) {
            // promise rejected, could be because server returned 404, 500 error...
            todoFactory.msgError('Error Getting Priorities:' + error);
          });
        };


        //================================================================================
        $scope.getLoginStatus = function() {
          authentication.getLoginStatus().then(function (data) {
              if (data.login){
                $rootScope.loggedIn = Number(data.login);
                $scope.$broadcast('LoggedIn', []);
                //fixme: is this where this belongs? It was in todolist but there were cases when login wasnt set so these werent called
                $scope.getpriorities();
                $scope.getfrequencies();
              }
            }, function(error) {
              // promise rejected, could be because server returned 404, 500 error...
              $rootScope.loggedIn = 0;
              todoFactory.msgError(error);
            });
        };
        $scope.getLoginStatus();

//        if ($rootScope.loggedIn){
//          $scope.getpriorities();
//          $scope.getfrequencies();
//        }

        //================================================================================
        /* jshint camelcase: false */
        $scope.logIn = function (logInData){
          $scope.loginmsg='';
          authentication.login(logInData).then(function (data) {
            $rootScope.loggedIn = Number(data.login);
            if ($rootScope.loggedIn) {
              $scope.user = [];
              $scope.user.firstName = data.first_name;
              $scope.user.lastName = data.last_name;
              $scope.user.email = data.email;
              $scope.$broadcast('LoggedIn', []);
              //fixme: is this where this belongs? It was in todolist but there were cases when login wasnt set so these werent called
              $scope.getpriorities();
              $scope.getfrequencies();
              $location.path( '/todolist' );
            } else {
              $scope.loginmsg = 'ERROR - Invalid email/password combination';
            }
          }, function(error) {
            // promise rejected, could be because server returned 404, 500 error...
            todoFactory.msgError(error);
          });
        };

        //================================================================================
        $scope.logMeOut = function(){
          authentication.logOut().then(function () {
            $scope.$broadcast('LogOut', []);
            $rootScope.loggedIn = 0;
            $location.path( '/' );
            todoFactory.msgSuccess('You have logged out. Thanks');
          }, function(error) {
            // promise rejected, could be because server returned 404, 500 error...
            todoFactory.msgError(error);
          });
        };

        //================================================================================
        $scope.forgotPassword = function(passworddata){
          authentication.forgotPassword(passworddata).then(function (data) {
            if (data.error){
              todoFactory.msgError(data.error);
            } else {
              todoFactory.msgSuccess('Temporary password has been sent');
            }
          }, function(error) {
            // promise rejected, could be because server returned 404, 500 error...
            todoFactory.msgError(error);
          });
        };
        //================================================================================
        $scope.changePassword = function(passworddata){
          authentication.changePassword(passworddata).then(function (data) {
            if (data.error){
              todoFactory.msgError(data.error);
            } else {
              $scope.pwd.old = '';
              $scope.pwd.new1 = '';
              $scope.pwd.new2 = '';
              todoFactory.msgSuccess(data.msg);
            }
          }, function(error) {
            // promise rejected, could be because server returned 404, 500 error...
            todoFactory.msgError(error);
          });
        };

        //================================================================================
        $scope.registerMe = function(user){
          authentication.registerUser(user).then(function (data) {
            $scope.errormsg = '';
            if (data.error){
              //$scope.errormsg = data.error;
              todoFactory.msgError(data.errMsg);
            }
            if (data.login){
              $rootScope.loggedIn = 1;
              $scope.$broadcast('LoggedIn', []);
              todoFactory.msgSuccess(data.msg);
              $location.path( '/welcome' );
            }
          }, function(error) {
            // promise rejected, could be because server returned 404, 500 error...
            todoFactory.msgError(error);
          });
        };
      }]);

angular.module('todoApp')
.directive('formAutofillFix', function() {
    return function(scope, elem, attrs) {
        // Fixes Chrome bug: https://groups.google.com/forum/#!topic/angular/6NlucSskQjY
        elem.prop('method', 'POST');

        // Fix autofill issues where Angular doesn't know about autofilled inputs
        if(attrs.ngSubmit) {
          setTimeout(function() {
                elem.unbind('submit').submit(function(e) {
                    e.preventDefault();
                    elem.find('input, textarea, select').trigger('input').trigger('change').trigger('keydown');
                    scope.$apply(attrs.ngSubmit);
                  });
              }, 0);
        }
      };
  });