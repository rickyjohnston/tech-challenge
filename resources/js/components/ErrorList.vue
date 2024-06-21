<template>
    <div v-if="errorList" class="invalid-feedback error" role="alert">
        <strong v-for="(error, index) in errorList" :key="index" v-text="error"></strong>
    </div>
</template>

<script>
export default {
    name: 'ErrorList',

    props: {
        errorList: {
            type: [null, Array],
            required: false,
            default: null
        }
    }
}
</script>
