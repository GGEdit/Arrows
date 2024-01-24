function srcollBottomObj($element){
    if(!$element.get(0)){
        return;
    }
    $element.scrollTop($element.get(0).scrollHeight);
}

function replaceNewLineCode(str){
    if(str == null){
        return '';
    }
    return str.replace(/\r?\n/g, '<br />');
}

function toDateTime(str){
    const dateTime = new Date(str);
    return `${dateTime.getFullYear()}-${(String)(dateTime.getMonth() + 1).padStart(2, '0')}-${(String)(dateTime.getDate()).padStart(2, '0')} 
        ${(String)(dateTime.getHours()).padStart(2, '0')}:${(String)(dateTime.getMinutes()).padStart(2, '0')}:${(String)(dateTime.getSeconds()).padStart(2, '0')}`;
}

function toDate(str){
    const dateTime = new Date(str);
    return `${dateTime.getFullYear()}-${(String)(dateTime.getMonth() + 1).padStart(2, '0')}-${(String)(dateTime.getDate()).padStart(2, '0')}`;
}

function toTime(str){
    const dateTime = new Date(str);
    return `${(String)(dateTime.getHours()).padStart(2, '0')}:${(String)(dateTime.getMinutes()).padStart(2, '0')}`;
}