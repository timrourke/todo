import Route from '@ember/routing/route';

import { inject } from '@ember/service';

export default Route.extend({
  store: inject('store'),

  setupController(controller) {
    this.get('store').findAll('todo').then((todos) => {
      controller.set('todos', todos);
    });
  },
});
