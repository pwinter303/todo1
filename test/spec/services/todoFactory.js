'use strict';

describe('Service: todoFactory', function () {

  // load the service's module
  beforeEach(module('todoApp'));

  // instantiate service
  var Authentication;
  beforeEach(inject(function (_todoFactory_) {
    Authentication = _todoFactory_;
  }));

  it('should do something', function () {
    expect(!!Authentication).toBe(true);
  });

});

