<template>
    <div>
        <a class="btn btn-success btn-block" @click="showModal()">
            <i class="fa fa-List" style="padding-top: 10px;"></i> Auf die Einkaufsliste
        </a>
        <modal name="shopping-list-modal" height="450px">
            <div class="container">
                <div class="row">
                    <div class="col-xs-12">
                        <h4>Auf eine Einkaufliste setzten <span class="pull-right"><i class="fa fa-close"
                                                                                      @click="closeModal()"></i> </span>
                        </h4>
                    </div>
                    <hr>
                    <div class="col-xs-12" v-if="user_id == 0">
                        <h5>Bitte erst einloggen!</h5>
                        <p :class="msg.class" v-html="msg.text"></p>
                        <div class="form-group">
                            <label>E-Mail / Nutzername</label>
                            <input class="form-control" type="text" v-model="loginCredetials.user"/>
                        </div>

                        <div class="form-group">
                            <label>Passwort</label>
                            <input class="form-control" type="password" v-model="loginCredetials.pw"/>
                        </div>

                        <div class="form-group">
                            <input type="submit" class="btn btn-block btn-success" @click="login()"/>
                        </div>

                        <p class="text-center">oder</p>

                        <div class="form-group">
                            <a class="btn btn-block btn-success" href="/login-register/"
                               target="_blank">Registrieren</a>
                        </div>
                    </div>
                    <div class="col-xs-12" v-else>
                        <div class="form-group" v-if="lists.length < 3">
                            <h5>Neue Liste</h5>
                            <p :class="msg.class" v-html="msg.text"></p>
                            <div class="row">
                                <div class="col-xs-10">
                                    <input type="text" placeholder="Listenname" class="form-control"
                                           v-model="listName"/>
                                </div>
                                <div class="col-xs-2">
                                    <button class="btn btn-default btn-block" @click="newlist()"><i
                                            class="fa fa-pencil"></i></button>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <h5>Deine Listen</h5>
                        <p :class="msg.class" v-html="msg.text" v-if="lists.length >= 3"></p>
                        <table class="table table-bordered table-responsive table-condensed">
                            <thead>
                            <tr>
                                <th>Liste</th>
                                <th><i class="fa fa-plus"></i></th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="(list,index) in lists">
                                <td v-text="list.name"></td>
                                <td>
                                    <ul class="list-inline">

                                        <li data-toggle="tooltip" data-placement="top" title="Auf die Liste setzten">
                                            <button class="btn btn-primary" @click="addToList(list)">
                                                <i class="fa fa-plus"></i>
                                            </button>
                                        </li>
                                    </ul>
                                </td>
                            </tr>
                            <tr v-if="lists.length == 0">
                                <td colspan="2" class="text-center">Keine Listen vohanden. Erstelle deine erste
                                    Einkaufsliste.
                                </td>
                            </tr>
                            </tbody>
                            <tfoot>
                            </tfoot>
                        </table>


                    </div>
                </div>
            </div>
        </modal>
    </div>
</template>
<script>
    export default {
        name: "shoppinglistwidget",
        props: ['user', 'portions'],
        data() {
            return {
                closeReload: false,
                listName: "",
                user_id: parseInt(this.user),
                msg: {
                    class: "warning",
                    text: "&nbsp;"
                },
                lists: [],
                loginCredetials: {
                    user: "",
                    pw: "",
                },

            }
        },
        beforeMount: function () {
            if (this.user_id != 0) {
                this.getLists();
            }
        },
        methods: {
            closeModal: function () {
                if (this.closeReload) location.reload();
                this.$modal.hide('shopping-list-modal');
            },
            showModal: function () {
                this.$modal.show('shopping-list-modal');
            },
            login: function () {

                var self = this;

                jQuery.post(
                    ajaxurl,
                    {
                        action: "tryLogin",
                        data: self.$data.loginCredetials
                    },
                    function (rsp) {
                        var login = JSON.parse(rsp);
                        if (login.loggedin) {
                            self.$data.user_id = login.user;
                            self.getLists();
                            self.closeReload = true;
                        } else {
                            self.$data.msg.text = login.message;
                            self.$data.msg.class = "text-warning";
                        }
                    });
            },
            newlist: function () {

                var listName = this.listName;
                var portions = this.portions;
                var self = this;

                if (listName == "") {
                    self.$data.msg.text = "Bitte gib einen Listennamen ein.";
                    self.$data.msg.class = "text-warning";
                    this.eraseMessage();
                } else {

                    $.post(
                        ajaxurl,
                        {
                            action: "add_new_shopping_list",
                            portions: portions,
                            listName: listName
                        },
                        function (rsp) {
                            if (rsp == 1) {
                                self.$data.listName = "";
                                self.$data.msg.text = "Liste erfolgreich erstellt und Rezept hinzugefügt.";
                                self.$data.msg.class = "text-success";
                                self.getLists();
                            }
                        }
                    );
                    this.eraseMessage();
                }
            },
            eraseMessage: function () {
                setTimeout(() => {
                    this.msg.text = "&nbsp;";
                    this.msg.class = "text-warning";
                }, 3000);
            },
            getLists: function () {

                var self = this;

                $.post(
                    ajaxurl,
                    {
                        action: "get_widget_shopping_lists",
                        user: self.$data.user_id
                    },
                    function (rsp) {
                        if (rsp != "empty") {
                            self.$data.lists = JSON.parse(rsp);
                        } else {
                            self.$data.lists = [];
                        }

                    }
                );

            },
            addToList: function (list) {

                var self = this;

                jQuery.post(
                    ajaxurl,
                    {
                        action: "add_to_shopping_list",
                        listId: list.id,
                        portions: this.portions
                    },
                    function (rsp) {
                        var update = JSON.parse(rsp);
                        self.$data.msg.text = update.msg;
                        self.$data.msg.class = update.added == 1 ? "text-success" : "text-warning";
                    }
                );
            }
        }
    }
</script>
<style>
    .v--modal {
        max-width: 100%;
    }
</style>