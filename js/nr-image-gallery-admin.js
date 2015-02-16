 jQuery(document).ready(function($) {

        $("#gallerysort").sortable({
		start : function(event, ui) {
			ui.item.addClass('active');
		},
		stop : function(event, ui) {

				$.each($('#gallerysort tr'), function(index, event) {

                   $(this).children('td').find('.edit_imageSort').val(parseInt(index, 10)+0);
			  });

		}
	});

     if($("#nri-imageresize").val() == ''){
       $('#nri-imageresize').val('false');
     }

     $("#nri-imageresize").change(function(){
    if($(this).is(":checked")){
      $(this).val('true');
    }
    else{
      $(this).val('false');
    }
    });


     /*if($("#nri-slidercontrols").val() == ''){
       $('#nri-slidercontrols').val('true');
        $('#nri-slidercontrols').attr('checked', 'checked');
     }
     */
     $("#nri-slidercontrols").change(function(){
    if($(this).is(":checked")){
      $(this).val('true');
    }
    else{
      $(this).val('false');
    }
    });

    /*
    if($("#nri-slidermarkers").val() == ''){
       $('#nri-slidermarkers').val('true');
        $('#nri-slidermarkers').attr('checked', 'checked');
     }
     */

     $("#nri-slidermarkers").change(function(){
    if($(this).is(":checked")){
      $(this).val('true');
    }
    else{
      $(this).val('false');
    }
    });

    /*
     if($("#nri-slidercaptions").val() == ''){
       $('#nri-slidercaptions').val('true');
        $('#nri-slidercaptions').attr('checked', 'checked');
     }
     */

     $("#nri-slidercaptions").change(function(){
    if($(this).is(":checked")){
      $(this).val('true');
    }
    else{
      $(this).val('false');
    }
    });

    /*
       if($("#nri-sliderresponsive").val() == ''){
       $('#nri-sliderresponsive').val('true');
        $('#nri-sliderresponsive').attr('checked', 'checked');
     }
     */

     $("#nri-sliderresponsive").change(function(){
    if($(this).is(":checked")){
      $(this).val('true');
    }
    else{
      $(this).val('false');
    }
    });


    $(".show-hide-nri-advanced").click(function(){
      text = $('.show-hide-nri-advanced').html();
      //alert(text);
      if(text == 'Show') $('.show-hide-nri-advanced').html('Hide');
      if(text == 'Hide') $('.show-hide-nri-advanced').html('Show');
        $(".nri-advanced").toggle();
});

    });