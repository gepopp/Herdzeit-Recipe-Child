<template>
<div>
    <div class="alert alert-info toast" role="alert" v-if="show">
        <h5 v-html="toastData.reason"></h5>
        <hr>
        <div class="row">
            <a :href="toastData.post_link">
            <div class="col-xs-4">
                 <img :src="toastData.img" class="img-responsive"/>
            </div>
            <div class="col-xs-8">
                <strong v-text="toastData.title"></strong>
            </div>
            </a>
        </div>


    </div>
</div>
</template>
<script>
    export default {
        data(){
            return {
                show: false,
                latest: false,
                toastData: false
            }
        },
        mounted: function(){
                var self = this;
                setInterval(function(){
                    self.getLatestToast();
                },15000);
        },
        methods:{
            getLatestToast: function () {
                this.toastHide();
                var self = this;
                jQuery.post(ajaxurl, {action:"latest_toast"}, function(rsp){
                    if(rsp !== 'false'){
                        self.$data.toastData = JSON.parse(rsp);
                        self.toastShow();
                    }
                });

            },
            toastShow: function(){
                this.show = true;
                var self = this;
                setTimeout(function(){
                    self.toastHide();
                },10000);
            },
            toastHide: function () {
                this.show = false;
            }
        }
    }
</script>
<style>
.toast{
    position: fixed;
    bottom: 30px;
    right: 30px;
    width: 250px;
    z-index: 9999;
}
</style>