'use strict';

angular.module('todoApp')
  .factory('authentication', ['$http', function($http) {

    // Public API here
    return {
      getLoginStatusNew:  function() {
        return $http.get('login.php');
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

