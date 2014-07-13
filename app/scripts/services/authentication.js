'use strict';

angular.module('todoApp')
  .factory('authentication', ['$http', '$q', function($http, $q) {

    // Public API here
    return {
      getLoginStatusNew:  function() {
        return $http.get('login.php').then(function(response) {
            if (typeof response.data === 'object') {
              return response.data;
            } else {
              // invalid response
              return $q.reject(response.data);
            }
          }, function(response) {
            // something went wrong
            return $q.reject(response.data);
          });
      },

      registerUser: function(user) {
        user.action = 'registerUser';
        return $http.post('login.php',user);
      },

      login:  function(user) {
        user.action = 'validateUser';
        return $http.post('login.php',user);
      },

      logOut: function() {
        var data = {action: 'logOutUser'};
        return $http.post('login.php',data);
      },

      changePassword: function(data) {
        data.action = 'changePassword';
        return $http.post('login.php',data);
      }
    };
  }]);

