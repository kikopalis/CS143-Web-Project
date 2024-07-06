$(document).ready(function () {
    $('#loginform').on('shown.bs.modal', function() {
        $('#username').focus();
    })

    $('#loginform').on('submit', function (e) { 
        e.preventDefault();

        $.ajax({
            type: "GET",
            url: "auth.php",
            data: $(this).serialize(),
            dataType: "html",
            success: function (response) {
                if (response == 'Incorrect password'){
                    $('#userpassword').addClass('danger');
                    $('#helpId-pass').html('*'+response);
                    $('#helpId-pass').addClass('danger-text');

                    $('#username').removeClass('danger');
                    $('#helpId-user').html('Input your username');
                    $('#helpId-user').removeClass('danger-text');
                }
                else if(response == 'Incorrect username'){
                    $('#username').addClass('danger');
                    $('#helpId-user').html('*'+response);
                    $('#helpId-user').addClass('danger-text');
                    
                    $('#userpassword').removeClass('danger');
                    $('#helpId-pass').html('Input your password');
                    $('#helpId-pass').removeClass('danger-text');
                }
                else{
                    window.location.replace(response);
                }
            }
        });
    });
});
