<template>
  <div class="container">
    <h1 class="title">EMPLOYEE TASK MANAGEMENT - TO-DO LIST</h1>

    <div class="main-layout">
      <!-- EMPLOYEE TABLE -->
      <div class="table-section">
        <table class="table">
          <thead>
            <tr>
              <th>s.no</th>
              <th>
                <input v-model="newEmployee.first_name" class="custom_nameInput" type="text" placeholder="First Name" />
              </th>
              <th>
                <input v-model="newEmployee.last_name" class="custom_nameInput" type="text" placeholder="Last Name" />
              </th>
              <th>
                <input v-model="newEmployee.email" class="custom_nameInput" type="email" placeholder="Email" />
              </th>
              <th>
                <select v-model="newEmployee.department_id" class="custom_dropdown">
                  <option value="">Department</option>
                  <option v-for="department in departments" :key="department.id" :value="department.id">{{
                    department.name }}</option>
                </select>
              </th>
              <th>
                <select v-model="newEmployee.designation_id" class="custom_dropdown">
                  <option value="">Designation</option>
                  <option v-for="designation in designations" :key="designation.id" :value="designation.id">{{
                    designation.name }}</option>
                </select>
              </th>
              <th>
                <button class="btn btn-primary w-100 add-button" @click="addEmployee">
                  <i class="fas fa-plus"></i>Add
                </button>
              </th>
            </tr>
          </thead>

          <tbody>
            <tr v-for="(employee, index) in paginatedEmployees.data" :key="employee.id">
              <td>{{ index + 1 + ((paginatedEmployees.current_page - 1) * paginatedEmployees.per_page) }}.</td>
              <td>
                <span v-if="!employee.isEditing">{{ employee.first_name }}</span>
                <input v-else v-model="employee.first_name" class="custom_input" type="text" />
              </td>
              <td>
                <span v-if="!employee.isEditing">{{ employee.last_name }}</span>
                <input v-else v-model="employee.last_name" class="custom_input" type="text" />
              </td>
              <td>
                <span v-if="!employee.isEditing">{{ employee.email }}</span>
                <input v-else v-model="employee.email" class="custom_input" type="email" />
              </td>
              <td>
                <span v-if="!employee.isEditing">{{ employee.department?.name || 'No Department' }}</span>
                <select v-else v-model="employee.department_id" class="custom_dropdown">
                  <option value="">Select Department</option>
                  <option v-for="department in departments" :key="department.id" :value="department.id">{{
                    department.name }}</option>
                </select>
              </td>
              <td>
                <span v-if="!employee.isEditing">{{ employee.designation?.name || 'No Designation' }}</span>
                <select v-else v-model="employee.designation_id" class="custom_dropdown">
                  <option value="">Select Designation</option>
                  <option v-for="designation in designations" :key="designation.id" :value="designation.id">{{
                    designation.name }}</option>
                </select>
              </td>
              <td>
                <div class="button-group">
                  <!-- View Detail Button -->
                  <button class="btn btn-info view-button" @click="viewEmployee(employee)">
                    <i class="fas fa-eye"></i>
                  </button>
                  <!-- Edit Button -->
                  <button v-if="!employee.isEditing" class="btn edit-button" @click="editEmployee(employee)">
                    <i class="fas fa-edit"></i>
                  </button>
                  <!-- Save Button -->
                  <button v-if="employee.isEditing" class="btn btn-success save-button"
                    @click="saveEmployee(employee, index)">
                    <i class="fas fa-save"></i>
                  </button>
                  <!-- Delete Button -->
                  <button @click="deleteEmployee(index, employee.id)" class="delete-button">
                    <i class="fas fa-trash"></i>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>

        <!-- Pagination Controls -->
        <div class="pagination">
          <button @click="changePage(paginatedEmployees.current_page - 1)"
            :disabled="paginatedEmployees.current_page === 1">
            <i class="fas fa-chevron-left"></i>
          </button>
          <button v-for="page in paginatedEmployees.last_page" :key="page" @click="changePage(page)"
            :class="{ active: paginatedEmployees.current_page === page }">
            {{ page }}
          </button>
          <button @click="changePage(paginatedEmployees.current_page + 1)"
            :disabled="paginatedEmployees.current_page === paginatedEmployees.last_page">
            <i class="fas fa-chevron-right"></i>
          </button>
        </div>
      </div>
    </div>
    <!-- SIDE DETAIL CARD -->
    <div class="detail-section" v-if="selectedEmployee">
      <!-- Employee Detail Card -->
      <div class="detail-card">
        <div class="card-header">
          <button class="close-btn" @click="selectedEmployee = null">✖</button>
        </div>

        <div class="avatar">
          {{ selectedEmployee.first_name?.charAt(0).toUpperCase() }}
        </div>
        <h4 class="emp-name">{{ selectedEmployee.first_name }} {{ selectedEmployee.last_name }}</h4>
        <div class="emp-info">
          <h5 class='detail'>Details</h5>
          <p><strong>Email:</strong> {{ selectedEmployee.email }}</p>
          <p><strong>Department:</strong> {{ selectedEmployee.department?.name || 'N/A' }}</p>
          <p><strong>Designation:</strong> {{ selectedEmployee.designation?.name || 'N/A' }}</p>
        </div>
      </div>

      <!-- Assigned Tasks Card -->
      <div class="task-card">
        <h4>Assigned Tasks</h4>
        <table class="task-table">
          <thead>
            <tr>
              <th>Task</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="task in employeeTasks" :key="task.id">
              <td>{{ task.title }}</td>
              <td><strong>{{ task.status }}</strong></td>
              <td>
                <button @click="editAssignedTask(task)" class="edit-button">
                  <i class="fas fa-edit"></i> Edit
                </button>
                <button @click="deleteAssignedTask(task.id)" class="delete-button">
                  <i class="fas fa-trash"></i> Delete
                </button>
              </td>
            </tr>
            <tr v-if="employeeTasks.length === 0">
              <td colspan="3" class="no-tasks">No tasks assigned</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';

