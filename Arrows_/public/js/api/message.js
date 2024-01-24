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

function postMessage(roomId, content){
    if(content == "" || content == null){
        return;
    }
    const csrfToken = $('meta[name="csrf-token"]').attr('content');
    $.ajax({
        url: '/message',
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
        },
        data:{
            room_id: roomId,
            content: content,
        },
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