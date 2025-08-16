<template>
  <div class="app-layout">
    <!-- Sidebar -->
    <Welcome />

    <!-- Main Content Area -->
    <div class="main-content">
      <router-view></router-view>
    </div>
  </div>
</template>

<script setup>
import Welcome from './Welcome.vue'; 
</script>

<style scoped>
.app-layout {
  display: flex;
  height: 100vh;
}

.main-content {
  flex: 1;
  padding: 20px;
  overflow-y: auto;
}

.sidebar {
  width: 200px;
  color: white;
  height: 100%;
}
</style>
