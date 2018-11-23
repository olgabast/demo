<style>
    .navbar-text {
        color: #fff;
    }

    #alerts {
        position: fixed;
        right: 0px;
        top: 60px;
        z-index: 99;
    }

    .slide-transition {
        position: relative;
        transition: all .5s ease;
        right: 0;
    }

    .slide-enter, .slide-leave {
        right: -500px;
    }

    .table > thead > tr > th {
        vertical-align: top;
        white-space: nowrap;
    }

    .table > thead > tr > th .can-order {
        cursor: pointer;
    }

    .table > thead > tr > th input {
        border: 1px solid #ccc;
    }
</style>

<template>
    <div class="wrapper">
        <!-- Main Header -->
        <header class="main-header">
            <!-- Logo -->
            <a v-link="'/'" class="logo">
                <!-- logo for regular state and mobile devices -->
                <span class="logo-lg"><b>E</b>xpenses</span>
            </a>

            <!-- Header Navbar -->
            <nav class="navbar navbar-static-top" role="navigation">
                <!-- Sidebar toggle button-->
                <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
                    <span class="sr-only">Toggle navigation</span>
                </a>
                <!-- Navbar Right Menu -->
                <div class="navbar-custom-menu">
                    <ul class="nav navbar-nav">
                        <li><p class="navbar-text username">Logged in as {{ $auth.user()['email'] }}</p></li>
                        <li><a href="#" @click.prevent="logout()">Logout</a></li>
                    </ul>
                </div>
            </nav>
        </header>

        <!-- Left side column. contains the logo and sidebar -->
        <aside class="main-sidebar">
            <!-- sidebar -->
            <section class="sidebar">
                <!-- Sidebar Menu -->
                <ul class="sidebar-menu">
                    <li v-link="{ path: '/', exact: true, activeClass: 'active' }"><a href="#"><i class="fa fa-dashboard"></i> <span>Home</span></a></li>
                    <li
                            v-link="{ path: '/users', activeClass: 'active' }"
                            v-if="$auth.user() && ($auth.user()['role'] == 'manager' || $auth.user()['role'] == 'admin')"
                    >
                        <a href="#"><i class="fa fa-user"></i> <span>Manage users</span></a>
                    </li>
                    <li
                            v-link="{ path: '/expenses', activeClass: 'active' }"
                            v-if="$auth.user() && $auth.user()['role'] == 'admin'"
                    >
                        <a href="#"><i class="fa fa-dollar"></i> <span>Manage expenses</span></a>
                    </li>
                </ul><!-- /.sidebar-menu -->
            </section>
            <!-- /.sidebar -->
        </aside>

        <div id="alerts" class="col-xs-12 col-sm-4">
            <div class="alert alert-{{ alert.type }}" v-for="alert in alerts" @click="this.alerts.$remove(alert)" transition="slide">
                {{ alert.message }}
            </div>
        </div>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <router-view></router-view>
        </div><!-- /.content-wrapper -->

        <!-- Main Footer -->
        <footer class="main-footer">
            <div>(c) 2016 | developed by bast.pro</div>
        </footer>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                alerts: []
            }
        },
        ready() {
            /*
             some of adminLTE scripts does not work after page change
             fixing it here
             */
            this.$nextTick(function () {
                if ($.AdminLTE.layout) {
                    $.AdminLTE.layout.fix(); // fix footer stickiness
                }
            })

            this.$on('alert', function (data) {
                this.alerts.push(data);
                setTimeout(() => { this.alerts.$remove(data) }, 5000);
            })
        },
        methods: {
            logout() {
                this.$auth.logout('/login', true);
            }
        }
    }
</script>