$(function () {

    let dataInfoTable = $('#data_info_table');
    let actionModal = $('#action_modal');
    let loadSpinner = $('#action_loading_div');

    dataInfoTable.DataTable().ajax.reload();

    $('.modal_redirect').on('click', function(){
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

    $('.modal_redirect_confirm').on('click', function(){
        if (confirm($(this).attr('data-message'))){
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
        }
    });

});