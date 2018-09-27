$(function () {

    let noteView = $('#note_view');
    let addNoteForm = $('#note_form');

    addNoteForm.on('submit', function(){
        $.ajax({
            type: 'POST',
            url: addNoteForm.attr('action'),
            data: addNoteForm.serialize(),
            success: function(data){
                // process data and refresh view
                noteView.html(data);
            },
            error: function(xhr){
                alert('Something has broken. Error: '+xhr.status);
            }
        });

        // prevent form from submitting
        return false;
    });

    $('.box-comment').on('click', '.edit_note_btn', function(){
        let note_id = $(this).attr('id').replace('note_edit_', '');
        let note_p = $('#note_text_' + note_id);
        let note_text = note_p.text();

        $.ajax({
            type: 'GET',
            url: '/note/update/' + note_id,
            success: function(data){
                // show form
                note_p.html(data);

                // focus text field
                let comment_field = $('#note_update_comment');
                comment_field.trigger('select');

                // set note text back if user leaves focus
                comment_field.on('blur', function(){
                    note_p.html(note_text)
                });

                // submit form
                let note_update_form = $('#note_update');
                note_update_form.on('submit', function(){
                    $.ajax({
                        type: 'POST',
                        url: '/note/update/' + note_id,
                        data: note_update_form.serialize(),
                        success: function(data){
                            note_p.html(data);
                        },
                        error: function(xhr){
                            alert('Something has broken. Error: '+xhr.status);
                        }
                    });

                    // prevent form from submitting
                    return false;
                })

            },
            error: function(xhr){
                alert('Something has broken. Error: '+xhr.status);
            }
        });

    });

    $('.delete_note_btn').on('click', function(){
        if (confirm('Are you sure you want to remove this comment?')){
            let note_id = $(this).attr('id').replace('note_delete_', '');
            let note_p = $('#note_text_' + note_id);
            let comment_box = $('#comment_' + note_id);

            $.ajax({
                type: 'GET',
                url: '/note/delete/' + note_id,
                success: function(data){
                    note_p.html(data);

                    // remove comment box
                    comment_box.remove();
                },
                error: function(xhr){
                    alert('Something has broken. Error: '+xhr.status);
                }
            })
        }
    });

});