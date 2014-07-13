'use strict';

angular.module('todoApp')
  .controller('todoAuthenticateCtrl', ['$scope', 'authentication', 'todoFactory', '$location', function($scope, authentication, todoFactory, $location){

        $scope.pwd = {};
        $scope.loggedIn = 0;

        $scope.getLoginStatus = function() {
          authentication.getLoginStatusNew().then(function (data) {
              if (data.login){
                $scope.loggedIn = data.login;
                $scope.$broadcast('LoggedIn', []);
              }
            }, function(error) {
              // promise rejected, could log the error with: console.log('error', error);
              $scope.loggedIn = 0;
        });
        };
        $scope.getLoginStatus();

        $scope.logIn = function (user){
            $scope.loginmsg='';
            authentication.login(user)
              .success(function (data) {
                // php returns a string.. must convert to number
                $scope.loggedIn = Number(data.login);
                if ($scope.loggedIn) {
                  $scope.$broadcast('LoggedIn', []);
                  $location.path( '/todolist' );
                } else {
                  $scope.loginmsg = 'ERROR - Invalid email/password combination';
                }
              })
              .error(function (error) {
                $scope.status = 'Error Logging In:' + error.message;
              });
          };

        $scope.logMeOut = function(){
          authentication.logOut()
            .success(function () {
              $scope.$broadcast('LogOut', []);
              $scope.loggedIn = 0;
              $location.path( '/' );
            })
            .error(function (error) {
              $scope.status = 'Error Logging Out:' + error.message;
            });
        };

        $scope.changePassword = function(passworddata){
          authentication.changePassword(passworddata)
            .success(function (data) {
              if (data.error){
                todoFactory.msgError(data.error);
              } else {
                $scope.pwd.old = '';
                $scope.pwd.new1 = '';
                $scope.pwd.new2 = '';
                todoFactory.msgSuccess(data.msg);
              }
            })
            .error(function (error) {
              $scope.status = 'Error Changing Password:' + error.message;
            });
        };

        $scope.registerMe = function(user){
          authentication.registerUser(user)
            .success(function (data) {
              $scope.errormsg = '';
              if (data.error){
                $scope.errormsg = data.error;
              }
              if (data.login){
                $scope.loggedIn = 1;
                $scope.$broadcast('LoggedIn', []);
                todoFactory.msgSuccess(data.msg);
                $location.path( '/todolist' );
              }
            })
            .error(function (error) {
              $scope.status = 'Error Logging In:' + error.message;
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