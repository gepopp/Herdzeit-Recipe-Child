<template>
    <div class="col-sm-6">
        <h4 class="section-title"><i class="fa fa-eyedropper"></i>Zutaten</h4>
        Rezept für:
        <div class="input-group">
            <input type="text" :value="basePortion" id="portion-calculator" class="form-control" disabled="true">
            <div class="input-group-addon hidden-print"  style="border: none" @click="decrementProtion"><span class="fa fa-minus" style="color:#6ba72b"></span></div>
            <div class="input-group-addon hidden-print"  style="border: none" @click="incrementPortion"><span class="fa fa-plus" style="color:#6ba72b"></span></div>
        </div>
        Portionen.
        <hr>
        <shoppinglistwidget :user="user" :portions="parseInt(basePortion)"></shoppinglistwidget>
        <hr>
        <input type="hidden" value="" id="portions-base">
        <ul class="list-unstyled ingredients-list ingredients-only">
            <li v-for="i in ingData"
                v-html="detectHeadlines(i)"
                v-bind:class="{checked: i.checked }"
                @click="strokeIt(i)"></li>
        </ul>
        <whatsapp></whatsapp>

    </div>
</template>
<script>
    export default {
        name: "ingredients",
        props: ['ingredient-list', 'portions', 'id', 'user'],
        data(){
            return{
                basePortion: false,
                ingData: calc_ingredients,
            }
        },
        beforeMount(){
            this.setIngList();
            this.setIngData();
        },
        watch:{
          basePortion: function(port){
              this.calculate_ingredients(port);
          }
        },
        methods:{
            setIngData: function(){
               var cookie = JSON.parse(this.$cookie.get('ing_' + this.id));
               if(cookie == null) return;
               this.ingData = cookie;
            },
            strokeIt: function(i){

                if(i.headline) return;

                i.checked = !i.checked;

                Event.$emit('ingChange', {portions:this.basePortion, inredients:this.ingData});
                this.$cookie.set('ing_' + this.id, JSON.stringify(this.ingData), { expires: '1D' });
            },
            isChecked:function(i){
                return i.checked;
            },
            incrementPortion:function(){
               return this.basePortion++;
            },
            decrementProtion:function(){
                if(this.basePortion > 1){
                    return this.basePortion--;
                }
            },
            detectHeadlines: function (line) {
                if(line.headline){
                    return '<h5>' + line.text + '</h5>';
                } else {
                    return '<a class="fake-checkbox"><i class="fa fa-check"></i></a>' + line.text;
                }
            },
            setIngList: function(){
              this.ingList = this.ingredientList.split("\n");
              this.basePortion = this.portions;
            },
            calculate_ingredients(port) {

                this.ingData.forEach((line, index)  => {

                    if(line.headline) return;

                    let much = line.menge * port;

                    if(line.einheit == 'g' || line.einheit == 'ml'){
                        much = parseInt( much);
                    }else if(line.einheit == 'el' || line.einheit == 'tl'){
                        much = (Math.round(much * 2) / 2);
                        if(much == 0) much = 0.5;
                    }else if( line.einheit == 'prise' || line.einheit == 'pr'  ){
                        much = 1;
                    }else{
                        much = (Math.round(much * 4) / 4);
                        if(much == 0) much = 0.25;
                    }
                    much = much.toString();
                    much = much.replace('.', ',');

                    this.ingData[line.zeile].text = this.ingData[line.zeile].text.replace(/^[^\s]+/, [much]);
                });
                Event.$emit('ingChange', {portions:this.basePortion, inredients:this.ingData});
            },
        }
    }

</script>

<style scoped>

</style>