function notifyConferenceRequest(room_id, meet_name){
    const csrfToken = $('meta[name="csrf-token"]').attr('content');
    const fd = new FormData();
    fd.append('room_id', room_id);
    fd.append('meet_name', meet_name);
    return new Promise((resolve, reject) => {
        $.ajax({
            url: `/meet/notify_conference`,
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
            },
            data: fd,
            processData: false,
            contentType: false,
        }).then(
            function(result){
                resolve(result);
            },
            function(){
                reject();
            }
        )
    });
}

function notifyTerminateConferenceRequest(room_id, meet_name){
    const csrfToken = $('meta[name="csrf-token"]').attr('content');
    const fd = new FormData();
    fd.append('room_id', room_id);
    fd.append('meet_name', meet_name);
    return new Promise((resolve, reject) => {
        $.ajax({
            url: `/meet/notify_terminate_conference`,
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
            },
            data: fd,
            processData: false,
            contentType: false,
        }).then(
            function(result){
                resolve(result);
            },
            function(){
                reject();
            }
        )
    });
}