<template>
  <div class="todo-container">
    <h1>TODO LIST</h1>
    <div class="sub-container">
      <div class="add-todo">
        <!-- Task input or dropdown based on assignMode -->
        <div v-if="!assignMode">
          <input v-model="newTodo"
                 type="text"
                 placeholder="Add a new task"
                 class="todo-input"
                 :class="{ 'input-error': showErrorTask }" />
          <!-- Error Message for Empty Task Input (only shows after clicking Add Task) -->
          <p v-if="showErrorTask" class="error-message">Task cannot be empty!</p>
        </div>

        <!-- When in Assign Mode, replace input with dropdown -->
        <div v-if="assignMode">
          <select v-model="selectedEmployee" class="todo-input">
            <option value="" disabled>Select Employee</option>
            <option v-for="employee in employees" :key="employee.id" :value="employee.id">
              {{ employee.first_name }} {{ employee.last_name }} ({{ employee.email }})
            </option>
          </select>
          <!-- Error Message for Empty Employee Selection (only shows after clicking Assign Task) -->
          <p v-if="showErrorEmployee" class="error-message">Please select an employee!</p>
        </div>
        
        <!-- Add Task and Assign Task Buttons (aligned side by side) -->
        <div class="buttons-container">
          <button @click="addTodo" class="add-button" :disabled="assignMode">
            <i class="fas fa-plus"></i> Add Task
          </button>
          <button @click="openAssignModal" class="assign-button" :disabled="!selectedTasks.length">
            <i class="fas fa-user-plus"></i> Assign 
          </button>
        </div>
      </div>

      <ul class="todo-list">
        <li v-for="todo in todos" :key="todo.id" class="todo-item">
          <input type="checkbox" v-model="selectedTasks" :value="todo.id" class="todo-checkbox" />
          <span :class="{ completed: todo.completed }" class="todo-title">{{ todo.title }}</span>
          <button @click="editTodo(todo)" class="edit-button" v-if="!todo.isEditing">
            <i class="fas fa-edit"></i>
          </button>
          <button @click="deleteTodo(todo.id)" class="delete-button">
            <i class="fas fa-trash"></i>
          </button>
        </li>
      </ul>
    </div>

    <!-- Assign Modal -->
    <div v-if="assignModalOpen" class="modal">
      <h3>Assign Task</h3>
      <label for="employee">Employee</label>
      <select v-model="selectedEmployee" @change="validateEmployee" class="todo-input">
        <option value="" disabled>Select Employee</option>
        <option v-for="employee in employees" :key="employee.id" :value="employee.id">
          {{ employee.first_name }} {{ employee.last_name }} ({{ employee.email }})
        </option>
      </select>
      <p v-if="showErrorEmployee && !selectedEmployee" class="error-message">Please select an employee!</p>

      <label for="department">Department</label>
      <select v-model="selectedDepartment" id="department">
        <option value="" disabled>Select Department</option>
        <option v-for="dep in departments" :key="dep.id" :value="dep.id">{{ dep.name }}</option>
      </select>

      <button @click="assignTask" class="assign-button" :disabled="!selectedEmployee || !selectedDepartment">
        Assign Task
      </button>
      <button @click="closeAssignModal" class="cancel-button">Cancel</button>
    </div>
  </div>
</template>



<script setup>
import { ref, onMounted, watch } from 'vue';
import axios from 'axios';

const todos = ref([]); // List of todos
const newTodo = ref(""); // New task input
const showErrorTask = ref(false);  // For task input error (only show after clicking Add Task)
const showErrorEmployee = ref(false);  // For employee dropdown error (only show after clicking Assign Task)
const assignModalOpen = ref(false); // For showing the assign task modal
const selectedEmployee = ref(null); // For storing selected employee ID
const selectedDepartment = ref(null); // For storing selected department ID
const employees = ref([]); // List of employees for dropdown
const departments = ref([]); // List of departments for dropdown
const selectedTasks = ref([]); // List of selected tasks for assignment
const assignMode = ref(false);  // Track if the input field should show the dropdown or task input

// Watchers for input validation (errors only trigger after clicking respective button)
watch(newTodo, () => {
  if (!newTodo.value.trim()) {
    showErrorTask.value = false;  // Prevent error message while typing
  }
});

watch(selectedEmployee, () => {
  if (!selectedEmployee.value) {
    showErrorEmployee.value = false;  // Prevent error message while typing or selecting
  }
});

