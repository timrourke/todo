import { module, test } from 'qunit';
import { setupTest } from 'ember-qunit';

module('Unit | Adapter | application', function(hooks) {
  setupTest(hooks);

  const UUIDV1_REGEX = /^[0-9A-F]{8}-[0-9A-F]{4}-[1][0-9A-F]{3}-[89AB][0-9A-F]{3}-[0-9A-F]{12}$/i;

  // Replace this with your real tests.
  test('should generate a uuidv1', function(assert) {
    const adapter = this.owner.lookup('adapter:application');
    const generatedId = adapter.generateIdForRecord();

    assert.ok(UUIDV1_REGEX.test(generatedId));
  });
});
