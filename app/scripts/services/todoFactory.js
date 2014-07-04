'use strict';

angular.module('todoApp')
  .factory('todoFactory', ['$http', function($http) {

    var dataFactory = {};
    var loginStatus;

    dataFactory.getLoginStatusNew = function() {
          return $http.get('login.php');
        };

    dataFactory.addTodo = function(todo) {
          todo.action = 'addNew';
          return $http.post('todo.php',todo);
        };

    dataFactory.updateTodo = function(todo) {
          todo.action = 'updateTodo';
          return $http.post('todo.php',todo);
        };

    dataFactory.getToDos = function() {
          var todo = {};
          todo.action = 'gettodos';
          return $http({method:'GET', url:'todo.php', params: todo});
    };


    dataFactory.moveTodos = function(data) {
          data.action = 'moveTodos';
          return $http.post('todo.php',data).then(function(result) {
            return result.data;
          });
        };

    dataFactory.getToDoGroups = function() {
          var myGet = $http({
            url: 'todo.php',
            method: 'GET',
            params: {action: 'gettodogroups'}
          });
          return myGet.then(function(result) {
            return result.data;
          });
        };

    dataFactory.addGroup = function(group) {
          group.action = 'addGroup';
          return $http.post('todo.php',group).then(function(result) {
            return result.data;
          });
        };

    dataFactory.deleteGroup = function(group) {
          group.action = 'deleteGroup';
          return $http.post('todo.php',group).then(function(result) {
            return result.data;
          });
        };

    dataFactory.updateGroup = function(group) {
          group.action = 'updateGroup';
          return $http.post('todo.php',group).then(function(result) {
            return result.data;
          });
        };

    dataFactory.registerUser = function(user) {
          user.action = 'registerUser';
          return $http.post('login.php',user).then(function(result) {
            return result.data;
          });
        };

    dataFactory.getfrequencies = function() {
          var myGet = $http({
            url: 'todo.php',
            method: 'GET',
            params: {action: 'getfrequencies'}
          });
          return myGet.then(function(result) {
            return result.data;
          });
        };

    dataFactory.getpriorities = function() {
          var myGet = $http({
            url: 'todo.php',
            method: 'GET',
            params: {action: 'getpriorities'}
          });
          return myGet.then(function(result) {
            return result.data;
          });
        };
    dataFactory.getOneTodo = function() {
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


    dataFactory.login = function(user) {
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

    dataFactory.logOut = function() {
          //see comments in getOneTodo
          var data = {
            action: 'logOutUser'
          };
          return $http.post('login.php',data).then(function(result) {
            return result.data;
          });
        };

    dataFactory.changePassword = function(data) {
          data.action = 'changePassword';
          return $http.post('login.php',data).then(function(result) {
            return result.data;
          });
        };

    dataFactory.getBatches = function() {
          var myGet = $http({
            url: 'todo.php',
            method: 'GET',
            params: {action: 'getbatches'}
          });
          return myGet.then(function(result) {
            return result.data;
          });
        };

    dataFactory.deleteBatch = function(batch) {
          batch.action = 'deleteBatch';
          return $http.post('todo.php',batch).then(function(result) {
            return result.data;
          });
        };

    dataFactory.getAccountDetails = function() {
          var myGet = $http({
            url: 'userAccount.php',
            method: 'GET',
            params: {action: 'getAccountDetails'}
          });
          return myGet.then(function(result) {
            return result.data;
          });
        };

    dataFactory.processPayment = function(token) {
          token.action = 'processPayment';
          return $http.post('userAccount.php',token).then(function(result) {
            return result.data;
          });

        };

    // prevent toastr is not defined error in grunt/jshint
    /*global toastr */
    toastr.options = {
      'timeOut': '2000'
    };
    dataFactory.msgSuccess = function(text) {
          toastr.success(text);
        };
    dataFactory.msgError = function(text) {
          toastr.error(text);
        };

    return dataFactory;

  }]);
