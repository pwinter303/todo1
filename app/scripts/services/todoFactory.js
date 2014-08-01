'use strict';

angular.module('todoApp')
  .factory('todoFactory', ['$http', '$q', function($http, $q) {

    var dataFactory = {};

    ////=============================================================================///
    dataFactory.addTodo = function(passedData) {
      var url = 'todo.php';
      passedData.action = 'addNew';
      //OLD CODE: return $http.post('todo.php',passedData);
      var promise = $http.post(url , passedData);
      return promise.then(function(result) {
        if (typeof result.data === 'object') {
          return result.data;
        } else {
          // call was successful but response was invalid (result was not an object)
          return $q.reject(result.data);
        }
      }, function(result) {
        // something went wrong.... error on the call..
        return $q.reject(result.data);
      });
    };
    ////=============================================================================///
    dataFactory.deleteTodo = function(passedData) {
      var url = 'todo.php';
      passedData.action = 'deleteTodo';
      var promise = $http.post(url , passedData);
      return promise.then(function(result) {
        if (typeof result.data === 'object') {
          return result.data;
        } else {
          // call was successful but response was invalid (result was not an object)
          return $q.reject(result.data);
        }
      }, function(result) {
        // something went wrong.... error on the call..
        return $q.reject(result.data);
      });
    };
    ////=============================================================================///
    dataFactory.updateTodo = function(passedData) {
      var url = 'todo.php';
      passedData.action = 'updateTodo';
      //OLD CODE: return $http.post('todo.php',passedData);
      var promise = $http.post(url , passedData);
      return promise.then(function(result) {
        if (typeof result.data === 'object') {
          return result.data;
        } else {
          // call was successful but response was invalid (result was not an object)
          return $q.reject(result.data);
        }
      }, function(result) {
        // something went wrong.... error on the call..
        return $q.reject(result.data);
      });
    };
    ////=============================================================================///
    dataFactory.getToDos = function() {
      var url = 'todo.php';
      var passedData = {action: 'gettodos'};
      //OLD CODE: return $http({method:'GET', url:'todo.php', params: passedData});
      // Start Standard Code... GET
      var promise = $http.get(url , {params: passedData });
      return promise.then(function(result) {
        if (typeof result.data === 'object') {
          return result.data;
        } else {
          // call was successful but response was invalid (result was not an object)
          return $q.reject(result.data);
        }
      }, function(result) {
        // something went wrong.... error on the call..
        return $q.reject(result.data);
      });
    };
    ////=============================================================================///
    dataFactory.moveTodos = function(passedData) {
      var url = 'todo.php';
      passedData.action = 'moveTodos';
      //OLD CODE: return $http.post('todo.php',passedData);
      // Start Standard Code... POST
      var promise = $http.post(url , passedData);
      return promise.then(function(result) {
        if (typeof result.data === 'object') {
          return result.data;
        } else {
          // call was successful but response was invalid (result was not an object)
          return $q.reject(result.data);
        }
      }, function(result) {
        // something went wrong.... error on the call..
        return $q.reject(result.data);
      });
    };
    ////=============================================================================///
    dataFactory.getTodoGroups = function() {
      var url = 'todo.php';
      var passedData = {action: 'gettodogroups'};
      //OLD CODE: return $http({method:'GET', url:'todo.php', params: passedData});
      // Start Standard Code... GET
      var promise = $http.get(url , {params: passedData });
      return promise.then(function(result) {
        if (typeof result.data === 'object') {
          return result.data;
        } else {
          // call was successful but response was invalid (result was not an object)
          return $q.reject(result.data);
        }
      }, function(result) {
        // something went wrong.... error on the call..
        return $q.reject(result.data);
      });
    };
    ////=============================================================================///
    dataFactory.addGroup = function(passedData) {
      var url = 'todo.php';
      passedData.action = 'addGroup';
      //OLD CODE: return $http.post('todo.php',passedData);
      // Start Standard Code... POST
      var promise = $http.post(url , passedData);
      return promise.then(function(result) {
        if (typeof result.data === 'object') {
          return result.data;
        } else {
          // call was successful but response was invalid (result was not an object)
          return $q.reject(result.data);
        }
      }, function(result) {
        // something went wrong.... error on the call..
        return $q.reject(result.data);
      });
    };
    ////=============================================================================///
    dataFactory.deleteGroup = function(passedData) {
      var url = 'todo.php';
      passedData.action = 'deleteGroup';
      //OLD CODE: return $http.post('todo.php',passedData);
      // Start Standard Code... POST
      var promise = $http.post(url , passedData);
      return promise.then(function(result) {
        if (typeof result.data === 'object') {
          return result.data;
        } else {
          // call was successful but response was invalid (result was not an object)
          return $q.reject(result.data);
        }
      }, function(result) {
        // something went wrong.... error on the call..
        return $q.reject(result.data);
      });
    };
    ////=============================================================================///
    dataFactory.updateGroup = function(passedData) {
      var url = 'todo.php';
      passedData.action = 'updateGroup';
      //OLD CODE: return $http.post('todo.php',passedData);
      // Start Standard Code... POST
      var promise = $http.post(url , passedData);
      return promise.then(function(result) {
        if (typeof result.data === 'object') {
          return result.data;
        } else {
          // call was successful but response was invalid (result was not an object)
          return $q.reject(result.data);
        }
      }, function(result) {
        // something went wrong.... error on the call..
        return $q.reject(result.data);
      });
    };
    ////=============================================================================///
    dataFactory.setGroupToActive = function(group_id) {
        var url = 'todo.php';
        var passedData = {action:'setGroupToActive', group_id: group_id };
        // Start Standard Code... POST
        var promise = $http.post(url , passedData);
        return promise.then(function(result) {
            if (typeof result.data === 'object') {
                return result.data;
            } else {
                // call was successful but response was invalid (result was not an object)
                return $q.reject(result.data);
            }
        }, function(result) {
            // something went wrong.... error on the call..
            return $q.reject(result.data);
        });
    };
    ////=============================================================================///
    dataFactory.getfrequencies = function() {
      var url = 'todo.php';
      var passedData = {action: 'getfrequencies'};
      //OLD CODE: return $http({method:'GET', url:'todo.php', params: passedData});
      // Start Standard Code... GET
      var promise = $http.get(url , {params: passedData });
      return promise.then(function(result) {
        if (typeof result.data === 'object') {
          return result.data;
        } else {
          // call was successful but response was invalid (result was not an object)
          return $q.reject(result.data);
        }
      }, function(result) {
        // something went wrong.... error on the call..
        return $q.reject(result.data);
      });
    };
    ////=============================================================================///
    dataFactory.getpriorities = function() {
      var url = 'todo.php';
      var passedData = {action: 'getpriorities'};
      //OLD CODE: return $http({method:'GET', url:'todo.php', params: passedData});
      // Start Standard Code... GET
      var promise = $http.get(url , {params: passedData });
      return promise.then(function(result) {
        if (typeof result.data === 'object') {
          return result.data;
        } else {
          // call was successful but response was invalid (result was not an object)
          return $q.reject(result.data);
        }
      }, function(result) {
        // something went wrong.... error on the call..
        return $q.reject(result.data);
      });
    };
    ////=============================================================================///
    dataFactory.getBatches = function() {
      var url = 'todo.php';
      var passedData = {action: 'getbatches'};
      //OLD CODE: return $http({method:'GET', url:'todo.php', params: passedData});
      // Start Standard Code... GET
      var promise = $http.get(url , {params: passedData });
      return promise.then(function(result) {
        if (typeof result.data === 'object') {
          return result.data;
        } else {
          // call was successful but response was invalid (result was not an object)
          return $q.reject(result.data);
        }
      }, function(result) {
        // something went wrong.... error on the call..
        return $q.reject(result.data);
      });
    };
    ////=============================================================================///
    dataFactory.deleteBatch = function(passedData) {
      var url = 'todo.php';
      passedData.action = 'deleteBatch';
      //OLD CODE: return $http.post('todo.php',passedData);
      // Start Standard Code... POST
      var promise = $http.post(url , passedData);
      return promise.then(function(result) {
        if (typeof result.data === 'object') {
          return result.data;
        } else {
          // call was successful but response was invalid (result was not an object)
          return $q.reject(result.data);
        }
      }, function(result) {
        // something went wrong.... error on the call..
        return $q.reject(result.data);
      });
    };
    ////=============================================================================///
    dataFactory.getAccountDetails = function() {
      var url = 'userAccount.php';
      var passedData = {action: 'getAccountDetails'};
      //OLD CODE:var myGet = $http({url: 'userAccount.php', method: 'GET', params: {action: 'getAccountDetails'}});
      //OLD CODE:return myGet.then(function(result) {
      //OLD CODE:return result.data;
      //OLD CODE:});
      // Start Standard Code... GET
      var promise = $http.get(url , {params: passedData });
      return promise.then(function(result) {
        if (typeof result.data === 'object') {
          return result.data;
        } else {
          // call was successful but response was invalid (result was not an object)
          return $q.reject(result.data);
        }
      }, function(result) {
        // something went wrong.... error on the call..
        return $q.reject(result.data);
      });
    };
    ////=============================================================================///
    dataFactory.processPayment = function(passedData) {
      var url = 'userAccount.php';
      passedData.action = 'processPayment';
      // OLD CODE: return $http.post('userAccount.php',passedData).then(function(result) {
      // OLD CODE:   return result.data;
      // OLD CODE: });
      // Start Standard Code... POST
      var promise = $http.post(url , passedData);
      return promise.then(function(result) {
        if (typeof result.data === 'object') {
          return result.data;
        } else {
          // call was successful but response was invalid (result was not an object)
          return $q.reject(result.data);
        }
      }, function(result) {
        // something went wrong.... error on the call..
        return $q.reject(result.data);
      });
    };
    ////=============================================================================///
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
