import DS from 'ember-data';
import { v1 } from 'ember-uuid';

export default DS.JSONAPIAdapter.extend({
  namespace: 'api',
  
  generateIdForRecord() {
    return v1();
  }
});
