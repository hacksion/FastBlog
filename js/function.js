let asyncUrl = document.querySelector('script[data-asyncurl]').dataset.asyncurl;
let dataUrl = document.querySelector('script[data-url]').dataset.url;
let dataImagesUrl = document.querySelector('script[data-imagesurl]').dataset.imagesurl;
////////////////////////////////////////////////
//  sweetalert2 default
////////////////////////////////////////////////
const swalOption = (icon, text) => {
    return {
        icon: icon,
        text: text,
        allowOutsideClick: false
    }
}
////////////////////////////////////////////////
//  getBrowser
////////////////////////////////////////////////
const getBrowser = () => {
    let result = 'unknow';
    let ua = window.navigator.userAgent.toLowerCase();
    if (ua.indexOf('msie') != -1 ||
        ua.indexOf('trident') != -1) {
        result = 'ie';
    } else if (ua.indexOf('edge') != -1) {
        result = 'edge';
    } else if (ua.indexOf('chrome') != -1) {
        result = 'chrome';
    } else if (ua.indexOf('safari') != -1) {
        result = 'safari';
    } else if (ua.indexOf('firefox') != -1) {
        result = 'firefox';
    } else if (ua.indexOf('opera') != -1) {
        result = 'opera';
    }
    return result;
};
////////////////////////////////////////////////
//  getDevice
////////////////////////////////////////////////
const getDevice = () => {
    let ua = navigator.userAgent;
    if (ua.indexOf('iPhone') > 0 || ua.indexOf('iPod') > 0 || ua.indexOf('Android') > 0 && ua.indexOf('Mobile') > 0) {
        return 'sp';
    }
    if (ua.indexOf('iPad') > 0 || ua.indexOf('Android') > 0) {
        return 'tab';
    }
    return 'other';
};
////////////////////////////////////////////////
//  XMLHttpRequest
////////////////////////////////////////////////
const XMLHttpRequestCreate = () => {
    try {
        return new XMLHttpRequest();
    } catch (e) {}
    try {
        return new ActiveXObject('MSXML2.XMLHTTP.6.0');
    } catch (e) {}
    try {
        return new ActiveXObject('MSXML2.XMLHTTP.3.0');
    } catch (e) {}
    try {
        return new ActiveXObject('MSXML2.XMLHTTP');
    } catch (e) {}
    return null;
}
////////////////////////////////////////////////
//  async post
////////////////////////////////////////////////
const asyncPost = (url, form_obj, reload = null, callback = null) => {
    let result = {
        "result": 0,
        "msg": "default error"
    };
    let xhr = XMLHttpRequestCreate();
    xhr.open("POST", url, true);
    xhr.send(form_obj);
    xhr.onload = () => {
        try{
            if(xhr.status == 200){

                result = JSON.parse(xhr.responseText.replace(/(\n)/g, "\\n"));
                if (reload == 1) {
                    location.reload();
                } else if (reload && reload != 1) {
                    location.href = reload;
                }
                if (callback)callback(result);
            }
        } catch (e){
            result.msg = e;
            if (callback)callback(result);
        }

    }
    xhr.onerror = error => {
        result.msg = error;
        if (callback)callback(result);
    }
};
////////////////////////////////////////////////
//  formData create *
////////////////////////////////////////////////
const createFormData = form_objs => {
    let form_data_obj;
    if (form_objs) {
        form_data_obj = new FormData();
        Object.keys(form_objs).forEach(i => {
            if (form_objs[i].name) {
                if (form_objs[i].type == 'radio' || form_objs[i].type == 'checkbox') {
                    if (form_objs[i].checked) {
                        form_data_obj.append(form_objs[i].name, form_objs[i].value);
                    }
                } else {
                    form_data_obj.append(form_objs[i].name, form_objs[i].value);
                }
            }
        });
    }
    return form_data_obj;
};
////////////////////////////////////////////////
//  現在のページ
////////////////////////////////////////////////
const navCurrentLink = () => {
    let id = document.querySelector('body[data-cat]').dataset.cat;
    if (id) {
        let nav_items = [].slice.call(document.querySelectorAll('.nav-item .dropdown-item'));
        let navs = [].slice.call(document.querySelectorAll('.nav-link'));
        nav_items.forEach(nav_item => {
            nav_item.classList.remove('active');
        });
        navs.forEach(nav => {
            if (id == (nav.dataset.id)) {
                nav.parentNode.classList.add('active');
            }
        });
    }
};
////////////////////////////////////////////////
//  イージング
////////////////////////////////////////////////
const easingEaseOutCubic = (current_time, start_value, change_value, duration) => {
    current_time /= duration;
    current_time--;
    return change_value * (current_time * current_time * current_time + 1) + start_value;
};
////////////////////////////////////////////////
//  アニメーション
////////////////////////////////////////////////
const animate = (elm, property, strat, end, time, unit = '') => {
    let frame_rate = 0.03; // 60 FPS 0.06 30FPS 0.03
    let begin = new Date() - 0;
    let from = strat; // 初期値
    let distance = end == 0 ? -strat : end; // 変動値
    let duration = time; // 継続時間
    let id = setInterval(() => {
        let time = new Date() - begin; // 経過時間
        let current = easingEaseOutCubic(time, from, distance, duration);
        if (time > duration) {
            clearInterval(id);
            current = from + distance;
        }
        elm.style[property] = current + unit;
    }, 1 / frame_rate);
}
////////////////////////////////////////////////
//  display=none 要素の高さ取得
////////////////////////////////////////////////
const getHideElmHeight = elm => {
    let copy_box = elm.cloneNode(true);
    elm.parentNode.appendChild(copy_box);
    copy_box.style.cssText = "display:block; height:auto; visibility:hidden; ";
    let cph = copy_box.offsetHeight;
    elm.parentNode.removeChild(copy_box);
    return cph;
}
////////////////////////////////////////////////
//  スクロール関数
////////////////////////////////////////////////
const scrollToFunc = (element, to, duration) => {
    if (duration <= 0) return;
    let difference = to - element.scrollTop;
    let per_tick = difference / duration * 10;
    setTimeout(() => {
        element.scrollTop = element.scrollTop + per_tick;
        if (element.scrollTop == to) return;
        scrollToFunc(element, to, duration - 10);
    }, 10);
}
////////////////////////////////////////////////
//  スクロールイベント　toTpoボタン
////////////////////////////////////////////////
const scrollFunc = () => {
    let to_top = document.getElementById('to_top');
    if (to_top) {
        to_top.style.position = 'fixed';
        to_top.style.right = '2rem';
        to_top.style.bottom = '2rem';
        to_top.style.opacity = 0;
        let scroll_elm = 'scrollingElement' in document ? document.scrollingElement : document.body;
        let to_top_show_flag = false;
        to_top.addEventListener('click', () => {
            scrollToFunc(scroll_elm, 0, 100);
        }, false);
        window.addEventListener('scroll', () => {
            let scroll_top = scroll_elm.scrollTop;
            if (scroll_top >= 50 && to_top_show_flag == false) {
                to_top_show_flag = true;
                animate(to_top, "opacity", 0, 1, 1000);
            }
            if (scroll_top < 50 && to_top_show_flag == true) {
                to_top_show_flag = false;
                animate(to_top, "opacity", 1, 0, 1000, '');
            }
        }, false);
    }

}
////////////////////////////////////////////////
//  navbar search
////////////////////////////////////////////////
const navSearchBtn = () => {
    let nav_search_btn = document.getElementById('nav_search_btn');
    let nav_search_area = document.getElementById('nav_search_area');
    nav_search_btn.onclick = e => {
        e.preventDefault();
        e.stopPropagation();
        nav_search_area.style.display = 'block';
        animate(nav_search_area, "opacity", 0, 1, 1000);
    }
    let search_close = document.getElementById('search_close');
    search_close.onclick = e => {
        e.preventDefault();
        e.stopPropagation();
        animate(nav_search_area, "opacity", 1, 0, 1000);
        nav_search_area.style.display = 'none';
        document.getElementById('search_result').innerHTML = '';
        document.getElementById('nav_search').value = '';
    }
    let navbar_toggler = document.querySelector('button.navbar-toggler');
    if (navbar_toggler) {
        navbar_toggler.onclick = () => {
            animate(nav_search_area, "opacity", 1, 0, 1000);
            nav_search_area.style.display = 'none';
            document.getElementById('search_result').innerHTML = '';
        }
    }

}
////////////////////////////////////////////////
//  navbar search result
////////////////////////////////////////////////
const navSearchBtnInput = () => {
    document.getElementById('nav_search').onkeyup = e => {
        if (e.target.value.length > 0) {
            let nav_search_btn = document.getElementsByClassName('nav_search_btn')[0];
            nav_search_btn.click();
        }
    }
}
////////////////////////////////////////////////
//  入力フォームエンター無効化
////////////////////////////////////////////////
const enterFalse = () => {
    document.onkeypress = e => {
        let ref = e.target;
        if (e.key == 'Enter' && (ref.type == 'text' || ref.type == 'radio' || ref.type == 'checkbox' || ref.type == 'password')) {
            return false;
        }
    }
}
////////////////////////////////////////////////
//  アクセスクッキー
////////////////////////////////////////////////
const getAccessCookie = () => {
    let page = location.pathname.split('/').filter(Boolean).slice(-1)[0];
    let blog = '_fastblog_';
    let result = false;
    let ck = document.cookie;
    let cka = ck.split(';');
    for(let c of cka){
        let ca = c.split('=');
        if( ca[0].trim() == blog + page){
            result = true;
        }
    }
    if(!result){
        let d = new Date();
        d.setTime(d.getTime() + 60*60*1000*24);
        document.cookie = blog + page + "=1; expires=" + d.toGMTString() + "; domain=" + document.domain;
        let xhr = new XMLHttpRequest();
        let fdo = new FormData();
        fdo.append("page", page);
        xhr.open("POST", asyncUrl + 'access.php', true);
        xhr.send(fdo);
    }
}
////////////////////////////////////////////////
//  離脱モダール
////////////////////////////////////////////////
const withdrawalModal = (modal_id, timer = 3600000) => {
    const getOnetimeCookie = timer => {
        let blog = '_fastblog_wd_';
        let result = true;
        let ck = document.cookie;
        let cka = ck.split(';');
        for(let c of cka){
            let ca = c.split('=');
            if( ca[0].trim() == blog){
                result = false;
            }
        }
        if(result){
            let d = new Date();
            d.setTime(d.getTime() + timer);
            document.cookie = blog + "=1; expires=" + d.toGMTString() + "; domain=" + document.domain + "; path=/";
        }
        return result;
    }
    let modal_elm = document.getElementById(modal_id);
    if(modal_elm){
        let myModal = new bootstrap.Modal(modal_elm);
        document.body.addEventListener('mousemove', e => {
            if(e.clientY < 5){
                if(getOnetimeCookie()){
                    myModal.show();
                }
            }
        });
    }

}
////////////////////////////////////////////////
//  banner
////////////////////////////////////////////////
const adBanner = () => {
    let admodal_elm = document.getElementById('admodal_elm');
    if (admodal_elm) {
        let timer = admodal_elm.dataset.timer;
        setTimeout(function(){
            admodal_elm.classList.remove('d-none');
        }, parseInt(timer) * 1000);
        let admodal_close = document.getElementById('admodal_close');
        admodal_close.onclick = () => {
            admodal_elm.classList.add('d-none');
        }
    }
}
////////////////////////////////////////////////
//  ページ内リンク
////////////////////////////////////////////////
const pageLink = () => {
    [].slice.call(document.getElementsByClassName('p_link')).forEach( v => {
        v.onclick = e => {
            e.preventDefault();
            e.stopPropagation();
            let rect = document.getElementById(e.target.dataset.id).getBoundingClientRect();
            let sec = rect.top + window.pageYOffset - 80;
            window.scrollTo({
                top: sec,
                behavior: "smooth"
            });
        }
    });
}
////////////////////////////////////////////////
//  コンタクトフォーム
////////////////////////////////////////////////
const contactForm = () => {
    let contact_form = document.getElementById('contact_form');
    let forms = document.forms.contact_form;
    if(contact_form && forms){

        contact_form.onclick = e => {
            let check_msg = document.getElementById('contact_form_check');
            check_msg = check_msg ? check_msg.textContent:'Confirmation';
            e.target.classList.add('d-none');
            e.preventDefault();
            e.stopPropagation();
            if (forms.checkValidity()) {
                Swal.fire({
                    title: check_msg,
                    showCancelButton: true,
                    confirmButtonText: e.target.textContent,
                    allowOutsideClick : false
                }).then( ret => {
                    if (ret.value) {
                        let fdo = new FormData(forms);
                        asyncPost( `${asyncUrl}contact_form.php`, fdo, null, json => {
                            if (json.result > 0) {
                                new Promise( resolve => {
                                    resolve();
                                }).then( () => {
                                    forms.reset();
                                    Swal.fire(swalOption('success', json.msg));
                                    return;
                                });
                            }
                            Swal.fire(swalOption('error', json.msg));
                        });
                    }else{
                        e.target.classList.remove('d-none');
                    }
                    forms.classList.remove('was-validated');
                    return;
                });
            }
            e.target.classList.remove('d-none');
            forms.classList.add('was-validated');
        }
    }
};
