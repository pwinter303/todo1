'use strict';

angular.module('todoApp')
  .factory('todoFactory', function ($route, $routeParams, $http) {

        var factory = {};
        var loginStatus;

        factory.getLoginStatus = function() {
            return $http.get('login.php').then(function(result) {
                return result.data;
              });
          };

        factory.setLoginStatus = function(stat) {
            loginStatus = stat;
          };

        factory.getLocalLoginStatus = function() {
            return loginStatus;
          };

        factory.addTodo = function(todo) {
          todo.action = 'addNew';
          return $http.post('todo.php',todo).then(function(result) {
            return result.data;
          });
        };

        factory.updateTodo = function(todo) {
          todo.action = 'updateTodo';
          return $http.post('todo.php',todo).then(function(result) {
            return result.data;
          });
        };

        factory.moveTodos = function(data) {
          data.action = 'moveTodos';
          return $http.post('todo.php',data).then(function(result) {
            return result.data;
          });
        };

        factory.getToDoGroups = function() {
          var myGet = $http({
            url: 'todo.php',
            method: 'GET',
            params: {action: 'gettodogroups'}
          });
          return myGet.then(function(result) {
            return result.data;
          });
        };

        factory.addGroup = function(group) {
          group.action = 'addGroup';
          return $http.post('todo.php',group).then(function(result) {
            return result.data;
          });
        };

        factory.deleteGroup = function(group) {
          group.action = 'deleteGroup';
          return $http.post('todo.php',group).then(function(result) {
            return result.data;
          });
        };

        factory.updateGroup = function(group) {
          group.action = 'updateGroup';
          return $http.post('todo.php',group).then(function(result) {
            return result.data;
          });
        };

        factory.uploadFile = function(file) {
          file.action = 'updateFile';
          return $http.post('todo.php',file).then(function(result) {
            return result.data;
          });
        };

        factory.registerUser = function(user) {
          user.action = 'registerUser';
          return $http.post('login.php',user).then(function(result) {
            return result.data;
          });
        };

        factory.getToDos = function() {
          //since $http.get returns a promise, and promise.then() also returns a promise..
          //that resolves to whatever value is returned in it's callback argument, we can return that.
          var myGet = $http({
            url: 'todo.php',
            method: 'GET',
            params: {action: 'gettodos'}
          });
          return myGet.then(function(result) {
              return result.data;
            });
        };

        factory.getfrequencies = function() {
          var myGet = $http({
            url: 'todo.php',
            method: 'GET',
            params: {action: 'getfrequencies'}
          });
          return myGet.then(function(result) {
            return result.data;
          });
        };

        factory.getpriorities = function() {
          var myGet = $http({
            url: 'todo.php',
            method: 'GET',
            params: {action: 'getpriorities'}
          });
          return myGet.then(function(result) {
            return result.data;
          });
        };
        factory.getOneTodo = function() {
              //since $http.get returns a promise, and promise.then() also returns a promise
            //that resolves to whatever value is returned in it's callback argument, we can return that.
            var myGet = $http({
                url: 'todo.php',
                method: 'GET',
                params: {action: 'gettodo'}
              });
            return myGet.then(function(result) {
                return result.data;
              });
          };


        factory.login = function(user) {
            //see comments in getOneTodo
            var data = {
                email: user.email,
                action: 'validateUser',
                password: user.password
              };
            return $http.post('login.php',data).then(function(result) {
                return result.data;
              });
          };

        factory.logOut = function() {
          //see comments in getOneTodo
          var data = {
            action: 'logOutUser'
          };
          return $http.post('login.php',data).then(function(result) {
            return result.data;
          });
        };

        factory.changePassword = function(data) {
          data.action = 'changePassword';
          return $http.post('login.php',data).then(function(result) {
            return result.data;
          });
        };

    // prevent toastr is not defined error in grunt/jshint
        /*global toastr */
        toastr.options = {
          'timeOut': '2000'
        };
        factory.msgSuccess = function(text) {
          toastr.success(text);
        };
        factory.msgError = function(text) {
          toastr.error(text);
        };

        return factory;

      });
