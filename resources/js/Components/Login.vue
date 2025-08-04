<template>
  <div class="container mt-4">
    <div class="row justify-content-center">
      <div class="col-md-4">
        <form @submit.prevent="login">
          <h4>LOGIN</h4>
          <div class="mb-4">
            <input 
              v-model="email" 
              type="email" 
              class="form-control" 
              placeholder="Email"/>
            <span v-if="errors.email" class="text-danger">{{ errors.email[0] }}</span>
          </div>
          <div class="mb-4">
            <input 
              v-model="password" 
              type="password" 
              class="form-control" 
              placeholder="Password"/>
            <span v-if="errors.password" class="text-danger">{{ errors.password[0] }}</span>
          </div>
          <div class="d-flex justify-content-between">
            <button type="submit" class="btn btn-primary w-48">Login</button>
            <button @click="goToSignup" type="button" class="btn btn-secondary w-48">Signup</button>
          </div>
          <div v-if="message" :class="[ 
            'alert', 
            messageType === 'success' ? 'alert-success' : 'alert-danger', 'mt-3']">
            {{ message }}
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';

const email = ref('demo@gmail.com');
const password = ref('12345678');
const errors = ref({});
const message = ref('');
const messageType = ref('');

const router = useRouter();

const validateForm = () => {
  errors.value = {}; 

  let isValid = true;

  if (!email.value) {
    errors.value.email = ['Email is required'];
    isValid = false;
  } else if (!/\S+@\S+\.\S+/.test(email.value)) {
    errors.value.email = ['Please enter a valid email'];
    isValid = false;
  }

  if (!password.value) {
    errors.value.password = ['Password is required'];
    isValid = false;
  } else if (password.value.length < 6) {
    errors.value.password = ['Password must be at least 6 characters'];
    isValid = false;
  }

  return isValid;
};

watch(email, () => {
  if (email.value && !/\S+@\S+\.\S+/.test(email.value)) {
    errors.value.email = ['Please enter a valid email'];
  } else {
    errors.value.email = null; 
  }
});

watch(password, () => {
  if (password.value.length < 6) {
    errors.value.password = ['Password must be at least 6 characters'];
  } else {
    errors.value.password = null; 
  }
});

const login = async () => {
  message.value = '';
  messageType.value = '';


  if (!validateForm()) {
    return;
  }

  try {
    const response = await axios.post('/api/login', {
      email: email.value,
      password: password.value,
     
    });

    if (response.data.token) {
  
      localStorage.setItem('token', response.data.token);

      localStorage.setItem('userName', response.data.user.name); 
      localStorage.setItem('userEmail', response.data.user.email);
      localStorage.setItem('userPassword', response.data.user.password);

      axios.defaults.headers.common['Authorization'] = `Bearer ${response.data.token}`;
      
      router.push('/welcome');
    } else {
      message.value = 'Login failed. Please check your credentials.';
      messageType.value = 'error';
    }
  } catch (error) {
    console.error('Login error:', error);
    message.value = 'An error occurred. Please try again later.';
    messageType.value = 'error';
  }
};

// Navigate to Signup(register) page
const goToSignup = () => {
  router.push('/register');
};
</script>

<style scoped>
.text-danger {
  font-size: 12px;
  margin-left: 14px;
}

form {
  position: fixed;
  top: 30%;
  left: 50%;
  transform: translate(-50%, -50%);
  width: 100%;
  max-width: 400px; /* Maximum width for form */
  padding: 30px;
  background-color: rgba(3, 3, 3, 0.7);
  box-sizing: border-box;
  box-shadow: 0 15px 25px rgba(51, 51, 51, 0.6);
  border-radius: 10px;
}

form input {
  border: none;
  border-bottom: 1px solid #fff;
  outline: none;
  background: transparent;
  color: #fff;
}

input::placeholder {
  color: rgba(255, 255, 255, 0.5);
  transition: color 0.3s ease;
  font-size: 14px;
}

input:focus {
  border: none;
  outline: none;
  box-shadow: none;
}

h4 {
  text-align: center;
  color: white;
}

.w-48 {
  width: 48%;
}

.alert {
  margin-top: 20px;
  padding: 10px;
  border-radius: 5px;
}

.alert-success {
  background-color: #d4edda;
  color: #155724;
}

.alert-danger {
  background-color: #f8d7da;
  color: #721c24;
}

@media (max-width: 576px) {
  form {
    padding: 20px; 
  }

  h4 {
    font-size: 20px; 
  }

  .w-48 {
    width: 100%; 
    margin-bottom: 10px; 
  }

  .alert {
    font-size: 12px; 
  }
}

@media (max-width: 768px) and (min-width: 577px) {
  .w-48 {
    width: 48%; 
  }
}
</style>
