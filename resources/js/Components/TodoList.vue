<template>
  <div class="todo-container">
    <h1>TODO LIST</h1>
    <div class="sub-container">
      <div class="add-todo">
        <!-- Add Task Input and Button -->
        <input v-model="newTodo" type="text" placeholder="Add a new task" @keyup.enter="addTodo"
          @input="showError = false" class="todo-input" :class="{ 'input-error': showError }" />

        <!-- Add Button -->
        <button @click="addTodo" class="add-button" :disabled="!newTodo.trim()">
          <i class="fas fa-plus"></i> Add
        </button>

        <!-- Assign Button - only enabled when tasks are selected -->
        <button @click="openAssignModal" class="assign-button" :disabled="!selectedTasks.length">
          <i class="fas fa-user-plus"></i> Assign
        </button>
      </div>

      <p v-if="showError" class="error-message">Task cannot be empty!</p>
      
      <ul class="todo-list">
        <!-- Loop through todos and display them with checkboxes -->
        <li v-for="todo in todos" :key="todo.id" class="todo-item">
          <input type="checkbox" v-model="selectedTasks" :value="todo.id" class="todo-checkbox" />
          <span :class="{ completed: todo.completed }" class="todo-title">{{ todo.title }}</span>

          <!-- Edit/Delete buttons -->
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
      
      <!-- Employee Dropdown -->
      <label for="employee">Employee</label>
      <select v-model="selectedEmployee" id="employee">
        <option value="" disabled>Select Employee</option>
        <!-- Fetch employee names and emails from the backend -->
        <option v-for="emp in employees" :key="emp.id" :value="emp.id">
          {{ emp.name }} ({{ emp.email }})
        </option>
      </select>

      <!-- Department Dropdown -->
      <label for="department">Department</label>
      <select v-model="selectedDepartment" id="department">
        <option value="" disabled>Select Department</option>
        <option v-for="dep in departments" :key="dep.id" :value="dep.id">{{ dep.name }}</option>
      </select>

      <!-- Assign Button -->
      <button @click="assignTask" class="assign-button">Assign</button>
      <button @click="closeAssignModal" class="cancel-button">Cancel</button>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const todos = ref([]);
const newTodo = ref("");
const showError = ref(false);
const assignModalOpen = ref(false);
const selectedEmployee = ref(null);
const selectedDepartment = ref(null);
const employees = ref([]);
const departments = ref([]);
const selectedTasks = ref([]); // Array to hold selected task IDs

// Fetch existing todos when component mounts
onMounted(() => {
  fetchTodos();
  fetchDropdownData();
});

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

const fetchDropdownData = async () => {
  try {
    // Fetch employee data
    const empRes = await axios.get('/api/employees');
    employees.value = empRes.data;

    // Fetch department data
    const depRes = await axios.get('/api/departments');
    departments.value = depRes.data;
  } catch (error) {
    console.error('Error fetching dropdown data:', error);
  }
};

// Add a new todo
const addTodo = async () => {
  if (!newTodo.value.trim()) {
    showError.value = true;
    return;
  }
  showError.value = false;
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

// Open the assign modal when the Assign button is clicked
const openAssignModal = () => {
  if (selectedTasks.value.length === 0) {
    alert('Please select at least one task');
    return;
  }
  assignModalOpen.value = true;
};

// Close the assign modal
const closeAssignModal = () => {
  assignModalOpen.value = false;
  selectedEmployee.value = null;
  selectedDepartment.value = null;
};

// Assign tasks to employee
const assignTask = async () => {
  if (!selectedEmployee.value || !selectedDepartment.value) {
    alert('Please select both employee and department.');
    return;
  }

  try {
    // Assign each selected task to the employee
    for (let taskId of selectedTasks.value) {
      const response = await axios.post(`/api/todos/${taskId}/assign`, {
        assigned_to: selectedEmployee.value,
        department_id: selectedDepartment.value
      }, {
        headers: {
          Authorization: `Bearer ${localStorage.getItem('token')}`
        }
      });

      // Optionally, update the UI after each task is assigned
      alert(`Task with ID ${taskId} assigned successfully!`);
    }
    closeAssignModal();
    fetchTodos(); // Refresh the todo list to show the assigned tasks
  } catch (error) {
    console.error('Error assigning task:', error);
  }
};

// Delete a todo from the list
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
/* Your existing styles for styling */
</style>


<style scoped>
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
  width: 76%;
  padding: 10px;
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
  width: 20%;
  padding: 8px;
  border:0px;
  color: rgb(10, 10, 10);
  border-radius: 5px;
  cursor: pointer;
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 16px;
  margin-left: 10px;
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
  background-color: rgb(245, 38, 38);
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
  margin-left: 10px; 
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
