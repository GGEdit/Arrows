function getRoomList(){
    let deferred = new $.Deferred();
    const csrfToken = $('meta[name="csrf-token"]').attr('content');
    $.ajax({
        url: '/rooms',
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