
function escape(toOutput) {
    return toOutput.replace(/\&/g, '&amp;')
        .replace(/\</g, '&lt;')
        .replace(/\>/g, '&gt;')
        .replace(/\"/g, '&quot;')
        .replace(/\'/g, '&#x27')
        .replace(/\//g, '&#x2F');
}

function flagSubmit() {
    const inputBlock = document.querySelectorAll('.login_input_block');
    var hasCheck = true;
    for (const inputCheck of inputBlock) {
        const errMsg = inputCheck.querySelector('p');
        const inputValue = inputCheck.querySelector('input');
        errMsg.className = 'input-ok';
        if (!inputValue.value) {
            errMsg.className = 'input-err';
            hasCheck = false
        }
    }
    if (!hasCheck) {
        return false;
    }
    return true;
}

$(document).ready(() => {
    $('.login_form').submit(e => {
        e.preventDefault();
        const newLoginData = {
            username: $('input[name=username]').val(),
            password: $('input[name=password]').val()
        }

        if (!flagSubmit()) {
            return
        }

        $.ajax({
            type: 'POST',
            url: 'https://tzutzu858.tw/json/api_login.php',
            data: newLoginData
        }).done(function (data) {
            if (!data.ok) {
                alert(data.message)
                return
            }

            sessionStorage.setItem('username', data.username);
            window.location = 'index.html';

            $('input[name=username]').val('') // 成功之後傳個空字串清空
            $('input[name=password]').val('')
        });

    })
})