const departments = ref([]);
const designations = ref([]);
const tasks = ref([]);
const paginatedEmployees = ref({ data: [], current_page: 1, last_page: 1, per_page: 3 });

const selectedEmployee = ref(null);  // Employee selected to assign task
const employeeTasks = ref([]);  // List of tasks assigned to the selected employee
const newTask = ref("");  // New task to be added
const selectedEmployeeId = ref(null);  // Store selected employee's ID for task assignment

const newEmployee = ref({
  email: '',
  department_id: '',
  designation_id: '',
  first_name: '',
  last_name: '',
});

const isAddDisabled = computed(() => {
  return !newEmployee.value.email ||
    !newEmployee.value.department_id ||
    !newEmployee.value.designation_id ||
    !newEmployee.value.first_name ||
    !newEmployee.value.last_name;
});

// Fetch initial data for departments, designations, and employees
onMounted(async () => {
  try {
    const response = await axios.get('/api/department-designation-data', {
      headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }
    });
    departments.value = response.data.departments;
    designations.value = response.data.designations;
    fetchEmployees(1);
  } catch (error) {
    console.error('Error fetching data:', error);
  }
});

// Fetch paginated list of employees
const fetchEmployees = async (page = 1) => {
  const response = await axios.get(`/api/employees?page=${page}`, {
    headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }
  });
  paginatedEmployees.value = response.data;
  paginatedEmployees.value.data.forEach(emp => emp.isEditing = false);
};

// Add a new employee
const addEmployee = async () => {
  if (isAddDisabled.value) return;

  const employeeData = { ...newEmployee.value };
  console.log('Adding new employee:', employeeData);

  try {
    const response = await axios.post('/api/employee/add-employee', employeeData, {
      headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }
    });
    console.log('Employee added successfully:', response.data);
    fetchEmployees(paginatedEmployees.value.current_page);
    resetForm(); // Reset the form after successful addition
  } catch (error) {
    console.error('Error adding employee:', error);
  }
};

// Edit employee details
const editEmployee = (employee) => { employee.isEditing = true; };

// Save edited employee details
const saveEmployee = async (employee) => {
  await axios.put(`/api/employee/update-employee/${employee.id}`, employee, {
    headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }
  });
  employee.isEditing = false;
  fetchEmployees(paginatedEmployees.value.current_page);
};

// Delete an employee
const deleteEmployee = async (index, employeeId) => {
  await axios.delete(`/api/employee/delete-employee/${employeeId}`, {
    headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }
  });
  fetchEmployees(paginatedEmployees.value.current_page);
};

// View tasks for a selected employee
const viewEmployee = async (employee) => {
  selectedEmployee.value = employee;
  try {
    const response = await axios.get(`/api/employee/${employee.id}/tasks`, {
      headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }
    });

    // Check what response is returned
    console.log(response.data);  // Log the response

    if (response.data && response.data.length > 0) {
      employeeTasks.value = response.data;
    } else {
      employeeTasks.value = [];
    }

  } catch (error) {
    console.error('Error fetching employee tasks:', error);
    employeeTasks.value = []; // If error, reset tasks to empty array
  }
};

// Change pagination page
const changePage = (page) => {
  if (page > 0 && page <= paginatedEmployees.value.last_page) {
    fetchEmployees(page);
  }
};

