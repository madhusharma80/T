<template>
  <div class="todo-container">
    <h1>TODO LIST</h1>
    <div class="sub-container">
      <div class="add-todo">
        <input
          v-model="newTodo"
          type="text"
          placeholder="Add a new task"
          @keyup.enter="addTodo"
          @input="showError = false"
          class="todo-input"
          :class="{ 'input-error': showError }"/>
        <button @click="addTodo" class="add-button">
          <i class="fas fa-plus"></i> Add
        </button>
      </div>
      <!-- Start writing error remove -->
      <p v-if="showError" class="error-message">Task cannot be empty!</p>
      <ul class="todo-list">
        <li v-for="todo in todos" :key="todo.id" class="todo-item">
          <input
            type="checkbox"
            v-model="todo.completed"
            @change="updateTodo(todo)"
            class="todo-checkbox"/>
          
          <!-- Display title or input field based on editing state -->
          <span v-if="!todo.isEditing" :class="{ completed: todo.completed }" class="todo-title">
            {{ todo.title }}
          </span>
          <input
            v-if="todo.isEditing"
            v-model="todo.title"
            @keyup.enter="saveEditedTodo(todo)"
            @blur="saveEditedTodo(todo)"
            class="edit-input"/>
          <!-- Edit and Delete buttons -->
          <button @click="editTodo(todo)" v-if="!todo.isEditing" class="edit-button">
            <i class="fas fa-edit"></i>
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
const showError = ref(false);

// Fetch existing todos when component mounts
onMounted(() => {
  fetchTodos();
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

// Update an existing todo
const updateTodo = async (todo) => {
  try {
    await axios.put(`/api/todos/${todo.id}`, {
      title: todo.title,
      completed: todo.completed
    }, {
      headers: {
        Authorization: `Bearer ${localStorage.getItem('token')}`
      }
    });
  } catch (error) {
    console.error('Error updating todo:', error);
  }
};

// Set task to edit mode
const editTodo = (todo) => {
  todo.isEditing = true;


};

// Save edited todo
const saveEditedTodo = async (todo) => {
  try {
    const response = await axios.put(`/api/todos/${todo.id}`, {
      title: todo.title,
      completed: todo.completed
    }, {
      headers: {
        Authorization: `Bearer ${localStorage.getItem('token')}`
      }
    });

    // After updating in backend, stop editing mode
    todo.isEditing = false;

    // Reflect changes in the frontend
    const index = todos.value.findIndex(t => t.id === todo.id);
    if (index !== -1) {
      todos.value[index] = response.data;
    }
  } catch (error) {
    console.error('Error saving edited todo:', error);
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
.todo-container {
  width: 100%;
  max-width: 600px;
  background-color: #f9f9f9;
  border-radius: 10px;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
  font-family: 'Arial', sans-serif;
  margin-left: 550px;
  margin-top: 80px;
  flex-grow: 1;
  padding: 20px;
  border: 1px solid rgb(118, 165, 209);
}

.sub-container {
  width: 100%;
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
  font-size: 1.7em;
  font-weight: 600;
  font-family: serif;
}

.add-todo {
  display: flex;
  justify-content: space-between;
  margin-bottom: 5px;
  /* Reduced margin to make space for error message */
}

.todo-input {
  width: 76%;
  padding: 10px;
  font-size: 16px;
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
  border-color: #4CAF50;
  color: rgb(10, 10, 10);
  border-radius: 5px;
  cursor: pointer;
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 16px;
}

.add-button:hover {
  background-color: #45a049;
  color: rgb(247, 246, 246);
}

.add-button i {
  font-size: 15px;
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
  color: #f9f9f9
}

.edit-button:hover {
  background-color: #45a049;
  color: #f9f9f9
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

.edit-input {
  width: 70%;
  padding: 8px;
  border-radius: 5px;
  border: 1px solid #ddd;
  font-size: 16px;
  margin-right: 10px;
  outline: none;
}

.edit-input:focus {
  border-color: #4CAF50;
}

/* New styles for validation error */
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