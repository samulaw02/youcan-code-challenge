<template>
    <div class="overflow-hidden overflow-x-auto p-6 bg-white border-gray-200">
        <div class="min-w-full align-middle">
            <div class="mb-4 grid lg:grid-cols-4 grid-cols-2 gap-4">
                <input v-model="search_global" type="text" placeholder="Advanced Search..." class="inline-block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" name="search_global">
            </div>
            <table class="min-w-full divide-y divide-gray-200 border">
                <thead>
                    <!-- Sort -->
                    <tr>
                        <th class="px-6 py-3 bg-gray-50 text-left">
                            <div class="flex flex-row items-center justify-between">
                                <div class="leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                    ID
                                </div>
                            </div>
                        </th>
                        <th class="px-6 py-3 bg-gray-50 text-left">
                            <div class="flex flex-row items-center justify-between">
                                <div class="leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                    Name
                                </div>
                            </div>
                        </th>
                        <th class="px-6 py-3 bg-gray-50 text-left">
                            <span class="text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Description</span>
                        </th>
                        <th class="px-6 py-3 bg-gray-50 text-left">
                            <div class="flex flex-row items-center justify-between cursor-pointer" @click="updateOrdering('price')">
                                <div class="leading-4 font-medium text-gray-500 uppercase tracking-wider" :class="{ 'font-bold text-blue-600': orderColumn === 'price' }">
                                    Price
                                </div>
                                <div class="select-none">
                                    <span :class="{
                                        'text-blue-600': orderDirection === 'asc' && orderColumn === 'price',
                                        'hidden': orderDirection !== '' && orderDirection !== 'asc' && orderColumn === 'price',
                                    }">&uarr;</span>
                                    <span :class="{
                                        'text-blue-600': orderDirection === 'desc' && orderColumn === 'price',
                                        'hidden': orderDirection !== '' && orderDirection !== 'desc' && orderColumn === 'price',
                                    }">&darr;</span>
                                </div>
                            </div>
                        </th>
                        <th class="px-6 py-3 bg-gray-50 text-left">
                            <span class="text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Image</span>
                        </th>
                        <th class="px-6 py-3 bg-gray-50 text-left">
                            <span class="text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Category</span>
                        </th>
                        <th class="px-6 py-3 bg-gray-50 text-left">
                            <div class="flex flex-row items-center justify-between">
                                <div class="leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                    Created at
                                </div>
                            </div>
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200 divide-solid">
                    <tr v-for="product in products.data">
                        <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 text-gray-900">
                            {{ product.id }}
                        </td>
                        <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 text-gray-900">
                            {{ product.name }}
                        </td>
                        <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 text-gray-900">
                            {{ product.description }}
                        </td>
                        <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 text-gray-900">
                            {{ product.price }}
                        </td>
                        <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 text-gray-900">
                            <img :src="renderProductImage(product.image)" alt="Product Image" class="product-image" style="max-width: 50px; max-height: 50px;" loading="lazy">
                        </td>
                        <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 text-gray-900">
                            {{ getCategoryNames(product.categories) }}
                        </td>
                        <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 text-gray-900">
                            {{ formatDate(product.created_at) }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <div>
                <TailwindPagination :data="products" @pagination-change-page="page => getProducts(page, search_global)" class="mt-4" :limit="10"/>
            </div>

        </div>
    </div>
</template>

<script setup>
import { onMounted, ref, watch } from "vue";
import { TailwindPagination } from 'laravel-vue-pagination';
import useProducts from "@/composables/products";
import moment from 'moment';


const search_global = ref('')
const orderColumn = ref('created_at')
const orderDirection = ref('desc')
const { products, getProducts, renderProductImage } = useProducts()

const updateOrdering = (column) => {
    orderColumn.value = column
    orderDirection.value = (orderDirection.value === 'asc') ? 'desc' : 'asc'
    getProducts(
        1,
        search_global.value,
        orderColumn.value,
        orderDirection.value
    );
}

const getCategoryNames = (categories) => {
    const categoryNameList = categories.map((category) => category.name);
    if (categoryNameList.length > 2) {
        return categoryNameList.slice(0, 2).join(", ") + " ...";
    } else {
        return categoryNameList.join(", ");
    }
};

const formatDate = (date) => {
    return moment(date).format('YYYY-MM-DD');
};

onMounted(() => {
    getProducts()
})

watch(search_global, (current, previous) => {
    getProducts(
        1,
        current
    )
})


</script>
