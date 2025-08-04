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
import Menu from './Components/Menu.vue';
import MainLayout from './Components/MainLayout.vue';

const routes = [
  { path: '/', component: Register},
  { path: '/login', component: Login, name: 'login' },
  { path: '/register', component: Register, name: 'register' },
  // { path: '/setting', component: Setting, name: 'setting' },
  // { path: '/todolist', component: TodoList, name: 'todolist' },
  // { path: '/user', component: User, name: 'user' },
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
   {
    path: '/',
    component: MainLayout,  // Use persistent layout with sidebar
    children: [
      {
        path: 'Setting',
        component: Setting,
        name: 'setting'
      },
      {
        path: 'TodoList',
        component: TodoList,
        name: 'todolist'
      },
      {
        path: 'User',
        component: User,
        name: 'user' 
      },
      {
        path:'Menu',
        component: Menu,
        name:'menu'
      }
      
    ],
    meta: { requiresAuth: true },  // These routes require the user to be authenticated
  },
];


const router = createRouter({
  history: createWebHistory(),
  routes,
});

// router.beforeEach((to, from, next) => {
//   const authStore = useAuthStore();  // Accessing authentication store
//   const isAuthenticated = authStore.isAuthenticated; // Check if the user is logged in

//   if (to.meta.requiresAuth && !isAuthenticated) {
//     next('/login');  // Redirect to login page if not authenticated
//   } else {
//     next();
//   }
// });

createApp(App).use(router).mount('#app');
