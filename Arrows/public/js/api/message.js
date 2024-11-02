function getMessageRequest(roomId){
    const csrfToken = $('meta[name="csrf-token"]').attr('content');
    return new Promise((resolve, reject) => {
        $.ajax({
            url: `/message/${roomId}`,
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
            },
            processData: false,
            contentType: false
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

function postMessageRequest(roomId, content, attachment = null){
    if((content == "" || content == null) && attachment == null){
        return;
    }
    const csrfToken = $('meta[name="csrf-token"]').attr('content');
    const fd = new FormData();
    if(attachment != null){
        fd.append('attachment', attachment);
    }
    fd.append('room_id', roomId);
    fd.append('content', content);
    return new Promise((resolve, reject) => {
        $.ajax({
            url: '/message',
            method: 'POST',
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

function updateMessageRequest(messageId, content){
    if(content == "" || content == null){
        return;
    }
    const csrfToken = $('meta[name="csrf-token"]').attr('content');
    const fd = new FormData();
    fd.append('content', content);
    return new Promise((resolve, reject) => {
        $.ajax({
            url: `/message/${messageId}`,
            method: 'POST',
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

function deleteMessageRequest(messageId){
    const csrfToken = $('meta[name="csrf-token"]').attr('content');
    return new Promise((resolve, reject) => {
        $.ajax({
            url: `/message/${messageId}`,
            type: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
            },
            dataType: "JSON",
            data: {
                '_method': 'DELETE'
            },
            processData: false,
            contentType: false
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