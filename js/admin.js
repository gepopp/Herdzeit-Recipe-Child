jQuery(document).ready(function($){
    var ing = [];
	var errors = [];
	var all;

    $('#update-ing-table').click(function (e) {
        $('#error-links').html('');
    	e.preventDefault();
        $.post(
            ajaxurl,
            {
                action: "collect_all_ingredients"
            },
            function (rsp) {
                $('#update-ing-table-feedback tbody').html('');
                ing = (JSON.parse(rsp));
                all = ing.length;
				var current;
                $(ing).each(function( index, value){

                    if(value.bezeichnung != ""){
                        var error = "OK";
                    }else{
                        var error = '<span style="font-weight:bolder; color:red">Fehler</span>';
                        errors.push(index);
					}


					$('#update-ing-table-feedback tbody').append('<tr id="'+index+'" class="ing-row">' +
						'<td >'+index+'</td>' +
						'<td>'+value.menge+'</td>' +
						'<td class="eh">'+value.einheit+'</td>' +
						'<td class="bez">'+value.bezeichnung+'</td>' +
						'<td>'+error+'</td>' +
						'<td><a href="'+value.link+'" target="_blank">bearbeiten</a></td>' +
						'</tr>');
				});
                if(errors.length){
                	$(errors).each(function (index, value) {

						$('#error-links').append('<a href="#' + value + '">Zeile ' + value + ' </a>');
                    })
				}else{
                	$('#update-now').prop('disabled', false);
				}
            }
        );
    });

    $('#update-now').click(function(e){
    	e.preventDefault();

        update_rows();
	});
    function update_rows() {

        if(ing.length == 0) return;

        current = ing.shift();

        $.post(
        	ajaxurl,
			{
				action: "update_ing_table",
				data: current
			},
			function (rsp) {
        		$('#' + (all - ing.length )).children().last().text(rsp);
                $('html, body').animate({
                    scrollTop: $("#" + (all - ing.length )).offset().top - 100
                });
                update_rows();

            }
		);

    }

    $('.wg-select').change(function(){

        $(this).parent().parent().find('.spinner').first().css('visibility', 'inherit');
        sel = $(this);
       $.post(
           ajaxurl,
           {
               action: "update_warengruppe",
               id: $(this).data('id'),
               gruppe: $(this).val()
           },
           function(rsp){

               if(rsp != 'false')
               sel.parent().parent().find('.spinner').first().css('visibility', 'hidden');
           }
       );

        console.log();
        console.log();



    });

    $('.immerzuhause').bind('change', function(){

        $(this).parent().parent().find('.spinner').first().css('visibility', 'inherit');
        sel = $(this);
        $.post(
            ajaxurl,
            {
                action: "update_immerzuhause",
                id: $(this).data('id'),
                immerzuhause: $(this).is(':checked')
            },
            function(rsp){

                if(rsp != 'false')
                    sel.parent().parent().find('.spinner').first().css('visibility', 'hidden');
            }
        );
    });





    var states = new Bloodhound({
        datumTokenizer: Bloodhound.tokenizers.obj.whitespace('bezeichnung'),
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        local: ingredients.all,

    });

    $('#bloodhound .typeahead').typeahead({
            hint: true,
            highlight: true,
            minLength: 1
        },
        {
            name: 'states',
            source: states,
            templates: {
                suggestion: function (data) {
                    return '<p style="padding:15px; border-bottom:1px solid lightgray;"><strong>' + data.einheit + ' ' + data.bezeichnung + '</strong></p>';
                }
            },
            display: function (data) {
                return data.einheit + ' ' + data.bezeichnung;
            },
        },

);


    $('.typeahead').on('typeahead:selected', function (e, datum) {

        var text = $('#recipe_ingredient-sm-field-0').val();
        text += "\r";
        text += $('.helper-menge').val();
        text += ' ' + datum.einheit + ' ' + datum.bezeichnung;
        var text = $('#recipe_ingredient-sm-field-0').val(text);
        $('.typeahead').typeahead('val', '');
        $('.helper-menge').val('').focus();
    });


});
