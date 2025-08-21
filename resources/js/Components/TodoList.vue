<template>
  <div class="todo-container">
    <h1>TODO LIST</h1>
    <div class="sub-container">
      <div class="add-todo">
        <!-- Task input or dropdown based on assignMode -->
        <div class="w-50">
          <!-- Show input if not assigning, show dropdown when assignMode is true -->
          <input v-if="!assignMode && !isEditing" v-model="newTodo" type="text" placeholder="Add a new task"
            class="todo-input" :class="{ 'input-error': showErrorTask }" />

          <select v-if="assignMode && !isEditing" v-model="selectedEmployee" class="todo-input">
            <option value="" disabled>Select Employee</option>
            <option v-for="employee in employees" :key="employee.id" :value="employee.id">
              {{ employee.first_name }} {{ employee.last_name }} ({{ employee.email }})
            </option>
          </select>
          <p v-if="showErrorTask" class="error-message">Task cannot be empty!</p>
        </div>

        <!-- Add Task and Assign Task Buttons -->
        <div class="buttons-container">
          <button @click="addTodo" class="add-button" :disabled="assignMode || isEditing">
            <i class="fas fa-plus"></i> Add Task
          </button>
          <button @click="toggleAssignMode" class="assign-button" :disabled="selectedTasks.length === 0 || isEditing">
            <i class="fas fa-user-plus"></i> Assign
          </button>
        </div>
      </div>

      <ul class="todo-list">
        <li v-for="todo in todos" :key="todo.id" class="todo-item">
          <input type="checkbox" v-model="selectedTasks" :value="todo.id" class="todo-checkbox" />

          <!-- Show task title or dropdown for assignment -->
          <span v-if="!todo.isEditing && !todo.isAssigned" class="todo-title">{{ todo.task }}</span>

          <!-- Show dropdown for employee selection when task is assigned -->
          <select v-if="todo.isAssigned && !todo.isEditing" v-model="todo.assigned_to" class="todo-input">
            <option value="" disabled>Select Employee</option>
            <option v-for="employee in employees" :key="employee.id" :value="employee.id">
              {{ employee.first_name }} {{ employee.last_name }} ({{ employee.email }})
            </option>
          </select>

          <!-- Edit task input field -->
          <input v-if="todo.isEditing" v-model="todo.task" class="todo-input" />

          <!-- Edit button -->
          <button @click="editTodo(todo)" class="edit-button" v-if="!todo.isEditing">
            <i class="fas fa-edit"></i>
          </button>

          <!-- Save button when editing -->
          <button @click="saveTodo(todo)" class="save-button" v-if="todo.isEditing">
            <i class="fas fa-save"></i>
          </button>

          <button @click="deleteTodo(todo.id)" class="delete-button">
            <i class="fas fa-trash"></i>
          </button>
        </li>
      </ul>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const todos = ref([]);
const newTodo = ref("");
const showErrorTask = ref(false);
const assignMode = ref(false); // Toggle between input field and dropdown
const selectedEmployee = ref(null);
const selectedTasks = ref([]);  // Store selected task IDs
const employees = ref([]);
const isEditing = ref(false);  // Track if any task is being edited


onMounted(() => {
  fetchTodos();
  fetchTodos();  // Fetch tasks from API and localStorage
 console.log("Loaded tasks:", todos.value); // Debug message

  fetchEmployeeEmails(); // Call this function when the component is mounted
});

// Fetch todos from API
const fetchTodos = async () => {
  try {
    const response = await axios.get('/api/todos', {
      headers: {
        Authorization: `Bearer ${localStorage.getItem('token')}`
      }
    });
    todos.value = response.data;  // Store tasks from API
  } catch (error) {
    console.error('Error fetching todos from API:', error);
    const localTasks = JSON.parse(localStorage.getItem('todos')) || [];
    todos.value
  }
};

// Fetch employee emails from the backend
const fetchEmployeeEmails = async () => {
  try {
    const response = await axios.get('/api/employee-emails', {
      headers: {
        Authorization: `Bearer ${localStorage.getItem('token')}`
      }
    });

    if (response.data && response.data.employees) {
      employees.value = response.data.employees;
    }
  } catch (error) {
    console.error('Error fetching employee emails:', error);
  }
};

// Add new task
const addTodo = async () => {
  if (!newTodo.value.trim()) {
    showErrorTask.value = true;
    console.log("Error: Task is empty before submission"); // Debug message
    return;
  }
  showErrorTask.value = false;

  console.log("Adding task:", newTodo.value);  // Log the task to be added

  try {
    // Store in localStorage immediately (just to test)
    const currentTodos = JSON.parse(localStorage.getItem('todos')) || [];
    currentTodos.push({ task: newTodo.value, assigned_to: null });
    localStorage.setItem('todos', JSON.stringify(currentTodos));

    // Now make the API request
    const response = await axios.post('/api/todos', {
      task: newTodo.value
    }, {
      headers: {
        Authorization: `Bearer ${localStorage.getItem('token')}`
      }
    });

    // Log the response to see if the task is added properly
    console.log("API Response:", response.data);

    // Push the entire task object to todos array after API response
    todos.value.push(response.data);  // Push the entire task object

    newTodo.value = "";  // Clear the input field
    assignMode.value = false; // Reset assign mode after adding task
  } catch (error) {
    console.error('Error adding todo:', error);
  }
};

// Toggle between input and dropdown
const toggleAssignMode = () => {
  assignMode.value = !assignMode.value;
};

// Edit task
const editTodo = (todo) => {
  todo.isEditing = true;
  isEditing.value = true; // Set the global editing flag to true
};

