<template>
    <form @submit.prevent="handleSubmit(product)">
        <!-- Name -->
        <div>
            <label for="product-name" class="block text-sm font-medium text-gray-700">
                *Name
            </label>
            <input v-model="product.name" id="product-name" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
            <div class="text-red-600 mt-1">
                <div v-for="message in validationErrors?.name">
                    {{ message }}
                </div>
            </div>
        </div>

        <!-- Description -->
        <div class="mt-4">
            <label for="product-description" class="block text-sm font-medium text-gray-700">
                *Description
            </label>
            <textarea v-model="product.description" id="product-description" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"></textarea>
            <div class="text-red-600 mt-1">
                <div v-for="message in validationErrors?.description">
                    {{ message }}
                </div>
            </div>
        </div>

        <!-- Price -->
        <div>
            <label for="product-price" class="block text-sm font-medium text-gray-700">
                *Price
            </label>
            <input v-model="product.price" id="product-price" type="number" step="0.01" inputmode="decimal" pattern="\d+(\.\d{1,2})?" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
            <div class="text-red-600 mt-1">
                <div v-for="message in validationErrors?.price">
                    {{ message }}
                </div>
            </div>
        </div>

        <!-- Image -->
        <div class="mt-4">
            <label for="product-image" class="block font-medium text-sm text-gray-700">
                *Image
            </label>
            <input @change="product.image = $event.target.files[0]" type="file" id="product-image" />
            <div class="text-red-600 mt-1">
                <div v-for="message in validationErrors?.image">
                    {{ message }}
                </div>
            </div>
        </div>

        <!-- Category -->
        <div class="mt-4">
            <label for="product-category" class="block text-sm font-medium text-gray-700">
                *Category
            </label>
            <input v-model="categoryInput" @input="searchCategories" placeholder="Type and select categories..." class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"/>
            <div class="text-red-600 mt-1">
                <div v-for="message in validationErrors?.category">
                    {{ message }}
                </div>
            </div>
        </div>

        <div class="mt-4">
            <span v-for="(selectedCategory, index) in selectedCategoriesName" :key="index" class="bg-gray-200 rounded-md px-2 py-1 m-1">
                {{ selectedCategory }}
                <button @click="removeCategory(index)" class="ml-1 text-xs mt-2">×</button>
            </span>
        </div>
        <ul v-if="showSuggestions" class="flex flex-wrap gap-2 mt-4">
            <li v-for="category in suggestedCategories.data" :key="category.id">
                <button v-if="isSelected(category.id)" @click="addCategory(category.name, category.id)" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-1 px-2 rounded-sm text-xs mt-2">
                    {{ category.name }}
                </button>
            </li>
        </ul>



        <!-- Buttons -->
        <div class="mt-4">
            <button :disabled="isLoading" class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm uppercase text-white disabled:opacity-75 disabled:cursor-not-allowed">
                <span v-show="isLoading" class="inline-block animate-spin w-4 h-4 mr-2 border-t-2 border-t-white border-r-2 border-r-white border-b-2 border-b-white border-l-2 border-l-blue-600 rounded-full"></span>
                <span v-if="isLoading">Processing...</span>
                <span v-else>Save</span>
            </button>
        </div>
    </form>
</template>

<script setup>
import { reactive, ref, } from 'vue';
import useProducts from '@/composables/products';

const product = reactive({
    name: '',
    description: '',
    price: '',
    image: '',
    category_ids: [],
})


const categoryInput = ref('');
const suggestedCategories = ref({})
const selectedCategoriesName = ref([]);
let showSuggestions = ref(false);



//Function to search for categories
const searchCategories = async () => {
  try {
    const response = await axios.get('/api/v1/categories', {
      params: { name: categoryInput.value }
    });
    suggestedCategories.value = response.data.data;
    showSuggestions.value = true;
  } catch (error) {
    console.error('Error fetching categories:', error);
  }
};

// Function to check if category has been selected
const isSelected = (categoryId) => {
    return !product.category_ids.some((category) => category === categoryId);
};

// Function to add a selected category
const addCategory = (categoryName, categoryId) => {
  selectedCategoriesName.value.push(categoryName);
  product.category_ids.push(categoryId);
  categoryInput.value = '';
  showSuggestions.value = false;
};

// Function to remove a selected category
const removeCategory = (index) => {
  selectedCategoriesName.value.splice(index, 1);
  product.category_ids.splice(index, 1);
};

// Function to check if all required fields are filled
const areAllFieldsFilled = (product) => {
    return (
        product.name.trim() !== '' &&
        product.description.trim() !== '' &&
        product.price !== '' &&
        product.image !== '' &&
        product.category_ids.length > 0
    );
};

// Function to handle form submission
const handleSubmit = (product) => {
    validationErrors.value = {}; // Reset validation errors

    if (product.name.trim() === '') {
        validationErrors.value.name = ['Name field is required.'];
    }
    if (product.description.trim() === '') {
        validationErrors.value.description = ['Description field is required.'];
    }
    if (product.price === '') {
        validationErrors.value.price = ['Price field is required.'];
    }
    if (product.image === '') {
        validationErrors.value.image = ['Image field is required.'];
    }
    if (product.category_ids.length === 0) {
        validationErrors.value.category = ['Category field is required.'];
    }

    if (Object.keys(validationErrors.value).length === 0) {
        // No validation errors, proceed with form submission
        storeProduct(product);
    }
};

const { storeProduct, validationErrors, isLoading } = useProducts()
</script>
