<style scoped>
    table th span {
        margin-right: 5px;
    }

    .expenses-controls .btn {
        padding: 1px 10px;
    }

    .expenses-controls .btn.active {
        color: #fff;
        background-color: #3097D1;
        border-color: #3097D1;
    }

    .btn-weeks {
        min-width: 200px;
    }

    .footer-text {
        margin: 4px 0 0 0;
    }
</style>

<template>
    <section class="content">
        <div class="row">
            <div class="col-md-8">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Your expenses list</h3>
                        <div class="expenses-controls pull-right">
                            <button type="button" class="btn btn-default btn-xs" v-bind:class="{'active' : !showWeekly}" @click="showWeekly = false">All</button>
                            <div class="btn-group">
                                <button type="button" class="btn-change-week btn btn-default btn-xs" @click.prevent="changeWeekStart(-7)">«</button>
                                <button type="button" class="btn-weeks btn btn-default btn-xs" v-bind:class="{'active' : showWeekly}" @click="clickWeekly()">{{ weekText }}</button>
                                <button type="button" class="btn-change-week btn btn-default btn-xs" @click.prevent="changeWeekStart(7)">»</button>
                            </div>
                        </div>
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
                                    v-for="record in shownExpenses | orderBy orderColumn orderDirection | limitBy perPage (page-1)*perPage"
                                    @click="selectRecord(record)"
                                    v-bind:class="{'active': selectedRecord.id == record.id}"
                            >
                                <td v-for="column in columns">{{record[column]}}</td>
                            </tr>
                            <tr v-if="shownExpenses.length == 0">
                                <td colspan="4" class="text-center">No records for given period</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="box-footer">
                        <p class="footer-text pull-left"><b>Total:</b> {{total}}</p>
                        <ul class="pagination pagination-sm no-margin pull-right" v-if="pages > 1">
                            <li><a href="#" @click.prevent="page = Math.max(page - 1, 1)">«</a></li>
                            <li v-for="n in pages" v-bind:class="{ 'active' : n + 1 == page}">
                                <a href="#" @click.prevent="page = n + 1">{{n + 1}}</a>
                            </li>
                            <li><a href="#" @click.prevent="page = Math.min(page + 1, pages)">»</a></li>
                        </ul>
                    </div>
                </div>

                <div class="box box-primary" v-if="showWeekly">
                    <div class="box-header with-border">
                        <h3 class="box-title">Average per day</h3>
                    </div>
                    <div class="box-body table-responsive no-padding">
                        <table class="table">
                            <thead>
                            <tr>
                                <td class="text-center" v-for="n in 7">{{moment(weekStart).day(n).format('dddd')}}<br><small>{{moment(weekStart).day(n).format('MMMM D')}}</small></td>
                            </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td v-for="n in 7">{{getAvg(moment(weekStart).day(n))}}</td>
                                </tr>
                            </tbody>
                        </table>
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
                            <div class="form-group" v-bind:class="{ 'has-error': errors.datetime }">
                                <label for="datetime">Datetime</label>
                                <input type="text" id="datetime" class="form-control" placeholder="Select datetime" v-datetimepicker="formData.datetime" @click="this.errors = {}">
                                <span class="help-block" v-for="error in errors.datetime">{{ error }}</span>
                            </div>
                            <div class="form-group" v-bind:class="{ 'has-error': errors.description }">
                                <label for="description">Description</label>
                                <textarea id="description" class="form-control" rows="3" placeholder="Enter description" v-model="formData.description" @click="this.errors = {}"></textarea>
                                <span class="help-block" v-for="error in errors.description">{{ error }}</span>
                            </div>
                            <div class="form-group" v-bind:class="{ 'has-error': errors.amount }">
                                <label for="amount">Amount</label>
                                <input type="text" id="amount" class="form-control" placeholder="00.0" v-model="amount" @click="this.errors = {}">
                                <span class="help-block" v-for="error in errors.amount">{{ error }}</span>
                            </div>
                            <div class="form-group" v-bind:class="{ 'has-error': errors.comment }">
                                <label for="comment">Comment</label>
                                <textarea id="comment" class="form-control" rows="3" placeholder="Enter comment" v-model="formData.comment" @click="this.errors = {}"></textarea>
                                <span class="help-block" v-for="error in errors.comment">{{ error }}</span>
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
                this.weekStart = this.getStartOfCurrentWeek();

                return this.$http.get('/api/expenses').then(({data}) => {
                    this.expenses = data.data;
                });
            }
        },
        data() {
            return {
                moment: moment,
                expenses: [],
                columns: ['datetime', 'description', 'amount', 'comment'],
                perPage: 5,
                page: 1,
                orderColumn: 'datetime',
                orderDirection: -1,
                showWeekly: false,
                weekStart: '',
                filters: {
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
                errors: {},
                error: ''
            }
        },
        computed: {
            formData() {
                return this.selectedRecord.id ? this.selectedRecord : this.form;
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
            shownExpenses() {
                let expenses = this.filteredExpenses;
                if (!this.showWeekly) {
                    return expenses
                }


                return expenses.filter((expense) => {
                    return moment(expense.datetime).isBetween(moment(this.weekStart), moment(this.weekStart).add(7, 'day'));
                })
            },
            pages() {
                return Math.ceil(this.shownExpenses.length / this.perPage);
            },
            amount: {
                get() {
                    return this.formData.amount.toFixed(2);
                },
                set(val) {
                    var number = +val.replace(/[^\d.]/g, '');
                    this.formData.amount = isNaN(number) ? 0 : number;
                }
            },
            total() {
                return this.shownExpenses.reduce(function (total, item) {
                    return total + item.amount
                }, 0).toFixed(2);
            },
            weekText() {
                if (moment(this.weekStart).diff(moment().day(0).startOf('day')) == 0) {
                    return "This week";
                }
                return moment(this.weekStart).format("ll") + " - " + moment(this.weekStart).add(6, 'days').format("ll");
            }
        },
        methods: {
            selectRecord(record) {
                (this.selectedRecord.id == record.id) ? this.selectedRecord = {} : this.selectedRecord = record;
            },
            submitForm() {
                if (this.formData.id) {
                    this.$http.put('/api/expenses/' + this.formData.id, this.formData).then(() => {
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

                } else {
                    this.$http.post('/api/expenses', this.formData).then(({data}) => {
                        this.expenses.push(data.data);
                        this.form = {
                            datetime: '',
                            description: '',
                            amount: 0,
                            comment: ''
                        };
                        this.$dispatch('alert', {type: 'success', message: 'Record created'});
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
                return this.$http.delete('/api/expenses/' + this.selectedRecord.id).then(() => {
                    this.expenses.$remove(this.selectedRecord);
                    this.selectedRecord = {};
                    this.$dispatch('alert', {type: 'success', message: 'Record deleted'});
                }, (data) => {
                    this.$dispatch('alert', {type: 'error', message: data.data.message});
                });
            },
            getStartOfCurrentWeek() {
                return moment().day(0).startOf('day').format();
            },
            orderTable(order) {
                if (this.orderColumn == order) {
                    this.orderDirection = -this.orderDirection;
                } else {
                    this.orderColumn = order;
                    this.orderDirection = 1;
                }
            },
            clickWeekly() {
                /* if we already in week mode than reset to current week*/
                if (this.showWeekly) {
                    this.weekStart = this.getStartOfCurrentWeek();
                }
                this.showWeekly = true;

            },
            changeWeekStart(days) {
                this.showWeekly = true;
                this.weekStart = moment(this.weekStart).add(days, 'days').format();
            },
            getAvg(day) {
                let day_expenses = this.expenses.filter((expense) => {
                    return moment(expense.datetime).startOf('day').diff(day.startOf('day')) == 0;
                });

                if (day_expenses.length == 0) {
                    return 0;
                }

                let day_expenses_total = day_expenses.reduce(function (total, item) {
                    return total + item.amount
                }, 0);

                return (day_expenses_total / day_expenses.length).toFixed(2);
            }
        }
    }
</script>
