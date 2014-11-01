$(function(){

    /**
     * Date picker
     */

    var datePickerOptions = {
        weekStart: 1,
        autoclose : true,
        format: "yyyy-mm-dd"
    };

    $('.input-group.date').datepicker(datePickerOptions);

    $(document).on('focus',"#fee-from", function(){
        $(this).datepicker(datePickerOptions).on('changeDate', function() {
            var date = moment($(this).val());
            date.add(1, 'years');
            $('#fee-to').val(date.format("YYYY-MM-DD"));
        });
    });

});