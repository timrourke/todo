import { module, test } from 'qunit';
import { setupTest } from 'ember-qunit';
import Service from '@ember/service';
import EmberObject from '@ember/object';
import {settled} from 'ember-test-helpers';

const ModelStub = EmberObject.extend({
  saveWasCalled: false,

  save() {
    this.set('saveWasCalled', true);
  },
});

const StoreStub = Service.extend({
  createRecordCalledWith: null,
  findAllCalledWith: null,
  modelToReturnInCreateRecord: EmberObject.create(),

  createRecord() {
    this.set('createRecordCalledWith', ...arguments);

    return this.get('modelToReturnInCreateRecord');
  },

  findAll() {
    this.set('findAllCalledWith', ...arguments);

    return new Promise((res) => res([]));
  },
});

module('Unit | Route | todos', function(hooks) {
  setupTest(hooks);

  hooks.beforeEach(function() {
    this.owner.unregister('service:store');
    this.owner.register('service:store', StoreStub);
  });

  test('it exists', function(assert) {
    const route = this.owner.lookup('route:todos');
    assert.ok(route);
  });

  test('should set up controller', function(assert) {
    const route = this.owner.lookup('route:todos');
    const stubController = EmberObject.create({});
    const store = this.owner.lookup('service:store');

    route.setupController(stubController);

    assert.equal(
      store.get('findAllCalledWith'),
      'todo'
    );

    settled().then(() => {
      assert.deepEqual(
        stubController.get('todos'),
        []
      );
    });
  });

  test('should create a new todo', function(assert) {
    const route = this.owner.lookup('route:todos');
    const store = this.owner.lookup('service:store');

    const expectedArgToCreateRecord = EmberObject.create();
    const modelStub = ModelStub.create();
    store.set('modelToReturnInCreateRecord', modelStub);

    route.send('createNewTodo', expectedArgToCreateRecord);

    assert.ok(
      modelStub.get('saveWasCalled'),
      'The new todo was saved'
    );
  });
});
