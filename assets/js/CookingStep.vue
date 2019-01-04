<template>
    <div>
        <div class="col-sm-6">
            <h4 class="section-title ingredients"><img
                    :src="icon"
                    width="22" height="22"
                    style="margin-right: 20px;">Kochschritte</h4>
            <ul class="list-unstyled ingredients-list steps-list">
                <li v-for="(step, index) in steps" v-bind:class="{checked : step.clicked}" @click="toggleClass(step)" >
                    <a class="fake-checkbox"><i class="fa fa-check"></i></a>
                    <img :src="icon" width="18" height="18">
                    <span style="color:#6ba72b; font-weight: 900;" v-text="'Schritt ' + (index + 1) "></span><br>
                    <h5 class="step-title" v-text="step.title"></h5>
                    <p v-html="step.content"></p>
                </li>
            </ul>
        </div>
    </div>
</template>
<script>

    export default {
        name: "cookingstep",
        props: ['id', 'icon'],
        data() {
            return {
                steps: false,
                cooked: false
            }
        },
        beforeMount(){
            this.getSteps();
        },
        methods:{
            getSteps: function () {

                var self = this;
                var cookie = JSON.parse(this.$cookie.get('steps_' + this.id));
                var postData;

                    jQuery.post(
                        ajaxurl,
                        {
                            action: "get_recipe_steps",
                            id: this.id
                        },
                        function(rsp){
                            postData = JSON.parse(rsp);
                            if(cookie){
                                cookie.map(function(value, index){
                                    postData[value -1].clicked = true;
                                });
                            }

                            self.$data.steps = postData;
                        });



            },
            toggleClass: function (step) {
                step.clicked = !step.clicked;
                this.checkAllDone();
                return step.clicked;
            },
            checkAllDone: function () {

                var allDone = false;
                var clickCookie = [];
                this.steps.map(function(value, key){
                    if(value.clicked){
                        clickCookie.push( key + 1 );
                    }
                    allDone = value.clicked;
                });
                this.$cookie.set('steps_' + this.id, JSON.stringify(clickCookie), { expires: '1D' });

                if(allDone && !this.cooked){
                    jQuery.post(ajaxurl, {action: "log_toast", what: 'cooked'});
                    this.cooked = true;
                }
            }
        }

    }


</script>