<template>
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-5">
        <form @submit.prevent="register">
          <h4>REGISTER</h4>
          <div class="mb-4">
            <input type="text" class="form-control" id="name" placeholder="Name" v-model="form.name" @input="validateForm" />
            <span v-if="errors.name" class="text-danger">{{ errors.name[0] }}</span>
          </div>

          <div class="mb-4">
            <input type="email" class="form-control" id="email" placeholder="Email Address" v-model="form.email" @input="validateForm" />
            <span v-if="errors.email" class="text-danger">{{ errors.email[0] }}</span>
          </div>

          <div class="mb-4">
            <input type="password" class="form-control" id="password" placeholder="Enter Password"
              v-model="form.password" @input="validateForm" />
            <span v-if="errors.password" class="text-danger">{{ errors.password[0] }}</span>
          </div>

          <div class="mb-4">
            <input type="password" class="form-control" id="password_confirmation" placeholder="Confirm Password"
              v-model="form.password_confirmation" @input="validateForm" />
            <span v-if="errors.password_confirmation" class="text-danger">{{ errors.password_confirmation[0] }}</span>
          </div>

          <div class="mt-4 text-center">
            <button :disabled="isFormInvalid" type="submit" class="btn btn-primary w-100">Sign Up</button>
            <button @click="goToLogin" type="button" class="btn btn-link mt-3">Already have an account? Login</button>
          </div>

          <div v-if="message" :class="['alert', messageType === 'success' ? 'alert-success' : 'alert-danger', 'mt-3']">
            {{ message }}
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { reactive, ref, computed } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';

const router = useRouter();

const form = reactive({
  name: '',
  email: '',
  password: '',
  password_confirmation: '' 
});

const errors = ref({});
const message = ref('');
const messageType = ref('');

const isFormInvalid = computed(() => {
  return !form.name || !form.email || !form.password || form.password !== form.password_confirmation;
});

// Register function
const register = async () => {
  errors.value = {};
  message.value = '';
  messageType.value = '';


  if (!form.name) errors.value.name = ['Name is required'];
  else if (form.name.length < 3) errors.value.name = ['Name must be between 3 and 15 characters'];
  else if (form.name.length > 15) errors.value.name = ['Name must not exceed 15 characters'];

  if (!form.email) errors.value.email = ['Email is required'];
  else if (!/^\S+@\S+\.\S+$/.test(form.email)) errors.value.email = ['Enter a valid email address'];

  if (!form.password) errors.value.password = ['Password is required'];
  else if (form.password.length < 8) errors.value.password = ['Password must be at least 8 characters'];

  // Validate password confirmation
  if (form.password !== form.password_confirmation) {
    errors.value.password_confirmation = ['Password confirmation does not match.'];
  }

  if (Object.keys(errors.value).length > 0) {
    message.value = 'Please fix the validation errors.';
    messageType.value = 'error';
    return;
  }

  try {
    const res = await axios.post('/api/register', form);

    messageType.value = 'success';
    message.value = 'Registration successful!';
    router.push('/login'); 
  } catch (e) {
    errors.value = e.response?.data?.errors || {};
    message.value = e.response?.data?.message || 'Registration failed.';
    messageType.value = 'error';
  }
};

const goToLogin = () => {
  router.push('/login');
};

const validateForm = () => {
  errors.value = {};  

  if (!form.name) errors.value.name = ['Name is required'];
  else if (form.name.length < 3) errors.value.name = ['Name must be between 3 and 15 characters'];
  else if (form.name.length > 15) errors.value.name = ['Name must not exceed 15 characters'];

  if (!form.email) errors.value.email = ['Email is required'];
  else if (!/^\S+@\S+\.\S+$/.test(form.email)) errors.value.email = ['Enter a valid email address'];
  
  if (!form.password) errors.value.password = ['Password is required'];
  else if (form.password.length < 8) errors.value.password = ['Password must be at least 8 characters'];

  if (form.password !== form.password_confirmation) {
    errors.value.password_confirmation = ['Password confirmation does not match.'];
  }
};
</script>

<style scoped>
form {
  position: fixed; 
  top: 40%; 
  left: 50%; 
  width: 400px;
  padding: 40px;
  transform: translate(-50%,-50%); 
  background-color: rgba(5, 5, 5, 0.7); 
  box-sizing: border-box;
  box-shadow: 0 15px 50px rgba(0,0,0,0.6); 
  border-radius: 10px;
}

form input {
  border: none;
  border-bottom: 1px solid #fff;
  outline: none;
  background: transparent;
  color: #fff;
}  

input::placeholder,
textarea::placeholder {
  color: rgba(255, 255, 255, 0.5); 
  transition: color 0.3s ease; 
  font-size: 14px;
}    

input:focus {
  border: none;           
  outline: none;          
  box-shadow: none;
}

.text-danger {
  font-size: 12px; 
  text-align: left;
  margin-left: 14px;
}

h4 {
  text-align: center;
  color:#fff;
}

.mt-4 {
  margin-top: 16px;
}

.btn-link {
  background: none;
  border: none;
  color: #007bff;
  padding: 0;
  text-decoration: underline;
}

.btn-primary:disabled {
  background-color: #cccccc;
  cursor: not-allowed;
}
@media (max-width: 576px) {
  form {
    width: 90%; 
    padding: 20px; 
  }

  h4 {
    font-size: 18px; 
  }

  .w-100 {
    width: 100%; 
  }

  .btn-link {
    font-size: 14px; 
  }
}

@media (max-width: 768px) and (min-width: 577px) {
  form {
    width: 80%; 
  }
}
</style>
