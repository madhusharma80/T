<template>
  <div class="page-container">
    <!-- Sidebar -->
    <div class="sidebar" :class="{ 'sidebar-hidden': !sidebarOpen }">
      <div class="sidebar-header">
        <i class="fas fa-user-tie"></i>
        <div class="user-info">
          <p class="user-name">{{ username }}</p>
          <p class="user-email">{{ email }}</p>
        </div>
      </div>
      <ul>
        <li><router-link to="/menu"><i class="fas fa-home"></i> MENU</router-link></li>
        <li><router-link to="/user"><i class="fas fa-user"></i> USER</router-link></li>
        <li><router-link to="/todolist"><i class="fas fa-check-square"></i> TODO</router-link></li>
        <li><router-link to="/setting"><i class="fas fa-cogs"></i> SETTINGS</router-link></li>
      </ul>
      <div class="sidebar-footer">
        <button @click="logout"><i class="fas fa-sign-out-alt"></i> Logout</button>
      </div>
    </div>

    <!-- Content area (without the welcome card) -->
    <div :class="{ 'content-area-expanded': sidebarOpen }" class="content">
      <router-view></router-view>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';

// User data from localStorage
const username = ref(localStorage.getItem('userName') || 'Guest');
const email = ref(localStorage.getItem('userEmail') || 'No email');

// Sidebar visibility state
const sidebarOpen = ref(true);

// Logout function
const logout = () => {
  localStorage.removeItem('auth_token');
  localStorage.removeItem('userName');
  localStorage.removeItem('userEmail');
  window.location.href = '/login';
};
</script>

<style scoped>
.page-container {
  display: flex;
  height: 100vh;
}

/* Sidebar styles */
.sidebar {
  position: fixed;
  width: 230px;
  height: 100vh;
  background: linear-gradient(to bottom, #183655, #74aee0);
  box-shadow: 0 4px 8px rgba(15, 15, 15, 0.1);
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  transition: transform 0.3s ease;
}

.sidebar-hidden {
  transform: translateX(-100%);
}

.sidebar-header {
  text-align: center;
  font-size: 40px;
  color: white;
  margin-bottom: 10px;
}

.user-info {
  color: #f5f2f2;
  text-align: center;
  border-bottom: 1px solid white;
}

.user-name {
  font-weight: bold;
  font-size: 16px;
}

.user-email {
  font-size: 12px;
  color: #e6e4e4;
}

.sidebar ul {
  list-style-type: none;
  padding: 0;
  margin: 0;
}

.sidebar ul li {
  margin: 20px 0;
  text-align: center;
}

.sidebar ul li a {
  color: #bebaba;
  text-decoration: none;
  display: block;
  padding: 10px;
  font-size: 14px;
  display: flex;
  align-items: center;
  gap: 10px;
}

.sidebar ul li a i {
  font-size: 18px;
}

.sidebar ul li a:hover {
  background-color: #fffefe;
  color: #726b6b;
}

.sidebar-footer {
  margin-top: auto;
  text-align: center;
}

.sidebar-footer button {
  width: 100%;
  padding: 10px 20px;
  cursor: pointer;
  font-size: 15px;
  background-color: #326999;
  color: white;
  border: none;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
}

.sidebar-footer button:hover {
  background-color:#183655; 
}

/* Content styles */
.content {
  margin-left: 230px; /* Adjust for sidebar width */
  padding: 20px;
  flex-grow: 1;
  transition: margin-left 0.3s ease;
}

.content-area-expanded {
  margin-left: 230px; /* Keep the space for sidebar when open */
}
</style>
