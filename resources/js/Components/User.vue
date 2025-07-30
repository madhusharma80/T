<template>
  <div class="container">
      <div class="sidebar" :class="{ 'sidebar-hidden': !sidebarOpen }"></div>
    <h1 class="title">EMPLOYEE TASK MANAGEMENT - TO-DO LIST</h1>
    <table class="table">
      <thead>
        <tr>
          <th>
            <select v-model="newEmployee.id">
              <option value=""> Id </option>
              <option v-for="id in employeeIds" :key="id" :value="id">{{ id }}</option>
            </select>
          </th>
          <th>
            <select v-model="newEmployee.email">
              <option value="">Select Email</option>
              <option v-for="email in employeeEmails" :key="email" :value="email">{{ email }}</option>
            </select>
          </th>
          <th>
            <select v-model="newEmployee.department">
              <option value="">Department</option>
              <option v-for="department in departments" :key="department" :value="department">{{ department }}</option>
            </select>
          </th>
          <th>
            <select v-model="newEmployee.designation">
              <option value="">Designation</option>
              <option v-for="designation in designations" :key="designation" :value="designation">{{ designation }}</option>
            </select>
          </th>
          <th>
            <select v-model="newEmployee.assigned_to">
              <option value="">Assigned To</option>
              <option v-for="assigned in assignedTo" :key="assigned" :value="assigned">{{ assigned }}</option>
            </select>
          </th>
          <th>
            <button class="btn btn-primary add_btn" @click="addEmployee" :disabled="isAddDisabled">Add</button>
          </th>
        </tr>
      </thead>
      <tbody>
        <!-- Display added rows -->
        <tr v-for="(employee, index) in employees" :key="index">
          <td>
            <span v-if="!employee.isEditing">{{ employee.id }}</span>
            <select v-else v-model="employee.id">
              <option value="">Select Email</option>
              <option v-for="id in employeeIds" :key="id" :value="id">{{ id }}</option>
            </select>
          </td>
          <td>
            <span v-if="!employee.isEditing">{{ employee.email }}</span>
            <select v-else v-model="employee.email">
              <option value="">Select Email</option>
              <option v-for="email in employeeEmails" :key="email" :value="email">{{ email }}</option>
            </select>
          </td>
          <td>
            <span v-if="!employee.isEditing">{{ employee.department }}</span>
            <select v-else v-model="employee.department">
              <option value="">Select Department</option>
              <option v-for="department in departments" :key="department" :value="department">{{ department }}</option>
            </select>
          </td>
          <td>
            <span v-if="!employee.isEditing">{{ employee.designation }}</span>
            <select v-else v-model="employee.designation">
              <option value="">Select Designation</option>
              <option v-for="designation in designations" :key="designation" :value="designation">{{ designation }}</option>
            </select>
          </td>
          <td>
            <span v-if="!employee.isEditing">{{ employee.assigned_to }}</span>
            <select v-else v-model="employee.assigned_to">
              <option value="">Select Assigned To</option>
              <option v-for="assigned in assignedTo" :key="assigned" :value="assigned">{{ assigned }}</option>
            </select>
          </td>
          <td>
            <div class="button-group">
            <button class="btn btn-secondary " @click="editEmployee(employee)" v-if="!employee.isEditing">Edit</button>
            <button class="btn btn-success" @click="saveEmployee(employee)" v-if="employee.isEditing">Save</button>
            <button class="btn btn-danger" @click="deleteEmployee(index)">Delete</button>
            </div>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';


// Data (using ref for reactivity)
const employees = ref([]); 
const newEmployee = ref({
  id: '',
  email: '',
  department: '',
  designation: '',
  assigned_to: ''
});

const departments = ref(['Web Development', 'Web Designing', 'Sales', 'Marketing']);
const designations = ref(['Designer', 'Trainee', 'Intern', 'Developer']);
const assignedTo = ref(['John', 'Jane', 'Doe', 'Smith']);
const employeeIds = ref([1, 2, 3, 4, 5]);
const employeeEmails = ref(['john@example.com', 'jane@example.com', 'doe@example.com','Smith@gmail']); 



// Load employees from localStorage if available
onMounted(() => {
  const storedEmployees = localStorage.getItem('employees');
  if (storedEmployees) {
    employees.value = JSON.parse(storedEmployees); // Parse the data and load it into employees
  }
});

// Computed property to enable/disable the Add button based on the selection
const isAddDisabled = computed(() => {
  return !(newEmployee.value.id && newEmployee.value.email && newEmployee.value.department &&
           newEmployee.value.designation && newEmployee.value.assigned_to);
});

// Method to add a new employee to the table
function addEmployee() {
  const newEmployeeData = { ...newEmployee.value, isEditing: false };
  employees.value.push(newEmployeeData); // Add the new employee to the employees list

  // Save updated employees list to localStorage
  localStorage.setItem('employees', JSON.stringify(employees.value));

  // Clear the form after adding the employee
  newEmployee.value = {
    id: '',
    email: '',
    department: '',
    designation: '',
    assigned_to: ''
  };
}

// Method to enable editing for a specific employee
function editEmployee(employee) {
  employee.isEditing = true;
}

// Method to save the edited employee
function saveEmployee(employee) {
  employee.isEditing = false; 
  // Save updated employees list to localStorage
  localStorage.setItem('employees', JSON.stringify(employees.value));
}

// Method to delete a specific employee from the list
function deleteEmployee(index) {
  employees.value.splice(index, 1); // Remove the employee from the list
  // Save updated employees list to localStorage
  localStorage.setItem('employees', JSON.stringify(employees.value));
}
</script>

<style scoped>
.title{
border-bottom: 1px solid #679fd8;
  text-align: center;
  color: #333;
  font-size: 1.7em;
  font-weight: 600;
  font-family: serif;
}
.container{
  max-width: 1000px;
  margin: 100px auto;
  background-color: #f8f9fa;
  padding: 80px;
  border-radius: 8px;
  box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
  border: 1px solid rgb(118, 165, 209);
  
}
table {
  width: 100%;
  border-collapse: collapse;
}
th, td {
  padding: 12px;
  border: 2px solid #ddd;
  background: linear-gradient(to bottom, #3a699b, #8daeca);
  color:white;
}
select {
  width: 100%;
  padding: 5px;
  border: 1px solid #ddd;
  margin-top: 5px;
}
button {
  padding: 5px 10px;
  cursor: pointer;
  margin-top: 5px;
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
.add_btn{
width: 100%;
background-color: #ddd;

}
button:hover {
  background-color: #f4f4f4;
  color:black;
}
input {
  padding: 5px;
  width: 100%;
}
</style>
