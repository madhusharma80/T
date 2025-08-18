<template>
  <div class="todo-container">
    <h1>TODO LIST</h1>
    <div class="sub-container">
      <div class="add-todo">
        <!-- Task input or dropdown based on assignMode -->
        <div v-if="!assignMode" class="w-50">
          <input v-model="newTodo" type="text" placeholder="Add a new task" class="todo-input"
            :class="{ 'input-error': showErrorTask }" />
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

const todos = ref([]);
const newTodo = ref("");
const showErrorTask = ref(false);  // For task input error (only show after clicking Add Task)
const showErrorEmployee = ref(false);  // For employee dropdown error (only show after clicking Assign Task)
const assignModalOpen = ref(false);
const selectedEmployee = ref(null);
const selectedDepartment = ref(null);
const employees = ref([]);
const departments = ref([]);
const selectedTasks = ref([]);
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
  fetchTodos();
  fetchDropdownData();
});

// Fetch todos from API
const fetchTodos = async () => {
  try {
    const response = await axios.get('/api/todos', {
      headers: {
        Authorization: `Bearer ${localStorage.getItem('token')}`
      }
    });
    todos.value = response.data;
  } catch (error) {
    console.error('Error fetching todos:', error);
  }
};

// Fetch employees and departments from the backend
const fetchDropdownData = async () => {
  try {
    const response = await axios.get('/api/fetchDropdownData');
    employees.value = response.data.employees;
    departments.value = response.data.departments;
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
        Authorization: `Bearer ${localStorage.getItem('token')}`
      }
    });
    todos.value.push(response.data);
    newTodo.value = "";
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

  assignModalOpen.value = true;
  assignMode.value = true;  // Switch to dropdown mode
  fetchEmployeeEmails();  // Fetch employee emails when modal is opened
};

// Fetch employee emails for the assign modal dropdown
const fetchEmployeeEmails = async () => {
  try {
    const response = await axios.get('/api/employees/emails', {
      headers: {
        'Authorization': `Bearer ${localStorage.getItem('token')}`
      }
    });
    console.log('Fetched employee data:', response.data);  // Log to check response
    employees.value = response.data.employees;  // Populate employees list
  } catch (error) {
    console.error('Error fetching employee emails:', error);
  }
};

const closeAssignModal = () => {
  assignModalOpen.value = false;
  selectedEmployee.value = null;
  selectedDepartment.value = null;
  assignMode.value = false;  // Close the dropdown and return to task input field
};

// Assign task
const assignTask = async () => {
  if (!selectedEmployee.value || !selectedDepartment.value) {
    showErrorEmployee.value = true;
    alert('Please select both employee and department');
    return;
  }

  try {
    // Loop through the selected tasks to assign them one by one
    for (let taskId of selectedTasks.value) {
      const response = await axios.post(`/api/todos/${taskId}/assign`, {
        assigned_to: selectedEmployee.value,  // Selected employee's ID
        department_id: selectedDepartment.value  // Selected department's ID
      }, {
        headers: {
          Authorization: `Bearer ${localStorage.getItem('token')}`
        }
      });

      // Now update the assigned task in the employee list
      const assignedTask = response.data; // This contains task info and assigned employee

      // Update the employees array to reflect the task assignment in the employee task list
      const employeeIndex = employees.value.findIndex(employee => employee.email === assignedTask.email);
      if (employeeIndex !== -1) {
        employees.value[employeeIndex].tasks.push(assignedTask);  // Add the assigned task to the employee's task list
      }

      alert(`Task with ID ${taskId} assigned successfully!`);
    }

    // After assigning the task, set assignMode to false to show the input field
    assignMode.value = false;  // Switch back to the task input field (hide dropdown)

    // Close the modal
    closeAssignModal();

    // Fetch the updated list of tasks
    fetchTodos();  // Refresh the todo list

    // Clear the selected employee and department
    selectedEmployee.value = null;
    selectedDepartment.value = null;

  } catch (error) {
    console.error('Error assigning task:', error);
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
  max-width: 450px;
  /* Maximum width for form */
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
  font-size: 1.5em;
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
  font-size: 14.7px;
}

.todo-title.completed {
  text-decoration: line-through;
  color: #888;
}

.delete-button {

  padding: 8px 12px;
  color: rgb(12, 12, 12);
  border-color: #e4e0de;
  border-radius: 4px;
  border:0px;
  cursor: pointer;
  display: flex;
  justify-content: center;
  align-items: center;
}

.edit-button {
  padding: 8px 12px;
  color: rgb(14, 13, 13);
  border:0px;
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
  color: rgb(19, 18, 18);
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