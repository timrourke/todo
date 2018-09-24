import Route from '@ember/routing/route';
import { inject } from '@ember/service';

export default Route.extend({
  store: inject('store'),
  
  setupController(controller) {
    this.store.findAll('todo').then((todos) => {
      controller.set('todos', todos);
    });
  },

  actions: {
    createNewTodo(newTodo) {
      const todo = this.store.createRecord('todo', newTodo);

      todo.save();
    },
  },
});
