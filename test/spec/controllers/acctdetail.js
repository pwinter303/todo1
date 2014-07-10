'use strict';

describe('Controller: AcctdetailCtrl', function () {

  // load the controller's module
  beforeEach(module('todoApp'));

  var AcctdetailCtrl,
    scope;

  // Initialize the controller and a mock scope
  beforeEach(inject(function ($controller, $rootScope) {
    scope = $rootScope.$new();
    AcctdetailCtrl = $controller('AcctdetailCtrl', {
      $scope: scope
    });
  }));

  it('should attach a list of awesomeThings to the scope', function () {
    expect(scope.awesomeThings.length).toBe(3);
  });
});
