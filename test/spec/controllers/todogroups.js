'use strict';

describe('Controller: TodoGroupCtrl', function () {

  // load the controller's module
  beforeEach(module('todoApp'));

  var AcctdetailCtrl,
    scope;

  // Initialize the controller and a mock scope
  beforeEach(inject(function ($controller, $rootScope) {
    scope = $rootScope.$new();
    AcctdetailCtrl = $controller('TodoGroupCtrl', {
      $scope: scope
    });
  }));

  it('should attach a list of awesomeThings to the scope', function () {
    expect(scope.awesomeThings.length).toBe(3);
  });
});