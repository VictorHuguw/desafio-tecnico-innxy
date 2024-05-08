<template>
  <div>
    <input type="text" v-model="searchText" placeholder="Digite para buscar" />
    <v-container>
      <v-row>
        <v-col v-for="item in displayedProducts" :key="item.id" cols="12" sm="6" md="3" lg="3">
          <v-card class="mx-auto" width="200px" @click="openModal(item)">
            <v-img height="150px" :src="logo" cover></v-img>
            <v-card-title>
              {{ item.name }}
            </v-card-title>

            <v-card-subtitle>
              {{ item.description }}
            </v-card-subtitle>
          </v-card>
        </v-col>
      </v-row>
      <v-pagination v-model="currentPage" :length="totalPages" @input="fetchPage"></v-pagination>
      <v-btn color="primary" dark fab @click="openInsertModal">
        <v-icon>mdi-plus</v-icon>
      </v-btn>
    </v-container>

    <v-dialog v-model="modalOpen" max-width="500px">
      <v-card>
        <v-form @input="checkFormChanges">
          <v-card-title v-if="modalData">Detalhes do produto</v-card-title>

          <v-container>
            <v-container fluid fill-height>
              <v-row align="center" justify="center">
                <v-col cols="12" sm="8" md="6">
                  <v-img :style="{ maxHeight: '200px' }" :src="uploadedImage ? uploadedImage : logoUpload"
                    contain></v-img>
                  <input type="file" ref="fileInput" style="display: none" @change="handleImageUpload" />
                  <v-btn color="primary" dark @click="$refs.fileInput.click()">Selecionar Imagem</v-btn>
                </v-col>
              </v-row>
            </v-container>

            <v-col>
              <v-text-field density="compact" placeholder="Nome" variant="outlined"
                v-model="modalData.name"></v-text-field>
            </v-col>
            <v-col>
              <v-text-field density="compact" placeholder="Descrição" variant="outlined"
                v-model="modalData.description"></v-text-field>
            </v-col>
            <v-col>
              <v-text-field density="compact" placeholder="Preço" variant="outlined"
                v-model="modalData.price"></v-text-field>
            </v-col>
            <v-col>
              <v-menu justify="space-around" v-model="menu" :close-on-content-click="false" content-class="menu-content" transition="scale-transition" offset-y>
                <template v-slot:activator="{ on }">
                  <v-text-field mask="dd-yy-mm" v-model="modalData.validateDate" @click="menu = !menu" v-on="on" dense placeholder="Data de validade"
                  return
                    outlined></v-text-field>
                </template>
                <v-date-picker v-model="modalData.validateDate" @input="menu = false" ></v-date-picker>
              </v-menu>
            </v-col>
            <v-col>
              <v-text-field density="compact" placeholder="Categoria" variant="outlined"
                v-model="modalData.category.name"></v-text-field>
            </v-col>
            <v-btn :disabled="!formChanged" :loading="loading" class="text-none mb-4" color="indigo-darken-3"
              size="x-large" variant="flat" block @click="submitForm">
              Salvar
            </v-btn>

            <v-btn class="text-none" color="red" size="x-large" variant="flat" block @click="deleteItem(modalData)">
              Deletar
            </v-btn>
          </v-container>
        </v-form>
      </v-card>
    </v-dialog>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import logo from "../assets/uploadedImages/logo.png";
import logoUpload from "../assets/img.png";

const menu = ref(false);
const todos = ref([]);
const currentPage = ref(1);
const itemsPerPage = 12;
const url = "http://localhost:8000/api/product";
const modalOpen = ref(false);
const modalData = ref(null);
const formChanged = ref(false);
const loading = ref(false);
const uploadedImage = ref(null);
const uploadedImageData = ref(null);
const totalPages = computed(() => Math.ceil(todos.value.length / itemsPerPage));

function handleImageUpload(event) {
  const file = event.target.files[0];
  uploadedImageData.value = file
  const reader = new FileReader();
  reader.onload = () => {
    uploadedImage.value = reader.result;
  };
  reader.readAsDataURL(file);
}
function openInsertModal() {
  modalData.value = {
    name: "",
    description: "",
    price: "",
    validateDate: "",
    category: { name: "" },
  };
  modalOpen.value = true;
}

const displayedProducts = computed(() => {
  const startIndex = (currentPage.value - 1) * itemsPerPage;
  const endIndex = startIndex + itemsPerPage;
  return todos.value.slice(startIndex, endIndex);
});


onMounted(async () => {
  await fetchTasks();
});

async function fetchTasks() {
  try {
    const response = await fetch("http://localhost:8000/api/products");
    const responseData = await response.json();
    todos.value = responseData.data;
  } catch (error) {
    console.error(error);
  }
}

async function updateItem(item) {
  loading.value = true;
  const payload = {
    id: item.id,
    name: item.name,
    category: item.category.id,
    img: uploadedImageData.value, 
    description: item.description,
    validateDate: item.validateDate,
    price: item.price,
  };
  try {
    const response = await fetch(`${url}/${item.id}`, {
      method: "PUT",
      body: JSON.stringify(payload),
    });
    const responseData = await response.json();
    if (response.ok) {
      window.alert(responseData.data);
      fetchTasks();
    } else {
      window.alert(responseData.data);
    }
    closeModal();
  } catch (error) {
    console.error(error);
  } finally {
    loading.value = false;
  }
}

async function insertItem() {

  loading.value = true;

  const formData = new FormData();

  formData.append('name', modalData.value.name);
  formData.append('category', modalData.value.category.name);
  formData.append('description', modalData.value.description);
  formData.append('validateDate', modalData.value.validateDate);
  formData.append('price', modalData.value.price);
  formData.append('img', uploadedImageData.value); // Enviar a imagem como parte do FormData

  try {
    const response = await fetch(url, {
      method: "POST",
      body: formData,
    });
    const responseData = await response.json();
    if (response.ok) {
      window.alert(responseData.data);
      fetchTasks();
    } else {
      window.alert(responseData.data);
    }
    closeModal();
  } catch (error) {
    console.error(error);
  } finally {
    loading.value = false;
  }
}

async function deleteItem(item) {
  try {
    const response = await fetch(
      `http://localhost:8000/api/product/${item.id}`,
      {
        method: "DELETE",
      }
    );
    const responseData = await response.json();
    if (response.ok) {
      window.alert(responseData.data);
      fetchTasks();
    } else {
      window.alert(responseData.data);
    }
    closeModal();
  } catch (error) {
    console.error(error);
  }
}

function checkFormChanges() {
  formChanged.value = true;
}

function closeModal() {
  modalOpen.value = false;
}

function resetFormChanges() {
  formChanged.value = false;
}

function openModal(item) {
  modalData.value = item;
  modalData.value.validateDate = new Date(modalData.value.validateDate);
  modalOpen.value = true;
}

function submitForm() {
  if (modalData.value.id) {
    updateItem(modalData.value);
  } else {
    insertItem(); 
  }
  resetFormChanges();
}

</script>
<style scoped>
.menu-content {
  display: flex;
  justify-content: center;
  align-items: center;
}
</style>