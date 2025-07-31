<template>
  <div class="container">
    <h1 class="title">EMPLOYEE TASK MANAGEMENT - TO-DO LIST</h1>
    <table class="table">
      <thead>
        <tr>
          <th>
            <select v-model="newEmployee.id">
              <option value="">Select Id</option>
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
            <select v-model="newEmployee.department_id">
              <option value="">Select Department</option>
              <option v-for="department in departments" :key="department.id" :value="department.id">{{ department.name }}</option>
            </select>
          </th>
          <th>
            <select v-model="newEmployee.designation_id">
              <option value="">Select Designation</option>
              <option v-for="designation in designations" :key="designation.id" :value="designation.id">{{ designation.name }}</option>
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
        <tr v-for="(employee, index) in employees" :key="index">
          <td>
            <span v-if="!employee.isEditing">{{ employee.id }}</span>
            <select v-else v-model="employee.id">
              <option value="">Select Id</option>
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
            <span v-if="!employee.isEditing">{{ employee.department.name }}</span>
            <select v-else v-model="employee.department_id">
              <option value="">Select Department</option>
              <option v-for="department in departments" :key="department.id" :value="department.id">{{ department.name }}</option>
            </select>
          </td>
          <td>
            <span v-if="!employee.isEditing">{{ employee.designation.name }}</span>
            <select v-else v-model="employee.designation_id">
              <option value="">Select Designation</option>
              <option v-for="designation in designations" :key="designation.id" :value="designation.id">{{ designation.name }}</option>
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

// Define the reactive data
const employees = ref([]);
const departments = ref([]);
const designations = ref([]);
const employeeIds = ref([]);  
const employeeEmails = ref([]);  
const assignedTo = ref([]); 
const newEmployee = ref({
  department_id: '',
  designation_id: '',
  assigned_to: ''
});

// Define the state for disabling the Add button
const isAddDisabled = computed(() => {
  return !newEmployee.value.id || !newEmployee.value.email || !newEmployee.value.department_id || !newEmployee.value.designation_id || !newEmployee.value.assigned_to;
});

// Fetch data when component is mounted
onMounted(async () => {
  try {
    // Fetch department and designation data
    const departmentDesignationResponse = await fetch('/api/department-designation-data');
    const departmentDesignationData = await departmentDesignationResponse.json();
    departments.value = departmentDesignationData.departments;
    designations.value = departmentDesignationData.designations;
    
    // Fetch employee data
    const employeeResponse = await fetch('/api/employees');
    const employeeData = await employeeResponse.json();
    employees.value = employeeData;

    // Populate the missing properties from the employee data
    employeeIds.value = employeeData.map(employee => employee.id);  
    employeeEmails.value = employeeData.map(employee => employee.email); 
    assignedTo.value = employeeData.map(employee => employee.name);  
  } catch (error) {
    console.error('Error fetching data:', error);
  }
});

// Add a new employee
const addEmployee = () => {
  console.log(newEmployee.value);
  // Logic to add the new employee to the system goes here.
};
</script>

<style scoped>
.title {
  border-bottom: 1px solid #679fd8;
  text-align: center;
  color: #333;
  font-size: 1.7em;
  font-weight: 600;
  font-family: serif;
}
.container {
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
.add_btn {
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
