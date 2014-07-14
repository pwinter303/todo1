'use strict';

angular.module('todoApp')
  .factory('todoFactory', ['$http', function($http) {

    var dataFactory = {};

    dataFactory.addTodo = function(passedData) {
          passedData.action = 'addNew';
          return $http.post('todo.php',passedData);
        };

    dataFactory.updateTodo = function(passedData) {
          passedData.action = 'updateTodo';
          return $http.post('todo.php',passedData);
        };

    dataFactory.getToDos = function() {
          var passedData = {action: 'gettodos'};
          return $http({method:'GET', url:'todo.php', params: passedData});
        };


    dataFactory.moveTodos = function(passedData) {
          passedData.action = 'moveTodos';
          return $http.post('todo.php',passedData);
        };

    dataFactory.getToDoGroups = function() {
          var passedData = {action: 'gettodogroups'};
          return $http({method:'GET', url:'todo.php', params: passedData});
        };

    dataFactory.addGroup = function(passedData) {
          passedData.action = 'addGroup';
          return $http.post('todo.php',passedData);
        };

    dataFactory.deleteGroup = function(passedData) {
          passedData.action = 'deleteGroup';
          return $http.post('todo.php',passedData);
        };

    dataFactory.updateGroup = function(passedData) {
          passedData.action = 'updateGroup';
          return $http.post('todo.php',passedData);
        };

    dataFactory.getfrequencies = function() {
          var passedData = {action: 'getfrequencies'};
          return $http({method:'GET', url:'todo.php', params: passedData});
        };

    dataFactory.getpriorities = function() {
          var passedData = {action: 'getpriorities'};
          return $http({method:'GET', url:'todo.php', params: passedData});
        };


    dataFactory.getBatches = function() {
          var passedData = {action: 'getbatches'};
          return $http({method:'GET', url:'todo.php', params: passedData});
        };

    dataFactory.deleteBatch = function(passedData) {
          passedData.action = 'deleteBatch';
          return $http.post('todo.php',passedData);
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

    dataFactory.processPayment = function(passedData) {
      passedData.action = 'processPayment';
      return $http.post('userAccount.php',passedData).then(function(result) {
        return result.data;
      });
    };

    // prevent toastr is not defined error in grunt/jshint
    /*global toastr */
    dataFactory.msgSuccess = function(text) {
      toastr.options = {'timeOut': '2000'};
          toastr.success(text);
        };
    dataFactory.msgError = function(text) {
          toastr.options = {'timeOut': '5000'};
          toastr.error(text);
        };

    return dataFactory;

  }]);
