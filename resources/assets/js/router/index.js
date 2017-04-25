import Vue from 'vue'

import VueRouter from 'vue-router'

import Register from '../views/auth/register.vue'
import Login from '../views/auth/login.vue'
import RecipeIndex from '../views/recipes/index.vue'

Vue.use(VueRouter)

const router = new VueRouter({
    
    routes: [   
        { path: '/', component: RecipeIndex },
        { path: '/register', component: Register },
        { path: '/login', component: Login }
    ]

})

export default router