import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'

export function queryEmail() {
    const email = ref('')
    const router = useRoute()

    onMounted(() => {
        if(router.query.email) {
            email.value = route.query.email
        }
    })

    return { email }
}