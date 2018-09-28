$(function(){

    $('#verify_email_enteredEmail').inputmask();

    $('.form-control:first').trigger('focus');

    let newPass = $('#reset_password_password_first');
    let strBar = $('#strengthBar');
    let strText = $('#strengthText');
    let strField = $('#reset_password_strength');

    newPass.on('keyup', function(){
        let strength = zxcvbn(newPass.val());
        strField.val(strength.score);
        switch(strength.score){
            case 0:
                strText.text('Strength (Abysmal)');
                strBar.attr('class', 'progress-bar progress-bar-danger');
                strBar.css('width', '20%');
                break;
            case 1:
                strText.text('Strength (Weak)');
                strBar.attr('class', 'progress-bar progress-bar-warning');
                strBar.css('width', '40%');
                break;
            case 2:
                strText.text('Strength (Alright)');
                strBar.attr('class', 'progress-bar progress-bar-info');
                strBar.css('width', '60%');
                break;
            case 3:
                strText.text('Strength (Good)');
                strBar.attr('class', 'progress-bar progress-bar-primary');
                strBar.css('width', '80%');
                break;
            case 4:
                strText.text('Strength (Excellent)');
                strBar.attr('class', 'progress-bar progress-bar-success');
                strBar.css('width', '100%');
                break;
        }
    });

});