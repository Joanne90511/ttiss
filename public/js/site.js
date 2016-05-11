/**
 * Created by mat on 23/03/16.
 */
$('#hideMenu').click(function (event) {
    event.preventDefault();
    $('#wrapper').addClass('toggled');
    setTimeout(function () {
        $('#showMenu').show();
    },500);

});

$('#showMenu').click(function (event) {
    event.preventDefault();
    $('#showMenu').hide();
    $('#wrapper').removeClass('toggled');

});


