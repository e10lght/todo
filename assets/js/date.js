const today = new Date();
today.setDate(today.getDate());
const yyyy = today.getFullYear();
const mm = ("0"+(today.getMonth()+1)).slice(-2);
const dd = ("0"+today.getDate()).slice(-2);
const date = document.querySelectorAll(".date");
console.log(date);
for(value of date) {
    value.value = yyyy+'-'+mm+'-'+dd;
}