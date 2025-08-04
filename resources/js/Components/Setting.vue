<template>
  <div class="content-area">
    <div class="settings-container">
      <div class="settings-content">
        <!-- Personal Information Section -->
        <div class="section-card profile-section">
          <h3 class="section-title border-bottom border-primary">
            <i class="fas fa-user-circle"></i> Personal Information
          </h3>
          <form @submit.prevent="updateProfile">
            <div class="form-group">
              <label for="name">
                <i class="fas fa-user"></i> Name
              </label>
              <input 
                type="text" 
                id="name" 
                v-model="form.name" 
                placeholder="Enter your name"
                @input="clearError('name')"/>
              <span v-if="errors.name" class="error-message">{{ errors.name }}</span>
            </div>
            <div class="form-group">
              <label for="email">
                <i class="fas fa-envelope"></i> Email
              </label>
              <input 
                type="email" 
                id="email" 
                v-model="form.email" 
                placeholder="Enter your email"
                @input="clearError('email')"/>
              <span v-if="errors.email" class="error-message">{{ errors.email }}</span>
            </div>
            <button type="submit" class="btn button_primary">
              <i class="fas fa-save"></i> Update
            </button>
          </form>
        </div>
        <!-- Change Password Section -->
        <div class="section-card password-section">
          <h3 class="section-title border-bottom border-secondary">
            <i class="fas fa-lock"></i> Change Password
          </h3>
          <form @submit.prevent="updatePassword">
            <div class="form-group">
              <label for="currentPassword">
                <i class="fas fa-key"></i> Current Password
              </label>
              <input 
                type="password" 
                id="currentPassword" 
                v-model="form.currentPassword" 
                placeholder="Enter your current password"
                @input="clearError('currentPassword')"/>
              <span v-if="errors.currentPassword" class="error-message">{{ errors.currentPassword }}</span>
            </div>
            <div class="form-group">
              <label for="newPassword">
                <i class="fas fa-key"></i> New Password
              </label>
              <input 
                type="password" 
                id="newPassword" 
                v-model="form.newPassword" 
                placeholder="Enter new password"
                @input="clearError('newPassword')"/>
              <span v-if="errors.newPassword" class="error-message">{{ errors.newPassword }}</span>
            </div>
            <div class="form-group">
              <label for="confirmPassword">
                <i class="fas fa-key"></i> Confirm New Password
              </label>
              <input 
                type="password" 
                id="confirmPassword" 
                v-model="form.confirmPassword"
                placeholder="Confirm new password"
                @input="clearError('confirmPassword')"/>
              <span v-if="errors.confirmPassword" class="error-message">{{ errors.confirmPassword }}</span>
            </div>
            <button type="submit" class="btn btn-secondary">
              <i class="fas fa-save"></i> Save
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { useRouter } from 'vue-router'; 
const router = useRouter();


const form = ref({
  name: '',
  email: '',
  currentPassword: '',
  newPassword: '',
  confirmPassword: '',
});

const errors = ref({});

onMounted(() => {
  form.value.name = localStorage.getItem('userName') || '';
  form.value.email = localStorage.getItem('userEmail') || '';
});

// Clear error message when user starts typing
const clearError = (field) => {
  if (errors.value[field]) {
    delete errors.value[field];
  }
};

// Update Profile
const updateProfile = async () => {
  errors.value = {};

  if (!form.value.name || form.value.name.trim() === '') {
    errors.value.name = 'Please enter a valid name.';
    return;
  }

  if (!form.value.email || form.value.email.trim() === '') {
    errors.value.email = 'Please enter a valid email address.';
    return;
  }

  // Email format validation
  const emailRegex = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;
  if (!emailRegex.test(form.value.email)) {
    errors.value.email = 'Please enter a valid email format.';
    return;
  }

  try {
    const token = localStorage.getItem('token');
    const response = await axios.put('/api/user/update',
      {
        name: form.value.name,
        email: form.value.email,
      },
      {
        headers: {Authorization: `Bearer ${token}`, },
      }
    );
    localStorage.setItem('userName', form.value.name);
    localStorage.setItem('userEmail', form.value.email);

    alert('Profile updated successfully!');
  } catch (error) {
    console.error('Profile update failed:', error);
    if (error.response && error.response.data) {
      alert(`Error: ${error.response.data.message}`);
    } else {
      alert('There was an error updating your profile.');
    }
  }
};

