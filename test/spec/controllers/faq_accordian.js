'use strict';

describe('Controller: FaqAccordianCtrl', function () {

  // load the controller's module
  beforeEach(module('todoApp'));

  var FaqAccordianCtrl,
    scope;

  // Initialize the controller and a mock scope
  beforeEach(inject(function ($controller, $rootScope) {
    scope = $rootScope.$new();
    FaqAccordianCtrl = $controller('FaqAccordianCtrl', {
      $scope: scope
    });
  }));

  it('should attach a list of awesomeThings to the scope', function () {
    expect(scope.awesomeThings.length).toBe(3);
  });
});
