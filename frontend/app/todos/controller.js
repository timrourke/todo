import Controller from '@ember/controller';
import EmberObject from '@ember/object';

export default Controller.extend({
  newTodo: EmberObject.create({
    title: '',
    description: '',
  }),
});
