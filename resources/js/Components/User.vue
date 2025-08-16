<template>
  <div class="container">
    <h1 class="title">EMPLOYEE TASK MANAGEMENT - TO-DO LIST</h1>
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
            <select v-model="newEmployee.email" class="custom_dropdown">
              <option value="">Email</option>
              <option v-for="email in employeeEmails" :key="email" :value="email">{{ email }}</option>
            </select>
          </th>
          <th>
            <select v-model="newEmployee.department_id" class="custom_dropdown">
              <option value="">Department</option>
              <option v-for="department in departments" :key="department.id" :value="department.id">{{ department.name
                }}</option>
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
            <button class="btn btn-primary w-100 add-button" @click="addEmployee" :disabled="isAddDisabled"><i
                class="fas fa-plus"></i>Add</button>
          </th>
        </tr>
      </thead>

      <tbody>
        <!-- Loop through the paginated employees array and display employee data dynamically -->
        <tr v-for="(employee, index) in paginatedEmployees.data" :key="employee.id">
          <td>{{ index + 1 + ((paginatedEmployees.current_page - 1) * paginatedEmployees.per_page) }}.</td>
          <td>
            <span v-if="!employee.isEditing">{{ employee.first_name }}</span>
            <input v-else v-model="employee.first_name" class="custom_input" type="text" placeholder="First Name" />
          </td>
          <td>
            <span v-if="!employee.isEditing">{{ employee.last_name }}</span>
            <input v-else v-model="employee.last_name" class="custom_input" type="text" placeholder="Last Name" />
          </td>
          <td>
            <span v-if="!employee.isEditing">{{ employee.email }}</span>
            <select v-else v-model="employee.email" class="custom_dropdown">
              <option value="">Select Email</option>
              <option v-for="email in employeeEmails" :key="email" :value="email">{{ email }}</option>
            </select>
          </td>
          <td>
            <span v-if="!employee.isEditing">{{ employee.department?.name || 'No Department' }}</span>
            <select v-else v-model="employee.department_id" class="custom_dropdown">
              <option value="">Select Department</option>
              <option v-for="department in departments" :key="department.id" :value="department.id">{{ department.name
                }}</option>
            </select>
          </td>

          <!-- Designation -->
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
        Previous
      </button>

      <span>Page {{ paginatedEmployees.current_page }} of {{ paginatedEmployees.last_page }}</span>

      <button @click="changePage(paginatedEmployees.current_page + 1)"
        :disabled="paginatedEmployees.current_page === paginatedEmployees.last_page">
        Next
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';

const employees = ref([]);  // Dynamically populated employees list
const departments = ref([]);  // List of departments
const designations = ref([]);  // List of designations
const employeeEmails = ref([]);  // List of employee emails for dropdown
const tasks = ref([]);  // Store tasks
const paginatedEmployees = ref({
  data: [],
  current_page: 1,
  last_page: 1,
  per_page: 3,  // Show 3 employees per page
});  // Store paginated employee data

const newEmployee = ref({
  email: '',
  department_id: '',
  designation_id: '',
  first_name: '',
  last_name: '',
  task_id: ''  // Add task_id for assigning task to new employee
});

const isAddDisabled = computed(() => {
  // Disable the Add button if any field is missing
  return !newEmployee.value.email ||
    !newEmployee.value.department_id ||
    !newEmployee.value.designation_id ||
    !newEmployee.value.first_name ||
    !newEmployee.value.last_name;
});

// On mounted, fetch departments, designations, tasks, and employees
onMounted(async () => {
  try {
    const response = await fetch('/api/department-designation-data', {
      method: 'GET',
      headers: {
        'Authorization': `Bearer ${localStorage.getItem('token')}`
      }
    });

    const data = await response.json();
    departments.value = data.departments;
    designations.value = data.designations;
    employeeEmails.value = data.employees.map(employee => employee.email);

    const taskResponse = await axios.get('/api/tasks', {
      headers: {
        'Authorization': `Bearer ${localStorage.getItem('token')}`
      }
    });
    tasks.value = taskResponse.data;

    // Load employees from API with pagination
    fetchEmployees(1);

  } catch (error) {
    console.error('Error fetching data:', error);
  }
});

