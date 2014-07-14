'use strict';

angular.module('todoApp')
  .factory('authentication', ['$http', '$q', function($http, $q) {

    // Public API here
    return {
      ////=============================================================================///
      getLoginStatus:  function() {
        var url = 'login.php';
        var passedData = {action: 'getLoginStatus'};
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
      },

      ////=============================================================================///
      registerUser: function(passedData) {
        var url = 'login.php';
        passedData.action = 'registerUser';
        //OLD CODE:   return $http.post('login.php',passedData);
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
      },

      ////=============================================================================///
      login:  function(passedData) {
        passedData.action = 'validateUser';
        return $http.post('login.php',passedData);
      },

      logOut: function() {
        var passedData = {action: 'logOutUser'};
        return $http.post('login.php',passedData);
      },

      changePassword: function(passedData) {
        passedData.action = 'changePassword';
        return $http.post('login.php',passedData);
      }
    };
  }]);

