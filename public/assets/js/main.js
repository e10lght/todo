"use strict";

// 左の要素
const r_content = document.querySelector(".r-content");
// 右の要素
const l_content = document.querySelector(".l-content");
// TODO要素の配列
const content = document.querySelectorAll(".content");
// チェック要素
const check = document.querySelectorAll("input[type='checkbox']");

for (let i = 0; i < check.length; i++) {
    check[i].addEventListener("change", () => {
        if (check[i].checked) {
            r_content.appendChild(content[i]);
        } else {
            l_content.appendChild(content[i]);
        }
    })
}


// メニューバーを表示する
const img = document.querySelector("#img");
const menu = document.querySelector(".menu_nav");
img.addEventListener("click", () => {
    menu.classList.toggle("active");
});


// JSからPHPにリクエストを送る（）
const xhr = new XMLHttpRequest();
const btn = document.querySelector("button");
const newdiv = document.createElement("div");



btn.addEventListener("click", (event) => {

    xhr.open("POST", "./access.php", true);
    xhr.responseType = "json"; //結果をテキスト形式で取得

    const todo_list = document.querySelectorAll(".l-content > .content");
    const archive_list = document.querySelectorAll(".r-content > #memo");

    const todo_title = [];
    for (const value of todo_list) {
        const title = parseInt(value.title, 10);
        todo_title.push(title);
    }

    let archive_title = [];
    for (const value of archive_list) {
        const title = parseInt(value.title, 10);
        archive_title.push(title);
    }

    const fd = new FormData();
    fd.append("todo_title", JSON.stringify(todo_title));
    fd.append("archive_title", JSON.stringify(archive_title));
    parseInt(fd);
    xhr.send(fd);

});

/**
 * 削除時の処理
 */
const alertDelete = document.querySelector(".alert");
const itemDelete = document.querySelectorAll(".delete_item");

// 一括削除の処理
alertDelete.addEventListener("click", () => {
    if (window.confirm("すべて削除されますがよろしいですか？")) {
        window.location.href = "../models/db/delete.php?id=all"
    } else {
        // なにもしない
    }
})

// 個別削除の処理
for (const value of itemDelete) {
    value.addEventListener("click", () => {
        if (window.confirm("1件削除してもよろしいですか？")) {
            window.location.href = "../models/db/item_delete.php?getId=" + value.title;
        } else {
            // 何もしない
        }
    });
}