// Fetch paginated employees
const fetchEmployees = async (page = 1) => {
  try {
    const response = await axios.get(`/api/employees?page=${page}`, {
      headers: {
        Authorization: `Bearer ${localStorage.getItem('token')}`
      }
    });
    paginatedEmployees.value = response.data;  // Store paginated employees
  } catch (error) {
    console.error('Error fetching employees:', error);
  }
};

// Add employee to the list dynamically
const addEmployee = async () => {
  if (isAddDisabled.value) {
    return;
  }

  const employeeData = {
    email: newEmployee.value.email,
    department_id: newEmployee.value.department_id,
    designation_id: newEmployee.value.designation_id,
    first_name: newEmployee.value.first_name,
    last_name: newEmployee.value.last_name,
    task_id: newEmployee.value.task_id,
  };

  try {
    const response = await axios.post('/api/employee/add-employee', employeeData, {
      headers: {
        'Authorization': `Bearer ${localStorage.getItem('token')}`
      }
    });

    // Add the new employee to the array and reload the list with pagination
    fetchEmployees(paginatedEmployees.value.current_page); // Reload with current page

    resetForm();

  } catch (error) {
    console.error('Error adding employee:', error);
    alert('Failed to add employee.');
  }
};

// Edit employee details
const editEmployee = (employee) => {
  employee.isEditing = true;
};

// Save the edited employee details
const saveEmployee = (employee, index) => {
  employee.isEditing = false;

  const updatedEmployee = {
    ...employee,
    department: departments.value.find(dep => dep.id === employee.department_id),
    designation: designations.value.find(desig => desig.id === employee.designation_id)
  };

  employees.value.splice(index, 1, updatedEmployee);

  // Update employee list with pagination
  fetchEmployees(paginatedEmployees.value.current_page);
};

// Delete employee
const deleteEmployee = async (index, employeeId) => {
  try {
    const response = await axios.delete(`/api/employee/delete-employee/${employeeId}`, {
      headers: {
        'Authorization': `Bearer ${localStorage.getItem('token')}`
      }
    });

    if (response.status === 200) {
      fetchEmployees(paginatedEmployees.value.current_page);
    }
  } catch (error) {
    console.error('Error deleting employee:', error);
    alert('Failed to delete employee.');
  }
};

// Change page for pagination
const changePage = (page) => {
  if (page > 0 && page <= paginatedEmployees.value.last_page) {
    fetchEmployees(page);
  }
};

// Reset the form fields
const resetForm = () => {
  newEmployee.value = {
    email: '',
    department_id: '',
    designation_id: '',
    first_name: '',
    last_name: '',
    task_id: '',  // Reset task_id when form is cleared
  };
};
</script>

<style scoped>
.title {
  border-bottom: 1px solid #679fd8;
  text-align: center;
  color: #2c2a2a;
  font-size: 1.5em;
  font-weight: 600;
  font-family: serif;
}

.container {
  width: 100%;
  max-width: 950px;
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
td:nth-child(2) {
  width: 10%;
  padding-left: 15px;
}

th:nth-child(3),
td:nth-child(3),
th:nth-child(4),
td:nth-child(4),
th:nth-child(5),
td:nth-child(5) {
  width: 10%;
}

th:nth-child(6),
td:nth-child(6) {
  width: 13%;
  text-align: left;
  white-space: normal;
  word-wrap: break-word;
}

th:nth-child(7),
td:nth-child(7) {
  width: 8.5%;
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
  color: rgb(43, 41, 41);
  font-size: 14.7px;
}

select,
input {
  width: 100%;
  padding: 8px 12px;
  border: 1px solid #ddd;
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

.custom_input .add-button {
  width: 20%;
  padding: 5px;
  border-color: #4396f5;
  color: rgb(48, 45, 45);
  border-radius: 5px;
  cursor: pointer;
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 16px;
}

.custom_nameInput {
  height: 35px;
}

.add-button:hover {
  background-color: #1675e2;
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
  justify-content: center;
  gap: 20px;
  margin-top: 20px;
}

.pagination button {
  padding: 8px 12px;
  border: 1px solid #ddd;
  border-radius: 4px;
  background-color: #f1f1f1;
}

.pagination button:disabled {
  background-color: #ddd;
  cursor: not-allowed;
}
</style>