// Add a new task and assign it to the selected employee
const addTask = async () => {
  if (!newTask.value.trim() || !selectedEmployeeId.value) {
    alert("Please provide a task title and select an employee.");
    return;
  }

  try {
    const response = await axios.post('/api/todos', {
      task: newTask.value,
      assigned_to: selectedEmployeeId.value  // Assign task to selected employee
    }, {
      headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }
    });

    // After creating task, refresh the employee's tasks
    viewEmployee(selectedEmployee.value);  // Fetch tasks for the selected employee again

    newTask.value = ""; // Clear input after adding task
  } catch (error) {
    console.error('Error adding task:', error);
  }
};

// Delete a task assigned to an employee
const deleteAssignedTask = async (taskId) => {
  try {
    await axios.delete(`/api/todos/${taskId}`, {
      headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }
    });

    // Refresh the employee's tasks after deletion
    viewEmployee(selectedEmployee.value);
  } catch (error) {
    console.error('Error deleting task:', error);
  }
};

// Reset the new employee form
const resetForm = () => {
  newEmployee.value = { email: '', department_id: '', designation_id: '', first_name: '', last_name: '' };
};

</script>

<style scoped>
.container {
  width: 100%;
  max-width: 1200px;
  background-color: #f8f9faa1;
  padding: 40px;
  border-radius: 4px;
  box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
  border: 1px solid rgb(118, 165, 209);
  margin-left: 137px;
  margin-top: 100px;
}

table {
  width: 100%;
  border-collapse: collapse;
  table-layout: fixed;
}

.title {
  border-bottom: 1px solid #679fd8;
  text-align: center;
  color: #2c2a2a;
  font-size: 1.4em;
  font-weight: 600;
  font-family: serif;
}
.view-button {
  padding: 6px 10px;
  background: #fbfcfd;
  border: none;
  border-radius: 5px;
  color: rgb(26, 25, 25);
  font-size: 14px;
  font-weight: bold;
  cursor: pointer;
  transition: all 0.2s ease-in-out;
}

.view-button:hover {
  background: #0d8ba1;
  color: white;
}

.delete-button,
.edit-button,
.save-button {
  padding: 6px 10px;
  border-radius: 4px;
  cursor: pointer;
  border: none;
  transition: 0.2s;
}

.main-layout {
  display: flex;
  gap: 20px;
}

.table-section {
  flex: 2;
}

.detail-card {
  background: #fff;
  padding: 20px;
  border: 1px solid #ccc;
  border-radius: 8px;
  width: 300px;
  height: 400px;
  font-family: sans-serif;
  box-shadow: inset 2px 4px 11px rgba(134, 187, 240, 0.5);
}

.task-card {
  flex: 1;
  background: #fff;
  padding: 20px;
  border: 1px solid #ccc;
  border-radius: 8px;
  box-shadow: inset 2px 4px 11px rgba(134, 187, 240, 0.5);
  height: 350px;
}

.task-card h5 {
  border-bottom: 1px solid #ddd;
  padding-bottom: 6px;
}

.task-card {
  padding: 20px;
  border: 1px solid #ccc;
  border-radius: 8px;
  box-shadow: inset 2px 4px 11px rgba(134, 187, 240, 0.5);
}

.task-table th,
.task-table td {
  padding: 10px;
  text-align: left;
  border: 1px solid #ddd;
}

.task-table th {
  background-color: #f4f4f4;
}

.task-table td {
  background-color: #fff;
}

.task-table .no-tasks {
  text-align: center;
  color: #888;
}

.edit-button,
.delete-button {
  padding: 6px 12px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  margin-right: 10px;
}

.edit-button {
  background-color: #4CAF50;
  color: white;
}

.delete-button {
  background-color: #f44336;
  color: white;
}

.edit-button:hover {
  background-color: #45a049;
}

.delete-button:hover {
  background-color: #d32f2f;
}

.edit-button i,
.delete-button i {
  margin-right: 5px;
  font-size: 14px;
}

.detail {
  font-size: 18px;
  border-bottom: 1px solid rgb(116, 112, 112);
  font-weight: bold;
}

.profile-header {
  display: flex;
  flex-direction: column;
  align-items: center;
  margin-top: 15px;
  margin-bottom: 20px;
}

.emp-name {
  text-align: center;
  font-size: 17px;
  margin-bottom: 100px;

}

.emp-info p {
  padding: 5px 0;
  margin: 0px;
}

.detail-section {
  display: flex;
  gap: 20px;
  margin-top: 20px;
}

.details-section p {
  margin: 6px 0;
}

