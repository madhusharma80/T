import { createApp } from 'vue';
import { createRouter, createWebHistory } from 'vue-router';
import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap';
import '/resources/css/app.css';
import '@fortawesome/fontawesome-free/css/all.min.css';

import App from './Components/App.vue';
import Register from './Components/Register.vue';
import Login from './Components/Login.vue';
import Welcome from './Components/Welcome.vue'; 
import Setting from './Components/Setting.vue';
import TodoList from './Components/TodoList.vue';
import User from './Components/User.vue';

const routes = [
  { path: '/', component: Register},
  { path: '/login', component: Login, name: 'login',},
  { path: '/register', component: Register, name: 'register', },
  { path: '/setting', component: Setting, name: 'setting ', },
  { path: '/todolist', component: TodoList, name: 'todolist', },
  { path: '/User', component: User, name: 'user', },
  {
    path: '/welcome',
    component: Welcome,
    beforeEnter: (to, from, next) => {
      const token = localStorage.getItem('token');
      if (!token) {
        next('/login');  
      } else {
        next(); 
      }
    }
  },
  
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

createApp(App).use(router).mount('#app');