onMounted(() => {
  fetchTodos(); // Fetch existing todos
  fetchDropdownData(); // Fetch employee and department dropdown data
});

const fetchTodos = async () => {
  try {
    const response = await axios.get('/api/todos', {
      headers: {
        Authorization: `Bearer ${localStorage.getItem('token')}` // Add token for authorization
      }
    });
    todos.value = response.data;
  } catch (error) {
    console.error('Error fetching todos:', error);
  }
};

// Fetch employees and departments for dropdown
const fetchDropdownData = async () => {
  try {
    const response = await axios.get('/api/fetchDropdownData');
    employees.value = response.data.employees; // Set employee list for dropdown
    departments.value = response.data.departments; // Set department list for dropdown
  } catch (error) {
    console.error('Error fetching dropdown data:', error);
  }
};

// Add new task
const addTodo = async () => {
  if (!newTodo.value.trim()) {
    showErrorTask.value = true;  // Show error if task is empty when Add Task is clicked
    return;
  }
  showErrorTask.value = false;
  
  try {
    const response = await axios.post('/api/todos', {
      title: newTodo.value
    }, {
      headers: {
        Authorization: `Bearer ${localStorage.getItem('token')}` // Add token for authorization
      }
    });
    todos.value.push(response.data);
    newTodo.value = ""; // Clear the input field
  } catch (error) {
    console.error('Error adding todo:', error);
  }
};

// Open the assign modal when tasks are selected
const openAssignModal = () => {
  if (selectedTasks.value.length === 0) {
    alert('Please select at least one task');  // Show error if no task is selected
    return;
  }

  assignModalOpen.value = true; // Open the modal
  assignMode.value = true;  // Switch to dropdown mode
};

// Close the assign modal
const closeAssignModal = () => {
  assignModalOpen.value = false;
  selectedEmployee.value = null;  // Reset employee selection
  selectedDepartment.value = null;  // Reset department selection
  assignMode.value = false;  // Close the dropdown and return to task input field
};

// Assign tasks to the selected employee and department
const assignTask = async () => {
  if (!selectedEmployee.value || !selectedDepartment.value) {
    showErrorEmployee.value = true;  // Show error if employee or department is not selected
    alert('Please select both employee and department');
    return;
  }

  try {
    for (let taskId of selectedTasks.value) {
      const response = await axios.post(`/api/todos/${taskId}/assign`, {
        assigned_to: selectedEmployee.value,
        department_id: selectedDepartment.value
      }, {
        headers: {
          Authorization: `Bearer ${localStorage.getItem('token')}` // Add token for authorization
        }
      });

      alert(`Task with ID ${taskId} assigned successfully!`);
    }
    closeAssignModal(); // Close the modal after assignment
    fetchTodos();  // Refresh the todo list
  } catch (error) {
    console.error('Error assigning task:', error);
  }
};

// Delete task
const deleteTodo = async (id) => {
  try {
    await axios.delete(`/api/todos/${id}`, {
      headers: {
        Authorization: `Bearer ${localStorage.getItem('token')}` // Add token for authorization
      }
    });
    todos.value = todos.value.filter(todo => todo.id !== id); // Remove deleted task from the list
  } catch (error) {
    console.error('Error deleting todo:', error);
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
  max-width: 450px; /* Maximum width for form */
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
  width: 50%;
  max-width: 700px;
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
  font-size: 1.5em;
  font-weight: 600;
  font-family: serif;
  border-bottom: 1px solid #679fd8;
}
.add-todo {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 5px;
}


.todo-input {
  width: 190%;
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
.add-button {
  padding: 8px;
  border:0px;
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
  font-size: 14px;
}

.todo-title.completed {
  text-decoration: line-through;
  color: #888;
}

.delete-button {
  
  padding: 8px 12px;
  color: white;
  border-color: #d34a14;
  border-radius: 4px;
  cursor: pointer;
  display: flex;
  justify-content: center;
  align-items: center;
}

.edit-button {
  padding: 8px 12px;
  color: white;
  border-color: #45a049;
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

.delete-button:hover i {
  color: #f9f9f9;
}

.delete-button i,
.edit-button i {
  font-size: 15px;
  color: #333;
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
  color:rgb(19, 18, 18);
}

.assign-button i {
  font-size: 14px;
}

.modal {
  padding: 20px;
  background: white;
  border: 1px solid #ccc;
  border-radius: 10px;
  max-width: 400px;
  margin: 50px auto;
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