.avatar {
  width: 70px;
  height: 70px;
  background: #295979;
  color: #fff;
  font-size: 37px;
  border-radius: 11%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 10px auto;
}

.close-btn {
  position: absolute;
  right: 15px;
  top: 15px;
  background: transparent;
  border: none;
  font-size: 18px;
  cursor: pointer;
  color: #444;
}

.close-btn:hover {
  color: #e74c3c;
}



th {
  padding: 6px;
  border: 2px solid #ddd;
  background: linear-gradient(to bottom, #275b8f, #b4cfe7);
  color: white;
  width: 14%;
}

th:first-child,
td:first-child {
  width: 4.3%;
}

th:nth-child(2),
td:nth-child(2),
th:nth-child(3),
td:nth-child(3) {
  width: 6%;
  padding-left: 15px;
}


th:nth-child(4),
td:nth-child(4),
th:nth-child(5),
td:nth-child(5) {
  width: 9%;
}

th:nth-child(6),
td:nth-child(6) {
  width: 9%;
  text-align: left;
  white-space: normal;
  word-wrap: break-word;
}

th:nth-child(7),
td:nth-child(7) {
  box-shadow: inset 2px 4px 11px rgba(134, 187, 240, 0.5);
  width: 7.7%;
}

th:nth-child(8),
td:nth-child(8) {
  width: 9%;
}

th:first-child,
td:first-child,
th:nth-child(2),
td:nth-child(2),
th:nth-child(3),
td:nth-child(3),
th:nth-child(4),
td:nth-child(4),
th:nth-child(5),
td:nth-child(5),
th:nth-child(6),
td:nth-child(6),
th:nth-child(8),
td:nth-child(8) {
  padding: 10px;
  border: 2px solid #cacaca;
  overflow: hidden;
  word-wrap: break-word;
  white-space: normal;
  box-shadow: inset 2px 4px 11px rgba(134, 187, 240, 0.5);
  font-size: 15px;
}

select,
input {
  width: 100%;
  padding: 8px 12px;
  border-color: #7bb0ec;
  border-radius: 4px;
  background-color: #ffffff;
  font-size: 16px;
  font-family: 'Arial', sans-serif;
  transition: border-color 0.3s ease;
}

select:focus,
input:focus {
  border-color: #2c87f0;
  outline: none;
}

.custom_input {
  padding: 5px;
  border-color: #7bb0ec;
  color: rgb(48, 45, 45);
  border-radius: 5px;
  cursor: pointer;
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 16px;
}

.add-button {
  border: 0px;
  background-color: #0867b4;
}

.custom_nameInput {
  height: 35px;
}

.add-button:hover {
  background-color: #084b97;
  color: rgb(247, 246, 246);
}

.delete-button {
  padding: 8px 12px;
  background-color: white;
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
  background-color: white;
  color: white;
  border-color: #347537;
  border-radius: 4px;
  cursor: pointer;
  display: flex;
  justify-content: center;
  align-items: center;
}

.save-button {
  padding: 8px 12px;
  background-color: white;
  color: white;
  border-color: #717271;
  border-radius: 4px;
  cursor: pointer;
  display: flex;
  justify-content: center;
  align-items: center;
}

.delete-button:hover {
  background-color: rgb(245, 38, 38);
  color: #f9f9f9
}

.edit-button:hover {
  background-color: #319131;
  color: #f9f9f9
}

.save-button:hover {
  background-color: #4e644e;
  color: #f9f9f9
}

.edit-button:hover i {
  color: #f9f9f9;
}

.delete-button:hover i {
  color: #f9f9f9;
}

.save-button:hover i {
  color: #f9f9f9;
}

.delete-button i,
.edit-button i,
.save-button i {
  font-size: 15px;
  color: #333;
}

button:disabled {
  cursor: not-allowed;
  background-color: #ddd;
}

.button-group {
  display: flex;
  gap: 10px;
  justify-content: flex-start;
}

.button-group button {
  flex: 1 1 auto;
}

button:hover {
  background-color: #f4f4f4;
  color: black;
}

.pagination {
  display: flex;
  justify-content: right;
  gap: 5px;
  margin-top: 20px;
}

.pagination button {
  padding: 2px 7px;
  border: 1px solid #ddd;
  border-radius: 6px;
  background-color: #f9f9f9;
  cursor: pointer;
  transition: all 0.2s ease;
}

.pagination button:hover {
  background-color: #1675e2;
  color: white;
}

.pagination button.active {
  background-color: #275b8f;
  color: white;

}

.pagination button:disabled {
  background-color: #ddd;
  cursor: not-allowed;
}
</style>