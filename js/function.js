////////////////////////////////////////////////
//  URL
////////////////////////////////////////////////
let adminUrl = document.querySelector('script[data-adminurl]') ? document.querySelector('script[data-adminurl]').dataset.adminurl:'';
let asyncUrl = document.querySelector('script[data-asyncurl]') ? document.querySelector('script[data-asyncurl]').dataset.asyncurl:'';
let dataUrl = document.querySelector('script[data-url]') ? document.querySelector('script[data-url]').dataset.url:'';
let dataImagesUrl = document.querySelector('script[data-imagesurl]') ? document.querySelector('script[data-imagesurl]').dataset.imagesurl:'';
////////////////////////////////////////////////
//  sweetalert
////////////////////////////////////////////////
const swalOption = (icon, text) => {
    return {
        icon: icon,
        text: text,
        allowOutsideClick: false
    }
}
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
//  Convert full-width numbers to half-width numbers
////////////////////////////////////////////////
const iNumbers = () => {
    const repFullToHalfNum = s => {
        for (let i = 0; i < 10; i++) {
            s = s.replace(new RegExp(new Array('０', '１', '２', '３', '４', '５', '６', '７', '８', '９')[i], 'g'), i);
        }
        return s;
    }
    [].slice.call(document.getElementsByClassName('i_numbers')).forEach(v => {
        v.onblur = e => {
            let n = repFullToHalfNum(e.target.value).replace(/[^0-9\.-]+/g, '');
            e.target.value = n.match(/\d+/) ? n : '';
        }
    });
}
////////////////////////////////////////////////
//  return HTML escape character
////////////////////////////////////////////////
const unescapeHTML = (str) => {
    return str.replace(/&lt;/g, "<")
        .replace(/&gt;/g, ">")
        .replace(/&nbsp;/g, " ")
        .replace(/&quot;/g, '"')
        .replace(/&apos;/g, "'")
        .replace(/&#13;/g, "\r")
        .replace(/&#10;/g, "\n");
}
////////////////////////////////////////////////
//  Convert full-width alphanumeric characters to half-width alphanumeric characters
////////////////////////////////////////////////
const repFullToHalfAlphNumeric = value => {
    return value.replace(/[Ａ-Ｚａ-ｚ０-９]/g, s => {
        return String.fromCharCode(s.charCodeAt(0) - 0xFEE0);
    });
}
////////////////////////////////////////////////
//  remove slash from string
////////////////////////////////////////////////
const unslash = () => {
    [].slice.call(document.getElementsByClassName('unslash')).forEach( v => {
        v.onblur = e => {
            let rep = e.target.value.replace(/\//g, '');
            if(rep == 'api'){
                rep = '';
            }else if(rep == adminUrl.replace(/\//g, '')){
                rep = '';
            }
            e.target.value = rep;
        }
    });
}
////////////////////////////////////////////////
//  flatpickr　setting
////////////////////////////////////////////////
const setFlatpicker = (default_date = null, lang = 'en') => {
    let _flatpickr = [].slice.call(document.getElementsByClassName('flatpickr'));
    _flatpickr.forEach((v, i) => {
        let flp_config = {
            disableMobile: true,
            locale: lang
        };
        flp_config['allowInput'] = v.dataset.allowinput ? true : false;
        flp_config['enableTime'] = v.dataset.time ? true : false;
        flp_config['mode'] = v.dataset.range ? 'range' : 'single';
        flp_config['defaultDate'] = v.value.length > 0 ? v.value : default_date ? default_date : null;
        let f = v.flatpickr(flp_config);
        let c = document.getElementsByClassName('flatpickr_clear_value')[i];
        if (c) {
            c.onclick = () => {
                f.clear();
            }
        }
    });
}
////////////////////////////////////////////////
//  min-height
////////////////////////////////////////////////
const bodyHeight = () => {
    let h = (window.innerHeight);
    let min_height = document.getElementsByClassName('min_height')[0];
    if (min_height) {
        min_height.style.minHeight = `${(h)}px`;
    }
}
////////////////////////////////////////////////
//  sp nav click
////////////////////////////////////////////////
const spNav = () => {
    let btn = document.querySelector('.btn-trigger');
    let navbar = document.querySelector('.navbar-nav');
    if(btn){
        btn.onclick = e => {
            if(e.target.classList.contains('active')){
                e.target.classList.remove('active');
                navbar.style.display = 'none';
            }else{
                e.target.classList.add('active');
                navbar.style.display = 'block';
            }
        }
    }
}
////////////////////////////////////////////////
//  logout event
////////////////////////////////////////////////
const logout = () => {
    [].slice.call(document.getElementsByClassName('logout_btn')).forEach(v => {
        v.addEventListener('click', e => {
            e.preventDefault();
            e.stopPropagation();
            let fdo = new FormData();
            fdo.append("token", "logout");
            asyncPost(`${asyncUrl}logout.php`, fdo, null, json => {
                if (json.result > 0) {
                    location.href = dataUrl + adminUrl;
                    return;
                }
                Swal.fire(swalOption('error', json.msg));
            });
        });
    });
}
////////////////////////////////////////////////
//  json parse
////////////////////////////////////////////////
const jsonParse = object => {
    return JSON.parse(JSON.stringify(object));
}
////////////////////////////////////////////////
//  getData
////////////////////////////////////////////////
const getData = () => {
    [].slice.call(document.getElementsByClassName('get_data')).forEach(get_data => {
        let xhr = XMLHttpRequestCreate();
        xhr.open('POST', `${asyncUrl}get_data.php`);
        let fdo = new FormData();
        fdo.append("method", get_data.dataset.method);
        if (get_data.dataset.option) {
            fdo.append("option", get_data.dataset.option);
        }
        xhr.send(fdo);
        xhr.onload = () => {
            if(xhr.status == 200){
                new Promise(resolve => {
                    get_data.innerHTML = xhr.responseText;
                    resolve();
                }).then(() => {
                    memoModal();
                });
            }
        };
        xhr.onerror = error => {
            console.log(error);
        };
    });
}
////////////////////////////////////////////////
//  sort record
////////////////////////////////////////////////
const sortRecord = () => {
    let result_list = document.getElementById('list_area');
    let sort_target = [].slice.call(document.getElementsByClassName('sort_target'));
    if (result_list && sort_target.length > 0) {
        Sortable.create(result_list, {
            handle: '.fa-arrows-alt-v',
            animation: 110,
            group: "save",
            store: {
                set: function(sortable) {
                    let fdo = new FormData();
                    fdo.append("method", "sort");
                    fdo.append("id", sortable.toArray());
                    fdo.append("table", result_list.dataset.table);
                    asyncPost(`${asyncUrl}sort_record.php`, fdo, null, json => {
                        if (json.result > 0) {
                            new Promise(resolve => {
                                resolve();
                            }).then(() => {
                                [].slice.call(document.getElementsByClassName('sort_num')).forEach((elm, i) => {
                                    elm.textContent = i + 1;
                                });
                            });
                            return;
                        }
                        Swal.fire(swalOption('error', 'Reload Rrowser'));
                    });
                }
            }
        });
    }
}
////////////////////////////////////////////////
//  table analysis
////////////////////////////////////////////////
const tableAnalysis = () => {
    [].slice.call(document.querySelectorAll('table.analysis')).forEach( e => {
        let data = [];
        let max = 0;
        [].slice.call(e.getElementsByClassName('data')).forEach( (d, i) => {
            data.push(d.textContent);
        });
        max = parseInt(Math.max(...data));
        [].slice.call(e.getElementsByClassName('data')).forEach( (d, i) => {
            if(parseInt(d.textContent) == max){
                d.innerHTML = '<span class="text-danger">'+ d.textContent +'</span>';
            }
        });
    })
}
////////////////////////////////////////////////
//  async post
////////////////////////////////////////////////
const asyncPost = (url, form_obj, reload = null, callback = null) => {
    let result = {
        "result": 0,
        "msg": "default error",
        "class": "false"
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
}
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
}
////////////////////////////////////////////////
//  current page
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
}
////////////////////////////////////////////////
//  easing
////////////////////////////////////////////////
const easingEaseOutCubic = (current_time, start_value, change_value, duration) => {
    current_time /= duration;
    current_time--;
    return change_value * (current_time * current_time * current_time + 1) + start_value;
}
////////////////////////////////////////////////
//  animation
////////////////////////////////////////////////
const animate = (elm, property, strat, end, time, unit = '') => {
    let frame_rate = 0.03; // 60 FPS 0.06 30FPS 0.03
    let begin = new Date() - 0;
    let from = strat;
    let distance = end == 0 ? -strat : end;
    let duration = time;
    let id = setInterval(() => {
        let time = new Date() - begin;
        let current = easingEaseOutCubic(time, from, distance, duration);
        if (time > duration) {
            clearInterval(id);
            current = from + distance;
        }
        elm.style[property] = current + unit;
    }, 1 / frame_rate);
}
////////////////////////////////////////////////
//  Get height of hidden element
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
//  scroll event
////////////////////////////////////////////////
const scrollFunc = () => {
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
//  Input form enter key invalidation
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
//  cookie
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
//  withdrawal modal
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
//  ad banner
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
//  In-page link
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
//  contact form event
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
}