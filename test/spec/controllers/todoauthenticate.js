'use strict';

describe('Test of Controller: todoAuthenticateCtrl', function () {

  var scope;
  var rootScope;
  var controller;
  var getDeferred;
  var myServiceMock1;
  var myServiceMock2;

  //mock Application to allow us to inject our own dependencies
  beforeEach(angular.mock.module('todoApp'));
  //mock the controller for the same reason and include $rootScope and $controller
  beforeEach(angular.mock.inject(function($q, $controller, $rootScope) {

    //scope = $rootScope;
    scope = $rootScope.$new();
    rootScope = $rootScope;
    myServiceMock1 = {
      getLoginStatus: function() {},
      login: function() {}
    };
    myServiceMock2 = {
      placeholder: function() {}
    };
    // setup a promise for the get
    getDeferred = $q.defer();
    spyOn(myServiceMock1, 'getLoginStatus').andReturn(getDeferred.promise);

    controller = $controller('todoAuthenticateCtrl', { $scope: scope, authentication: myServiceMock1});
  }));

  it('should set some data on the scope when successful', function () {
    var getResponse = {login: 1};
    getDeferred.resolve(getResponse);
    scope.getLoginStatus();
    scope.$apply();
    expect(myServiceMock1.getLoginStatus).toHaveBeenCalled();
    //expect(scope.plw).toEqual(1);
    expect(scope.loggedIn).toEqual(1);
  });

  it('should broadcast something', function() {
    var getResponse = {login: 1};
    spyOn(scope, '$broadcast');
    getDeferred.resolve(getResponse);
    scope.getLoginStatus();
    scope.$apply();
    expect(scope.$broadcast).toHaveBeenCalledWith('LoggedIn', []);
  });

});

