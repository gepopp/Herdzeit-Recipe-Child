jQuery(document).ready(function($){


    $('.to-shopping-list').click(function(){

        $('#shopping-list-modal').modal('show');


    });

    // $('#btn-calculate-plus').click(function(){
    //     $('#portion-calculator').val(parseInt($('#portion-calculator').val()) + 1);
    //     calculate_ingredients();
    // });
    // $('#btn-calculate-minus').click(function(){
    //     if( $('#portion-calculator').val() > 1) {
    //         $('#portion-calculator').val($('#portion-calculator').val() - 1);
    //         calculate_ingredients();
    //     }
    // });
    //var whatsHref = $('.fwabtn').attr('href');

    //update_whatsapp_href();




    // function update_whatsapp_href(){
    //     $('.fwabtn').attr('href', whatsHref);
    //     var zutaten = '';
    //     var addit = false;
    //     ingredients = $('ul.ingredients-only li:not(".ingredient-title")');
    //
    //     $(ingredients).each(function (i, v) {
    //         text = $(v).text();
    //         if($(v).hasClass('checked')){
    //            text = '~'+text.replace(/(\r\n\t|\n|\r\t)/gm,"")+'~';
    //            addit = true;
    //         }
    //         zutaten += text + '%0A';
    //     });
    //     zutaten = zutaten.replace('#', '*');
    //     if(addit){
    //         zutaten += '%0A%0A%0A ~Durchgestrichene~ Zutaten müssen nicht mehr eingekauft werden.';
    //     }
    //
    //     newHref = whatsHref.replace('{{zutaten}}', zutaten);
    //     newHref = newHref.replace('{{prt}}', $('#portion-calculator').val());
    //
    //     $('.fwabtn').attr('href', newHref);
    // }


    // $('.ingredients-only li').click(function(){
    //     $('.fwabtn').hide();
    //     setTimeout(function(){
    //         update_whatsapp_href();
    //         $('.fwabtn').show();
    //     },500);
    //
    // });
    //
    // function calculate_ingredients() {
    //
    //     var multiplier = $('#portion-calculator').val();
    //     $(calc_ingredients).each(function (i, v) {
    //
    //         much = v.menge * multiplier;
    //
    //         if(v.einheit == 'g' || v.einheit == 'ml'){
    //             much = parseInt( much);
    //         }else if(v.einheit == 'el' || v.einheit == 'tl'){
    //             much = (Math.round(much * 2) / 2);
    //             if(much == 0) much = 0.5;
    //         }else if( v.einheit == 'prise' || v.einheit == 'pr'  ){
    //             much = 1;
    //         }else{
    //             much = (Math.round(much * 4) / 4);
    //             if(much == 0) much = 0.25;
    //         }
    //         much = much.toString();
    //         much = much.replace('.', ',');
    //
    //         old = $('.ingredients-only li')[v.zeile];
    //         text = $(old).text();
    //         text = text.replace(/^[^\s]+/, [much]);
    //
    //         var $contents = $(old).contents();
    //         $contents[$contents.length - 1].nodeValue = text;
    //         update_whatsapp_href();
    //     });
    // }

});