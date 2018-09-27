$(function () {

    let noteView = $('#note_view');
    let addNoteForm = $('#note_form');

    addNoteForm.on('submit', function(){
        $.ajax({
            type: 'POST',
            url: addNoteForm.attr('action'),
            data: new FormData(addNoteForm[0]),
            processData: false,
            contentType: false,
            success: function(data){
                // process data and refresh view
                noteView.html(data);
            },
            error: function(xhr){
                alert('Something has broken. Error: '+xhr.status);
            }
        });

        return false;
    });

});