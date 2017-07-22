var email = false;
var name = false;
var password = false;
function passcheck() {
    if((document.getElementById('pass').value === document.getElementById('passrepeat').value) && (document.getElementById('pass').value.length > 5) && (document.getElementById('passrepeat').value.length > 5)) {
        password = true;
        var passrepeat = document.getElementById("passrepeatdiv");
        if (passrepeat.classList.contains("error-border")) {
            passrepeat.classList.remove("error-border")
        }
        var pass = document.getElementById("passdiv");
        if (pass.classList.contains("error-border")) {
            pass.classList.remove("error-border")
        }
    } else {
        var passrepeat = document.getElementById("passrepeatdiv");
        if (!passrepeat.classList.contains("error-border")) {
            passrepeat.classList.add("error-border")
        }

        var pass = document.getElementById("passdiv");
        if (!pass.classList.contains("error-border")) {
            pass.classList.add("error-border")
        }
        password = false;
    }
    buttonendis();
}
function namecheck() {
    if(document.getElementById('name').value.length > 1) {
        name = true;
        var named = document.getElementById("namediv");
        if (named.classList.contains("error-border")) {
            named.classList.remove("error-border")
        }
    } else {
        name = false;
        var named = document.getElementById("namediv");
        if (!named.classList.contains("error-border")) {
            named.classList.add("error-border")
        }
    }
    buttonendis();
}
function emailcheck() {
    var emailvalue = document.getElementById('email').value;
    if(document.getElementById('email').value.length > 5 && emailvalue.includes('@')) {
        email = true;
        var emaild = document.getElementById("emaildiv");
        if (emaild.classList.contains("error-border")) {
            emaild.classList.remove("error-border")
        }
    } else {
        email = false;
        var emaild = document.getElementById("emaildiv");
        if (!emaild.classList.contains("error-border")) {
            emaild.classList.add("error-border")
        }
    }
    buttonendis();
}
function buttonendis() {
    if(password && name && email) {
        document.getElementById("registerbutton").disabled = false;
    } else {
        document.getElementById("registerbutton").disabled = true;
    }
}