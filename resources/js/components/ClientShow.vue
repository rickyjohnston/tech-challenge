<template>
    <div>
        <h1 class="mb-6">Clients -> {{ client.name }}</h1>

        <div class="flex">
            <div class="w-1/3 mr-5">
                <div class="w-full bg-white rounded p-4">
                    <h2>Client Info</h2>
                    <table>
                        <tbody>
                            <tr>
                                <th class="text-gray-600 pr-3">Name</th>
                                <td>{{ client.name }}</td>
                            </tr>
                            <tr>
                                <th class="text-gray-600 pr-3">Email</th>
                                <td>{{ client.email }}</td>
                            </tr>
                            <tr>
                                <th class="text-gray-600 pr-3">Phone</th>
                                <td>{{ client.phone }}</td>
                            </tr>
                            <tr>
                                <th class="text-gray-600 pr-3">Address</th>
                                <td>{{ client.address }}<br/>{{ client.postcode + ' ' + client.city }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="w-2/3">
                <div>
                    <button class="btn" :class="{'btn-primary': currentTab == 'bookings', 'btn-default': currentTab != 'bookings'}" @click="switchTab('bookings')">Bookings</button>
                    <button class="btn" :class="{'btn-primary': currentTab == 'journals', 'btn-default': currentTab != 'journals'}" @click="switchTab('journals')">Journals</button>
                </div>

                <!-- Bookings -->
                <div class="bg-white rounded p-4" v-if="currentTab == 'bookings'">
                    <div class="flex">
                        <h3 class="mb-3 mr-auto">List of client bookings</h3>

                        <div>
                            <select class="form-control form-control-sm" v-model="bookingSort">
                                <option value="all">
                                    All Bookings
                                </option>
                                <option value="future">
                                    Future Bookings Only
                                </option>
                                <option value="past">
                                    Past Bookings Only
                                </option>
                            </select>
                        </div>
                    </div>

                    <template v-if="client.bookings && client.bookings.length > 0">
                        <table>
                            <thead>
                                <tr>
                                    <th>Time</th>
                                    <th>Notes</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="booking in bookingList" :key="booking.id">
                                    <td>{{ booking.time }}</td>
                                    <td>{{ booking.notes }}</td>
                                    <td>
                                        <button class="btn btn-danger btn-sm" @click="deleteBooking(booking)">Delete</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </template>

                    <template v-else>
                        <p class="text-center">The client has no bookings.</p>
                    </template>

                </div>

                <!-- Journals -->
                <div class="bg-white rounded p-4" v-if="currentTab == 'journals'">
                    <h3 class="mb-3">List of client journals</h3>

                    <form @submit.prevent="addJournalEntry">
                        <div class="form-group">
                            <label for="journal-date">Date</label>
                            <input type="date" class="form-control" id="journal-date" v-model="newJournalDate">
                        </div>
                        <div class="form-group">
                            <label for="journal-text">Text</label>
                            <textarea class="form-control" id="journal-text" rows="3" v-model="newJournalText"></textarea>
                        </div>
                        <div>
                            <button class="btn btn-primary btn-sm">Submit</button>
                        </div>
                    </form>

                    <template v-if="client.journals && client.journals.length > 0">
                        <table>
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Text</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="journal in journals" :key="journal.id">
                                    <td>{{ journal.date }}</td>
                                    <td>{{ journal.text }}</td>
                                    <td>
                                        <button class="btn btn-danger btn-sm" @click="deleteJournal(journal)">Delete</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </template>

                    <template v-else>
                        <p class="text-center">The client has no journals.</p>
                    </template>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    name: 'ClientShow',

    props: ['client'],

    data() {
        return {
            currentTab: 'bookings',
            bookingSort: 'all',
            bookings: Array.from(this.client.bookings),
            journals: Array.from(this.client.journals),
            newJournalDate: null,
            newJournalText: null
        }
    },

    computed: {
        bookingList () {
            switch(this.bookingSort) {
                case 'future':
                    return this.bookings.filter(booking => new Date(booking.start) > new Date());
                case 'past':
                    return this.bookings.filter(booking => new Date(booking.start) < new Date());
                default:
                    return this.bookings;
            }
        }
    },

    methods: {
        switchTab(newTab) {
            this.currentTab = newTab;
        },

        deleteBooking(booking) {
            axios.delete(`/bookings/${booking.id}`)
                .then((response) => {
                    this.bookings = this.bookings.filter(b => b.id !== booking.id);
                });
        },

        addJournalEntry() {
            axios.post(`/clients/${this.client.id}/journals`, {
                    date: this.newJournalDate,
                    text: this.newJournalText
                })
                .then((response) => {
                    this.journals.unshift(response.data);

                    this.newJournalDate = null;
                    this.newJournalText = null;
                });
        },

        deleteJournal(journal) {
            axios.delete(`/clients/${this.client.id}/journals/${journal.id}`)
                .then((response) => {
                    this.journals = this.journals.filter(j => j.id !== journal.id);
                });
        }
    }
}
</script>
