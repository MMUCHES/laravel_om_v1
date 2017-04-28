import Vue from 'vue'
import VueRouter from 'vue-router'

import Login from '../views/auth/login.vue'
import Register from '../views/auth/register.vue'
import RecipeIndex from '../views/Recipe/index.vue'
import RecipeShow from '../views/Recipe/show.vue'
import RecipeForm from '../views/Recipe/form.vue'
import NotFound from '../views/notFound.vue'

Vue.use(VueRouter)

const router = new VueRouter({

	routes: [
		{ path: '/', component: RecipeIndex },
		{ path: '/products/create', component: RecipeForm, meta: { mode: 'create' }},
		{ path: '/products/:id/edit', component: RecipeForm, meta: { mode: 'edit' }},
		{ path: '/products/:id', component: RecipeShow },
		{ path: '/register', component: Register },
		{ path: '/login', component: Login },
		{ path: '/not-found', component: NotFound },
		{ path: '*', component: NotFound }
	]
})

export default router