// Save edited task
const saveTodo = async (todo) => {
  try {
    const response = await axios.put(`/api/todos/${todo.id}`, { task: todo.task }, {
      headers: {
        Authorization: `Bearer ${localStorage.getItem('token')}`
      }
    });
    todo.isEditing = false;
    todo.task = response.data.task;
    isEditing.value = false; // Reset global editing flag after save
  } catch (error) {
    console.error('Error saving todo:', error);
  }
};

// Delete task
const deleteTodo = async (id) => {
  try {
    await axios.delete(`/api/todos/${id}`, {
      headers: {
        Authorization: `Bearer ${localStorage.getItem('token')}`
      }
    });
    todos.value = todos.value.filter(todo => todo.id !== id);
  } catch (error) {
    console.error('Error deleting todo:', error);
  }
};

// Assign Task to Employee
const assignTask = async () => {
  if (!selectedEmployee.value) {
    alert('Please select an employee!');
    return;
  }
  try {
    const response = await axios.post(`/api/todos/${selectedTasks.value}/assign`, {
      employee_id: selectedEmployee.value,
    });

    todos.value = todos.value.map(task => {
      if (task.id === selectedTasks.value) {
        task.assigned_to = selectedEmployee.value; // Assign employee
      }
      return task;
    });

    alert('Task assigned successfully!');
  } catch (error) {
    console.error('Error assigning task:', error);
  }
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
  max-width: 550px;
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

.todo-input {
  width: 100%;
  padding: 8px;
  font-size: 15px;
  border: 2px solid #ddd;
  border-radius: 5px;
  outline: none;
  transition: border-color 0.3s ease;
}

.todo-input:focus {
  border-color: #4CAF50;
}


input::placeholder {
  color: rgba(58, 57, 57, 0.5);
  transition: color 0.3s ease;
  font-size: 16px;
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

.add-todo {
  display: flex;
  align-items: center;
  margin-bottom: 10px;
}

.buttons-container {
  display: flex;
  gap: 10px;
  align-items: center;
}

.todo-container {
  width: 100%;
  max-width: 650px;
  background-color: #f8f9faa1;
  padding: 40px;
  border-radius: 4px;
  box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
  border: 1px solid rgb(118, 165, 209);
  overflow: hidden;
  box-sizing: border-box;
  height: auto;
  margin-left: 137px;
  margin-top: 100px;
}

.sub-container {
  padding: 10px;
  background: linear-gradient(to bottom, #336597, #74aee0);
  box-shadow: 0 4px 8px rgba(15, 15, 15, 0.1);
  border-radius: 10px;
}

.todo-title {
  color: #333;
  font-size: 2em;
  margin-top: 8px;
}

h1 {
  text-align: center;
  color: #333;
  font-size: 1.4em;
  font-weight: 600;
  font-family: serif;
  border-bottom: 1px solid #679fd8;
}

.add-todo {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
}

.add-button {
  padding: 8px;
  border: 0px;
  color: rgb(10, 10, 10);
  border-radius: 5px;
  cursor: pointer;
  justify-content: center;
  align-items: center;
  gap: 16px;

  background-color: #ddd;
}

.add-button:hover {
  background-color: #45a049;
  color: rgb(247, 246, 246);
}

.add-button i {
  font-size: 13px;
}

.todo-list {
  list-style-type: none;
  padding: 0;
  margin: 0;
}

.todo-item {
  display: flex;
  justify-content: space-between;
  background-color: #fff;
  padding: 10px;
  margin-bottom: 10px;
  border-radius: 8px;
  box-shadow: 0 10px 10px rgba(0, 0, 0, 0.1);
}

.todo-checkbox {
  margin-right: 17px;
}

.todo-title {
  flex-grow: 1;
  font-size: 15px;
}

.todo-title.completed {
  text-decoration: line-through;
  color: #888;
}

.delete-button {

  padding: 8px 12px;
  color: rgb(36, 35, 35);
  border: 0px;
  border-radius: 4px;
  cursor: pointer;
  display: flex;
  justify-content: center;
  align-items: center;
}

.edit-button {
  padding: 8px 12px;
  color: rgb(36, 35, 35);
  border: 0px;
  border-radius: 4px;
  cursor: pointer;
  display: flex;
  justify-content: center;
  align-items: center;
}

.edit-button {
  margin-right: 10px;
}

.delete-button:hover {
  background-color: rgb(199, 54, 54);
  color: #f9f9f9;
}

.edit-button:hover {
  background-color: #45a049;
  color: #f9f9f9;
}

.edit-button:hover i {
  color: #f9f9f9;
}

.save-button {
  padding: 8px 12px;
  background-color: #f7f8f7;
  color: rgb(44, 43, 43);
  border-radius: 4px;
  cursor: pointer;
  display: flex;
  justify-content: center;
  align-items: center;
  border: 0px;
}

.save-button:hover {
  background-color: #45a049;
}

.save-button i {
  font-size: 14px;
}

.assign-button {
  padding: 8px 12px;
  background-color: #4CAF50;
  color: white;
  border-radius: 4px;
  cursor: pointer;
  display: flex;
  justify-content: center;
  align-items: center;
  border: 0px;
}

.assign-button:hover {
  background-color: #45a049;
}

.assign-button i {
  font-size: 14px;
}

select {
  width: 100%;
  padding: 10px;
  margin: 10px 0;
  border-radius: 5px;
  border: 1px solid #ccc;
}

.assign-button {
  background-color: #45a049;
}

.cancel-button {
  background-color: #f44336;
}

.error-message {
  color: #a32d20;
  font-size: 0.9em;
  margin-top: 0px;
  margin-bottom: 15px;
}

.input-error {
  border-color: #e74c3c !important;
}
</style>