'use strict';

angular.module('todoApp')
  .factory('authentication', ['$http', '$q', function($http, $q) {

    // Public API here
    return {
      getLoginStatusNew:  function() {
        var promise = $http({
          url: 'login.php',
          method: 'GET'
        });
        return promise.then(function(result) {
            if (typeof result.data === 'object') {
              return result.data;
            } else {
              // invalid response
              return $q.reject(result.data);
            }
          }, function(response) {
            // something went wrong
            return $q.reject(result.data);
          });
//        var promise =  $http.get('login.php').then(function(response) {
//            if (typeof response.data === 'object') {
//              return response.data;
//            } else {
//              // invalid response
//              return $q.reject(response.data);
//            }
//          }, function(response) {
//            // something went wrong
//            return $q.reject(response.data);
//          });
//        return promise;
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

