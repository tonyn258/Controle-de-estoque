var left = true;

function validateForm(){
    var btn = document.getElementById("btn");
    var name = document.forms["Form"]["name"].value;
    var email = document.forms["Form"]["email"].value;
    if (name == null || name == "", email == null || email == ""){
        btn.classList.add("error");
        changePosition(btn);
    } else {
        btn.classList.add("success");
    }
}

function changePosition(btn) {
    if (left){
        btn.style.marginLeft = '150px';
        left = false;
    } else {
        btn.style.marginLeft = '0px';
        left = true;
    }
}