// Update Password
const updatePassword = async () => {
  errors.value = {};

  // Validate form fields
  if (!form.value.currentPassword || !form.value.newPassword || !form.value.confirmPassword) {
    alert('Please fill in all fields.');
    return;
  }

  if (form.value.newPassword !== form.value.confirmPassword) {
    errors.value.confirmPassword = 'Passwords do not match.';
    return;
  }

  try {
    const token = localStorage.getItem('token');
    const response = await axios.put('/api/user/change-password',
      {
        currentPassword: form.value.currentPassword,
        newPassword: form.value.newPassword,
        newPassword_confirmation: form.value.confirmPassword,
      },
      {
        headers: {
          Authorization: `Bearer ${token}`,
        },
      }
    );
    
    alert('Password updated successfully!');
    // Redirect to login page
    router.push({ name: 'login' });
                                                   
  } catch (error) {
    console.error('Password update failed:', error);
    alert('There was an error updating your password.');
  }
};
</script>

<style scoped>
.page-container {
  display: flex;
}

.user-info {
  color: #f5f2f2;
  text-align: center;
}

.user-name {
  font-weight: bold;
  font-size: 16px;
}

.user-email {
  font-size: 12px;
  color: #e6e4e4;
}

.content-area {
  /* margin-left: 250px; */
  margin-top: 80px;
  flex-grow: 1;
  padding: 20px;
}

.settings-container {
  max-width: 1000px;
  background-color: #f8f9fa70;
  padding: 30px;
  border-radius: 4px;
  box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
  border:1px solid rgb(124, 170, 230);
}

.settings-header {
  text-align: center;
  font-size: 2rem;
  font-weight: 600;
  color: #333;
  margin-bottom: 30px;
}

.settings-content {
  display: flex;
  gap: 30px;
  justify-content: space-between;
}

.section-card {
  background-color: white;
  padding: 30px;
  border-radius: 10px;
  box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
  width: 48%;
  border:1px solid rgb(226, 220, 220);
}

.section-title {
  font-size: 1.5rem;
  font-weight: 600;
  margin-bottom: 20px;
  color: #333;
  font-family: serif;
}

.form-group {
  margin-bottom: 15px;
}

.form-group label {
  font-size: 14px;
  font-weight: 600;
  color: #555;
}

.form-group input {
  width: 100%;
  padding: 10px;
  font-size: 14px;
  border: 1px solid #ccc;
  border-radius: 5px;
}

.form-group input:focus {
  border-color: #4CAF50;
  outline: none;
}

.error-message {
  color: red;
  font-size: 12px;
  margin-top: 5px;
}

button[type="submit"] {
  width: 100%;
  padding: 12px;
  border: none;
  color: white;
  font-size: 16px;
  border-radius: 5px;
  cursor: pointer;
  margin-top: 20px;
}

.btn-secondary:hover {
  background-color: #5a6268;
}

.button_primary {
  background-color: #4e7dd4;
}

.button_primary:hover {
  background-color: #2b63cc;
}

@media (max-width: 768px) {
  .content-area {
    margin-left: 0;
  }

  .settings-content {
    flex-direction: column;
    gap: 20px;
  }

  .section-card {
    width: 100%;
  }
}

@media (max-width: 480px) {
  .settings-container {
    padding: 15px;
  }

  .section-title {
    font-size: 1.2rem;
  }

  .form-group input {
    font-size: 12px;
  }

  button[type="submit"] {
    font-size: 14px;
  }
}
</style>
