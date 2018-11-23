<template>
    <section class="content">
        <div class="row">
            <div v-if="$loadingRouteData">Loading ...</div>
            <div v-if="!$loadingRouteData" class="col-md-8">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">All expenses list</h3>
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
                                    v-for="record in filteredExpenses | orderBy orderColumn orderDirection | limitBy perPage (page-1)*perPage"
                                    @click="selectRecord(record)"
                                    v-bind:class="{'active': selectedRecord.id == record.id}"
                            >
                                <td v-for="column in columns">{{record[column]}}</td>
                            </tr>
                            <tr v-if="expenses.length == 0">
                                <td colspan="4" class="text-center">No records in database</td>
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
            <div class="col-md-4" v-if="selectedRecord.id">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Edit record</h3>
                    </div>
                    <!-- /.box-header -->
                    <form role="form">
                        <div class="box-body">
                            <input type="hidden" v-if="selectedRecord.id" value="{{selectedRecord.id}}">
                            <div class="form-group" v-bind:class="{ 'has-error': errors.datetime }">
                                <label for="datetime">Datetime</label>
                                <input type="text" id="datetime" class="form-control" placeholder="Select datetime" v-datetimepicker="selectedRecord.datetime" @click="this.errors = {}">
                                <span class="help-block" v-for="error in errors.datetime">{{ error }}</span>
                            </div>
                            <div class="form-group" v-bind:class="{ 'has-error': errors.description }">
                                <label for="description">Description</label>
                                <textarea id="description" class="form-control" rows="3" placeholder="Enter description" v-model="selectedRecord.description" @click="this.errors = {}"></textarea>
                                <span class="help-block" v-for="error in errors.description">{{ error }}</span>
                            </div>
                            <div class="form-group" v-bind:class="{ 'has-error': errors.amount }">
                                <label for="amount">Amount</label>
                                <input type="text" id="amount" class="form-control" placeholder="00.0" v-model="amount" @click="this.errors = {}">
                                <span class="help-block" v-for="error in errors.amount">{{ error }}</span>
                            </div>
                            <div class="form-group" v-bind:class="{ 'has-error': errors.comment }">
                                <label for="comment">Comment</label>
                                <textarea id="comment" class="form-control" rows="3" placeholder="Enter comment" v-model="selectedRecord.comment" @click="this.errors = {}"></textarea>
                                <span class="help-block" v-for="error in errors.comment">{{ error }}</span>
                            </div>
                        </div>

                        <div class="box-footer">
                            <button type="submit" class="btn btn-primary" @click="submitForm()">Submit</button>
                            <button type="submit" class="btn btn-default" v-if="selectedRecord.id" @click="selectedRecord = {}">Cancel</button>
                            <button type="submit" class="btn btn-danger pull-right" v-if="selectedRecord.id" @click="removeRecord()">Delete</button>
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
                return this.$http.get('/api/expenses/all').then(({data}) => {
                    this.expenses = data.data;
                });
            }
        },
        data() {
            return {
                moment: moment,
                expenses: [],
                columns: ['user_email', 'datetime', 'description', 'amount', 'comment'],
                perPage: 5,
                page: 1,
                orderColumn: 'datetime',
                orderDirection: -1,
                filters: {
                    'user_email': '',
                    'datetime': '',
                    'description': '',
                    'comment': ''
                },
                selectedRecord: {},
                form: {
                    datetime: '',
                    description: '',
                    amount: 0,
                    comment: ''
                },
                errors: {}
            }
        },
        computed: {
            pages() {
                return Math.ceil(this.filteredExpenses.length / this.perPage);
            },
            amount: {
                get() {
                    return this.selectedRecord.amount.toFixed(2);
                },
                set(val) {
                    var number = +val.replace(/[^\d.]/g, '');
                    this.selectedRecord.amount = isNaN(number) ? 0 : number;
                }
            },
            filteredExpenses() {
                let expenses = this.expenses;
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
                this.$http.put('/api/expenses/' + this.selectedRecord.id, this.selectedRecord).then(() => {
                    this.selectedRecord = {};
                    this.$dispatch('alert', {type: 'success', message: 'Record updated'});
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
            },
            removeRecord() {
                return this.$http.delete('/api/expenses/' + this.selectedRecord.id).then(() => {
                    this.expenses.$remove(this.selectedRecord);
                    this.selectedRecord = {};
                    this.$dispatch('alert', {type: 'success', message: 'Record deleted'});
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
