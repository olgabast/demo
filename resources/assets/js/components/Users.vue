<template>
    <section class="content">
        <div class="row">
            <div class="col-md-8">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Users list</h3>
                    </div>
                    <div class="box-body table-responsive no-padding">
                        <table class="table table-hover">
                            <thead>
                            <tr>
                                <th v-for="column in columns">
                                    <div class="can-order" @click="orderTable(column)">
                                        <span v-if="orderColumn == column" class="fa" v-bind:class="orderDirection == 1 ? 'fa-arrow-up' : 'fa-arrow-down'"></span>
                                        {{column | capitalize}}
                                    </div>
                                    <div v-if="filters.hasOwnProperty(column)">
                                        <input type="text" v-model="filters[column]">
                                    </div>
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr
                                    v-for="record in filteredUsers| orderBy orderColumn orderDirection | limitBy perPage (page-1)*perPage"
                                    @click="selectRecord(record)"
                                    v-bind:class="{'active': selectedRecord.id == record.id}"
                            >
                                <td v-for="column in columns">{{record[column]}}</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="box-footer">
                        <ul class="pagination pagination-sm no-margin pull-right" v-if="pages > 1">
                            <li><a href="#" @click.prevent="page = Math.max(page - 1, 1)">«</a></li>
                            <li v-for="n in pages" v-bind:class="{ 'active' : n + 1 == page}">
                                <a href="#" @click.prevent="page = n + 1">{{n + 1}}</a>
                            </li>
                            <li><a href="#" @click.prevent="page = Math.min(page + 1, pages)">»</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">{{ formData.id ? "Edit" : "Add new"}} record</h3>
                    </div>
                    <!-- /.box-header -->
                    <form role="form">
                        <div class="box-body">
                            <input type="hidden" v-if="formData.id" value="{{formData.id}}">
                            <div class="form-group" v-bind:class="{ 'has-error': errors.email }">
                                <label for="email">Email</label>
                                <input type="text" id="email" class="form-control" placeholder="Select datetime" v-model="formData.email" @click="this.errors = {}">
                                <span class="help-block" v-for="error in errors.email">{{ error }}</span>
                            </div>
                            <div class="form-group" v-bind:class="{ 'has-error': errors.password }">
                                <label for="password">Password</label>
                                <input type="password" id="password" class="form-control" placeholder="Enter password" v-model="formData.password" @click="this.errors = {}">
                                <span class="help-block" v-for="error in errors.password">{{ password }}</span>
                            </div>
                            <div class="form-group" v-bind:class="{ 'has-error': errors.role }">
                                <label for="role">Role</label>
                                <select id="role" class="form-control" v-model="formData.role" @click="this.errors = {}">
                                    <option v-for="role in roles" value="{{ role }}">{{ role }}</option>
                                </select>
                                <span class="help-block" v-for="error in errors.role">{{ error }}</span>
                            </div>

                        </div>

                        <div class="box-footer">
                            <button type="submit" class="btn btn-primary" @click="submitForm()">Submit</button>
                            <button type="submit" class="btn btn-default" v-if="formData.id" @click="selectedRecord = {}">Cancel</button>
                            <button type="submit" class="btn btn-danger pull-right" v-if="formData.id" @click="removeRecord()">Delete</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</template>

<script>
    export default {
        route: {
            data (transition) {
                return this.$http.get('/api/users').then(({data}) => {
                    this.users = data.data;
                });
            }
        },
        data() {
            return {
                users: [],
                columns: ['email', 'role'],
                roles: ["user", "manager", "admin"],
                perPage: 5,
                page: 1,
                orderColumn: 'email',
                orderDirection: -1,
                filters: {
                    'email': '',
                    'role': ''
                },
                selectedRecord: {},
                form: {
                    email: '',
                    password: '',
                    role: 'user'
                },
                errors: {}
            }
        },
        computed: {
            formData() {
                return this.selectedRecord.id ? this.selectedRecord : this.form;
            },
            pages() {
                return Math.ceil(this.filteredUsers.length / this.perPage);
            },
            filteredUsers() {
                let expenses = this.users;
                Object.keys(this.filters).forEach((filter) => {
                    if (this.filters[filter] != "") {
                        expenses = expenses.filter((record) => {
                            return record[filter].indexOf(this.filters[filter]) !== -1;
                        })
                    }
                });
                return expenses;
            },
        },
        methods: {
            selectRecord(record) {
                (this.selectedRecord.id == record.id) ? this.selectedRecord = {} : this.selectedRecord = record;
            },
            submitForm() {
                if (this.formData.id) {
                    this.$http.put('/api/users/' + this.formData.id, this.formData).then(() => {
                        this.selectedRecord = {};
                        this.$dispatch('alert', {type: 'success', message: 'User updated'});
                    }, ({data}) => {
                        if (data.status == 422) {
                            this.errors = data.errors;
                        } else {
                            this.errors = {};
                            if (data.message) {
                                this.$dispatch('alert', {type: 'error', message: data.message});
                            }
                        }
                    });

                } else {
                    this.$http.post('/api/users', this.formData).then(({data}) => {
                        this.users.push(data.data);
                        this.form = {
                            email: '',
                            password: '',
                            role: 'user'
                        };
                        this.$dispatch('alert', {type: 'success', message: 'User created'});
                    }, ({data}) => {
                        if (data.status == 422) {
                            this.errors = data.errors;
                        } else {
                            this.errors = {};
                            if (data.message) {
                                this.$dispatch('alert', {type: 'error', message: data.message});
                            }
                        }
                    });
                }
            },
            removeRecord() {
                return this.$http.delete('/api/users/' + this.selectedRecord.id).then(() => {
                    this.users.$remove(this.selectedRecord);
                    this.selectedRecord = {};
                    this.$dispatch('alert', {type: 'success', message: 'User deleted'});
                }, (data) => {
                    this.$dispatch('alert', {type: 'error', message: data.data.message});
                });
            },
            orderTable(order) {
                if (this.orderColumn == order) {
                    this.orderDirection = -this.orderDirection;
                } else {
                    this.orderColumn = order;
                    this.orderDirection = 1;
                }
            }
        }
    }
</script>
