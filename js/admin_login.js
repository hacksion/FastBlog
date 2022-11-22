document.addEventListener( "DOMContentLoaded", ()=> {
    let timerId = setInterval(() => {
        let xhr = XMLHttpRequestCreate();
        xhr.open('GET', `${asyncUrl}session.php`, true);
        xhr.send();
        xhr.onload = () => {
            if (xhr.status == 200) {
                if (xhr.responseText == 0) {
                    [].slice.call(document.getElementsByClassName('login_reload')).forEach(e => {
                        e.classList.remove('d-none');
                    });
                    clearInterval(timerId);
                }
            }
        }
    }, 60000);
    let eye_toggle = document.querySelector('.eye_toggle');
    let input_passwd = document.querySelector('input[name=passwd]');
    if(eye_toggle && input_passwd){
        eye_toggle.addEventListener('click', e => {
            let elm = e.target;
            if(elm.classList.contains('fa-eye-slash')){
                elm.classList.remove('fa-eye-slash');
                elm.classList.add('fa-eye');
                input_passwd.setAttribute('type', 'text');
            }else{
                elm.classList.add('fa-eye-slash');
                elm.classList.remove('fa-eye');
                input_passwd.setAttribute('type', 'password');
            }
        });
    }

    let btn_submit = document.getElementById('btn_submit');
    let form = document.querySelector('form[name=login]');
    if(btn_submit && form){
        btn_submit.addEventListener('click', e => {
            e.preventDefault();
            e.stopPropagation();
            if (form.checkValidity()) {
                asyncPost( `${asyncUrl}login_auth.php`, new FormData(form), null, json => {
                    if (json.result == 1) {
                        location.href = json.request_url;
                        return;
                    }
                    Swal.fire(swalOption('error', json.msg)).then( ret => {
                        if (ret.value) window.location.reload();
                    });
                });
                return;
            }
            form.classList.add('was-validated');
        });
    }
});
