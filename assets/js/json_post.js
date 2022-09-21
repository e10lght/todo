let xhr = new XMLHttpRequest();
xhr.open('POST', './XMLRequest.php', true);
xhr.responseType = 'json'; //JSON形式で取得
xhr.addEventListener('load', function(event) {
    console.log(xhr.response.todo_title);
    console.log(xhr.response.num);
});
let fd = new FormData();
fd.append("todo_title","ギュスターヴ");
fd.append("number","XIII");
xhr.send(fd);