jQuery(document).ready(function($)
{
    setInfoStatus();

    function setInfoStatus()
    {
        if( $('#FM_isSold').is(':checked') )
        {
            $('.vehicleInfoInput, .vehicleInfoSelect, .vehicleArea').each(function()
            {
                $(this).attr('disabled','disabled');
            });

            $('#vehicle_optiondiv input').each(function()
            {
                $(this).attr('disabled','disabled');
            });
        }
    }
});

