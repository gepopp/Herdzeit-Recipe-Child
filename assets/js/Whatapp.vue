<template>
<div class="visible-xs">
        <a target="_blank" :href="message" class="btn btn-success btn-block" @click="logToast">
            <i class="fa fa-whatsapp" style="padding-top: 10px;"></i> Zutaten per Whatsapp teilen.
        </a>
</div>


</template>
<script>
    export default {
        data(){
          return{
              whatsAppText: '',
              portions: false,
              ingredients: false,
              message: ''
          }
        },

        methods:{

            logToast: function(){
                jQuery.post(ajaxurl, {action: "log_toast", what: 'whatsapp'});
            },
            buildWhatsappMessage: function(){

                var checkSentece = false;
                var message =  'whatsapp://send?text=' + window.location.href;
                message += "%0A%0A%0A";
                message += "Zutaten für " + this.portions + " Portionen."
                message += "%0A%0A%0A";
                this.ingredients.forEach(function(item){
                    if(item.checked){
                        message +=  '~' + item.text.trim() + '~%0A';
                        checkSentece = true;
                    }else if(item.headline){
                        message +=  '*' + item.text.trim() + '*%0A';
                    }else{
                        message +=   item.text.trim() + '%0A';
                    }
                });
                if(checkSentece){
                    message += '%0A%0A%0A ~Durchgestrichene~ Zutaten müssen nicht mehr eingekauft werden.';
                }
                this.message = message;
            }
        },
        created(){
            Event.$on('ingChange', (data) =>{
                this.portions = data.portions;
                this.ingredients = data.inredients;
                this.buildWhatsappMessage();
            })
        }

    }
</script>