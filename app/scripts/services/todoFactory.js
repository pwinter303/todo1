'use strict';

angular.module('todoApp')
  .factory('todoFactory', ['$http', function($http) {

    var dataFactory = {};

    dataFactory.addTodo = function(todo) {
          todo.action = 'addNew';
          return $http.post('todo.php',todo);
        };

    dataFactory.updateTodo = function(todo) {
          todo.action = 'updateTodo';
          return $http.post('todo.php',todo);
        };

    dataFactory.getToDos = function() {
          var data = {action: 'gettodos'};
          return $http({method:'GET', url:'todo.php', params: data});
        };


    dataFactory.moveTodos = function(data) {
          data.action = 'moveTodos';
          return $http.post('todo.php',data);
        };

    dataFactory.getToDoGroups = function() {
          var data = {action: 'gettodogroups'};
          return $http({method:'GET', url:'todo.php', params: data});
        };

    dataFactory.addGroup = function(group) {
          group.action = 'addGroup';
          return $http.post('todo.php',group);
        };

    dataFactory.deleteGroup = function(group) {
          group.action = 'deleteGroup';
          return $http.post('todo.php',group);
        };

    dataFactory.updateGroup = function(group) {
          group.action = 'updateGroup';
          return $http.post('todo.php',group);
        };

    dataFactory.getfrequencies = function() {
          var data = {action: 'getfrequencies'};
          return $http({method:'GET', url:'todo.php', params: data});
        };

    dataFactory.getpriorities = function() {
          var data = {action: 'getpriorities'};
          return $http({method:'GET', url:'todo.php', params: data});
        };


    dataFactory.getBatches = function() {
          var data = {action: 'getbatches'};
          return $http({method:'GET', url:'todo.php', params: data});
        };

    dataFactory.deleteBatch = function(batch) {
          batch.action = 'deleteBatch';
          return $http.post('todo.php',batch);
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
