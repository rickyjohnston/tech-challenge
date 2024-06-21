<template>
    <div>
        <h1 class="mb-6">Clients -> Add New Client</h1>

        <div class="max-w-lg mx-auto">
            <div class="form-group">
                <label for="name">Name</label>
                <input
                    type="text"
                    id="name"
                    class="form-control"
                    :class="{ 'is-invalid': errors.name }"
                    v-model="client.name"
                >
                <ErrorList :errorList="errors.name" />
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input
                    type="email"
                    id="email"
                    class="form-control"
                    :class="{ 'is-invalid': errors.email }"
                    v-model="client.email"
                >
                <ErrorList :errorList="errors.email" />
            </div>
            <div class="form-group">
                <label for="phone">Phone</label>
                <input
                    type="tel"
                    id="phone"
                    class="form-control"
                    :class="{ 'is-invalid': errors.phone }"
                    v-model="client.phone"
                >
                <ErrorList :errorList="errors.phone" />
            </div>
            <div class="form-group">
                <label for="name">Address</label>
                <input type="text" id="address" class="form-control" v-model="client.address">
            </div>
            <div class="flex">
                <div class="form-group flex-1">
                    <label for="city">City</label>
                    <input type="text" id="city" class="form-control" v-model="client.city">
                </div>
                <div class="form-group flex-1">
                    <label for="postcode">Postcode</label>
                    <input type="text" id="postcode" class="form-control" v-model="client.postcode">
                </div>
            </div>

            <div class="text-right">
                <a href="/clients" class="btn btn-default">Cancel</a>
                <button @click="storeClient" class="btn btn-primary">Create</button>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import ErrorList from './ErrorList.vue';

export default {
    name: 'ClientForm',

    components: {
        ErrorList
    },

    data() {
        return {
            client: {
                name: '',
                email: '',
                phone: '',
                address: '',
                city: '',
                postcode: '',
            },
            errors: {}
        }
    },

    methods: {
        storeClient() {
            axios.post('/clients', this.client)
                .then((data) => {
                    window.location.href = data.data.url;
                })
                .catch(errors => {
                    this.errors = Object.assign({}, errors.response.data.errors);
                });
        }
    }
}
</script>
