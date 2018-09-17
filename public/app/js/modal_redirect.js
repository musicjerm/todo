$(function (){
    let loadSpinner = $('#action_loading_div');
    let actionModal = $('#action_modal');
    let modalRedirect = $('.modal_redirect');

    modalRedirect.on('click', function(){
        $.ajax({
            type: 'GET',
            url: $(this).attr('data-href'),
            success: function(data){
                actionModal.html(data)
            },
            error: function(xhr){
                alert('Something has broken. Error: '+xhr.status)
            }
        });
        loadSpinner.show();
    });
});