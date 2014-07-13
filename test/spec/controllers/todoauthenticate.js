'use strict';

describe('Test of Controller: todoAuthenticateCtrl', function () {

  var scope;
  var controller;
  var getResponse = { data: 1 };
  var getDeferred;
  var myServiceMock1;
  var myServiceMock2;

  //mock Application to allow us to inject our own dependencies
  beforeEach(angular.mock.module('todoApp'));
  //mock the controller for the same reason and include $rootScope and $controller
  beforeEach(angular.mock.inject(function($q, $controller, $rootScope) {

    scope = $rootScope;
    myServiceMock1 = {
      getLoginStatusNew: function() {}
    };
    myServiceMock2 = {
      placeholder: function() {}
    };
    // setup a promise for the get
    getDeferred = $q.defer();
    //set in 'it'
    getDeferred.resolve({login: 1});
    spyOn(myServiceMock1, 'getLoginStatusNew').andReturn(getDeferred.promise);
    controller = $controller('todoAuthenticateCtrl', { $scope: scope, authentication: myServiceMock1});
  }));

  it('should set some data on the scope when successful', function () {
    //getDeferred.resolve(getResponse);
    scope.getLoginStatus();
    scope.$apply();
    expect(myServiceMock1.getLoginStatusNew).toHaveBeenCalled();
    //expect(scope.plw).toEqual(1);
    expect(scope.loggedIn).toEqual(1);
  });
});

