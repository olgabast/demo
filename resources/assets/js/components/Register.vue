<style>
    .margin-top {
        margin-top: 10px;
    }
</style>

<template>
    <div class="login-box">
        <div class="login-logo">
            <b>Expenses</b> manager
        </div>
        <div class="login-box-body">
            <p class="login-box-msg">Register new account</p>
            <form @submit.prevent="submit()">
                <div class="form-group has-feedback" v-bind:class="{ 'has-error': errors.email }">
                    <input type="email" class="form-control" placeholder="Email" v-model="credentials.email" @click="clearErrors()">
                    <span class="glyphicon glyphicon-user form-control-feedback"></span>
                    <span class="help-block" v-for="error in errors.email">{{ error }}</span>
                </div>
                <div class="form-group has-feedback" v-bind:class="{ 'has-error': errors.password }">
                    <input type="password" class="form-control" placeholder="Password" v-model="credentials.password" @click="clearErrors()">
                    <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                    <span class="help-block" v-for="error in errors.password">{{ error }}</span>
                </div>
                <div class="form-group" v-bind:class="{ 'has-error': password_confirm.length > 0 && !passwordsMatch }">
                    <input type="password" class="form-control" placeholder="Retype password" v-model="password_confirm" debounce="500">
                    <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                    <span class="help-block" v-if="password_confirm.length > 0 && !passwordsMatch">Passwords do not match</span>
                </div>
                <p class="text-center text-red" v-if="error">{{ error }}</p>
                <div class="row">
                    <div class="col-xs-4 col-xs-offset-4">
                        <button type="submit" class="btn btn-primary btn-block btn-flat" :disabled="!passwordConfirmed">Register</button>
                    </div>
                </div>
                <div class="text-center margin-top">
                    <a href="#" v-link="{ path: '/login' }">Sign in</a>
                </div>
            </form>

        </div>
    </div>
</template>


<script>
    export default {
        data() {
            return {
                credentials: {
                    email: '',
                    password: ''
                },
                password_confirm: '',
                errors: {},
                error: ''
            }
        },
        computed: {
            passwordConfirmed() {
                return this.password_confirm != '' && this.passwordsMatch
            },
            passwordsMatch() {
                return this.credentials.password == this.password_confirm
            }
        },
        methods: {
            submit() {
                this.clearErrors();
                this.$http.post('/api/auth/register', this.credentials).then(() => {
                    this.$auth.login(this.credentials, true, '/');
                }, (data) => {
                    this.errors = (data.status == 422) ? data.data.errors : {};
                    this.error = (data.status == 401) ? data.data.message : "";
                });
            },
            clearErrors() {
                this.errors = {};
                this.error = '';
            }
        }
    }
</script>