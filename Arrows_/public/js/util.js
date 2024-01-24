function srcollBottomObj($element){
    if(!$element.get(0)){
        return;
    }
    $element.scrollTop($element.get(0).scrollHeight);
}

function replaceNewLineCode(str){
    return str.replace(/\r?\n/g, '<br />');
}

function toDateTime(str){
    const dateTime = new Date(str);
    return `${dateTime.getFullYear()}-${dateTime.getMonth() + 1}-${dateTime.getDate()} 
        ${dateTime.getHours()}:${dateTime.getMinutes()}:${dateTime.getSeconds()}`;
}