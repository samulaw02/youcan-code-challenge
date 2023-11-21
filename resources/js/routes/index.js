import { createRouter, createWebHistory } from 'vue-router';
import AuthenticatedLayout from '@/layouts/Authenticated.vue';
import ProductIndex from '@/components/Products/Index.vue'
import ProductCreate from '@/components/Products/Create.vue'


const routes = [
    {
        path: '/',
        component: AuthenticatedLayout,
        children: [
            {
                path: '/',
                redirect: { name: 'products.index' },
            },
            {
                path: '/products',
                name: 'products.index',
                component: ProductIndex,
                meta: { title: 'Products' }
            },
            {
                path: '/products/create',
                name: 'products.create',
                component: ProductCreate,
                meta: { title: 'Add new post' }
            },
        ]
    }
]

export default createRouter({
    history: createWebHistory(),
    routes
})
