$( function() {
    let from = $( "#fromDate" )
      .datepicker({
        defaultDate: "+1w",
        showAnim: 'drop',
        minDate : new Date(), // Ne pas autoriser la sélection d'une date antérieur de la date d'aujourd'hui
        dateFormat: "yy-mm-dd",
        changeMonth: true,
        numberOfMonths: 1
      })
      .on( "change", function() {
        to.datepicker( "option", "minDate", getDate( this ) );
      }),
    to = $( "#toDate" ).datepicker({
      defaultDate: "+1w",
      showAnim: 'drop',
      dateFormat: "yy-mm-dd",
      changeMonth: true,
      numberOfMonths: 1
    })
    .on( "change", function() {
      from.datepicker( "option", "maxDate", getDate( this ) );
    });

  function getDate( element ) {
    var date;
    let dateFormat= "yy-mm-dd";
    try {
      date = $.datepicker.parseDate( dateFormat, element.value );
    } catch( error ) {
      date = null;
    }
    return date;
  }
} );
