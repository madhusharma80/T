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
        <li>
          <router-link
            to="/menu"
            active-class="active-link"
            class="sidebar-link">
            <i class="fas fa-home"></i> HOME</router-link>
        </li>
        <li>
          <router-link
            to="/user"
            active-class="active-link"
            class="sidebar-link">
            <i class="fas fa-user"></i> USER</router-link>
        </li>
        <li>
          <router-link
            to="/todolist"
            active-class="active-link"
            class="sidebar-link">
            <i class="fas fa-check-square"></i> TODO</router-link>
        </li>
        <li>
          <router-link
            to="/setting"
            active-class="active-link"
            class="sidebar-link">
            <i class="fas fa-cogs"></i> SETTINGS</router-link>
        </li>
      </ul>
      <div class="sidebar-footer">
        <button @click="logout"><i class="fas fa-sign-out-alt"></i> Logout</button>
      </div>
    </div>

    <!-- Main Content Area -->
    <div :class="{ 'content-area-expanded': sidebarOpen }" class="content">
      <!-- Background Overlay for Blur Effect -->
      <div v-if="showWelcomeCard" class="background-overlay"></div>

      <!-- Success Welcome Card (Only shows once after login) -->
      <div v-if="showWelcomeCard" class="welcome-card">
        <div class="card">
          <div class="card-body">
            <div class="icon-container">
              <i class="fas fa-check-circle success-icon"></i>
            </div>
            <h5 class="card-title">Welcome, {{ username }}!</h5>
            <p class="card-text">You are successfully logged in!</p>
            <button class="btn" @click="closeWelcomeCard">OK</button>
          </div>
        </div>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';

// User details from local storage
const username = ref(localStorage.getItem('userName'));
const email = ref(localStorage.getItem('userEmail'));

// Sidebar visibility state (no need to hide it unless desired)
const sidebarOpen = ref(true);

// Show the success card only once after login
const showWelcomeCard = ref(false);

// Check if the user has already closed the success card
onMounted(() => {
  const hasSeenWelcomeCard = localStorage.getItem('hasSeenWelcomeCard');
  
  // Show the card if it's the user's first login (has not seen the card)
  if (!hasSeenWelcomeCard) {
    showWelcomeCard.value = true;
  }
});

// Close the welcome card to click on OK button and store the state
const closeWelcomeCard = () => {
  showWelcomeCard.value = false;
  // Mark that the user has seen the welcome card
  localStorage.setItem('hasSeenWelcomeCard', 'true');
};

// Logout function
const logout = () => {
  localStorage.removeItem('auth_token');
  localStorage.removeItem('userName');
  localStorage.removeItem('userEmail');
  localStorage.removeItem('hasSeenWelcomeCard'); // Reset the success message state on logout
  window.location.href = '/login';
};
</script>

<style scoped>
.page-container {
  display: flex;
  height: 100vh;
}
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

.sidebar-link {
  color: #bebaba;
  text-decoration: none;
  display: block;
  padding: 10px;
  font-size: 14px;
  display: flex;
  align-items: center;
  gap: 10px;
}

.sidebar-link i {
  font-size: 18px;
}

.sidebar-link:hover {
  background-color: #fffefe;
  color: #726b6b;
}

/* Highlight the active link */
.active-link {
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
  background-color: #183655;
}

.content {
  margin-left: 230px; /* Adjust for sidebar width */
  padding: 20px;
  flex-grow: 1;
  transition: margin-left 0.3s ease;
}

.content-area-expanded {
  margin-left: 230px; /* Keep the space for sidebar when open */
}

/* Welcome card style */
.welcome-card {
  position: absolute;
  top: 20%;
  left: 50%;
  transform: translateX(-50%);
  z-index: 999;
}

.card {
  width: 400px;
  margin: 0 auto;
  padding: 10px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  background-color: #fff;
  align-items: center;
}

.card-title {
  font-size: 25px;
  font-weight: bold;
  color: #4CAF50;
}

.card-text {
  font-size: 16px;
  color: #555;
}

.card button {
  margin-top: 15px;
  width: 100%;
  border-color: #4CAF50;
}

.card button:hover {
  background-color: #4CAF50;
  color: white;
}

/* Background overlay style for blur effect */
.background-overlay {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.5);
  backdrop-filter: blur(3px);
  z-index: 998;
}

/* Icon styles */
.icon-container {
  text-align: center;
  margin-bottom: 15px;
}

.success-icon {
  color: #4CAF50;
  font-size: 65px;
}
</style>
