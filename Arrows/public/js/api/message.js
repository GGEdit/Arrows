function getMessage(roomId){
    let deferred = new $.Deferred();
    const csrfToken = $('meta[name="csrf-token"]').attr('content');
    $.ajax({
        url: `/message/${roomId}`,
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
        },
        success: function(data){
            deferred.resolve(data);
        }
    });
    return deferred;
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
    $.ajax({
        url: '/message',
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
        },
        data: fd,
        processData: false,
        contentType: false,
        success: function(response){
            if(response.message == 'my blocked'){
                alert('このユーザーをブロックしているため、送信できませんでした');
            }
            else if(response.message == 'blocked'){
                alert('あなたはブロックされているため、送信できませんでした');
            }
        }
    });
}