import { ref, inject } from 'vue'
import { useRouter } from 'vue-router'

export default function useProducts() {
    const products = ref({})
    const product = ref({})
    const router = useRouter()
    const validationErrors = ref({})
    const isLoading = ref(false)
    const swal = inject('$swal')

    const getProducts = async (
        page = 1,
        search_global = '',
        sort_column = '',
        sort_direction = ''
    ) => {
        axios.get('/api/v1/products?page=' + page +
            '&search_global=' + search_global +
            '&sort_column=' + sort_column +
            '&sort_direction=' + sort_direction)
            .then(response => {
                products.value = response.data.data;
            })
    }

    const getProduct = async (id) => {
        axios.get('/api/v1/products/' + id)
            .then(response => {
                product.value = response.data.data;
            })
    }

    const storeProduct = async (product) => {
        if (isLoading.value) return;

        isLoading.value = true
        validationErrors.value = {}

        let serializedProduct = new FormData();
        for (let item in product) {
            if (product.hasOwnProperty(item)) {
                if (item === 'category_ids' && Array.isArray(product[item])) {
                    product[item].forEach(categoryId => {
                        serializedProduct.append(`${item}[]`, categoryId);
                    });
                } else {
                    serializedProduct.append(item, product[item]);
                }
            }
        }

        axios.post('/api/v1/products', serializedProduct)
            .then(response => {
                router.push({ name: 'products.index' })
                swal({
                    icon: 'success',
                    title: 'Product saved successfully'
                })
            })
            .catch(error => {
                if (error.response?.data) {
                    validationErrors.value = error.response.data.errors
                    isLoading.value = false
                }
            })
    }


    //Function to check if the image is a path or url based
    const renderProductImage = (image) => {
        if (isURL(image)) {
            return image;
        } else {
            const baseUrl = '/storage';
            return `${baseUrl}/${image}`
        }
    };

    // Simple URL validation, you might need a more robust check in your actual implementation
    const isURL = (str) => {
        return /^https?:\/\//.test(str);
    };

    return {
        products,
        product,
        getProducts,
        getProduct,
        storeProduct,
        validationErrors,
        isLoading,
        renderProductImage
    }
}
