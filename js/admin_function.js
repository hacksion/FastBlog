////////////////////////////////////////////////
//  URL
////////////////////////////////////////////////
let adminUrl = document.querySelector('script[data-adminurl]').dataset.adminurl;
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
//  全角数字を半角に変換
////////////////////////////////////////////////
const repFullToHalfNum = s => {
    for (let i = 0; i < 10; i++) {
        s = s.replace(new RegExp(new Array('０', '１', '２', '３', '４', '５', '６', '７', '８', '９')[i], 'g'), i);
    }
    return s;
}
////////////////////////////////////////////////
//  HTMLエスケープ文字を戻す
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
//  全角英数字を半角英数字
////////////////////////////////////////////////
const repFullToHalfAlphNumeric = value => {
    return value.replace(/[Ａ-Ｚａ-ｚ０-９]/g, s => {
        return String.fromCharCode(s.charCodeAt(0) - 0xFEE0);
    });
}
////////////////////////////////////////////////
//  文字列よりスラッシュ削除
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
//  半角数字変換
////////////////////////////////////////////////
const iNumbers = () => {
    [].slice.call(document.getElementsByClassName('i_numbers')).forEach(v => {
        v.onblur = e => {
            let n = repFullToHalfNum(e.target.value).replace(/[^0-9\.-]+/g, '');
            e.target.value = n.match(/\d+/) ? n : '';
        }
    });
};
////////////////////////////////////////////////
//  日本語判定
////////////////////////////////////////////////
const isJp = c => { // c:判別したい文字 E38080
    let unicode = c.charCodeAt(0);
    if (
        (unicode >= 0x4e00 && unicode <= 0x9fcf) || // CJK統合漢字
        (unicode >= 0x3400 && unicode <= 0x4dbf) || // CJK統合漢字拡張A
        (unicode >= 0x20000 && unicode <= 0x2a6df) || // CJK統合漢字拡張B
        (unicode >= 0xf900 && unicode <= 0xfadf) || // CJK互換漢字
        (unicode >= 0x2f800 && unicode <= 0x2fa1f) ||
        (unicode >= 0x3190 && unicode <= 0x319f) ||
        (unicode >= 0x3000 && unicode <= 0x309f) ||
        (unicode >= 0x30a0 && unicode <= 0x30ff) ||
        (unicode >= 0xff61 && unicode <= 0xff9f)
    ) { // CJK互換漢字補助
        return true;
    }
    return false;
}
////////////////////////////////////////////////
//  日本語削除
////////////////////////////////////////////////
const jpDelete = () => {
    [].slice.call(document.getElementsByClassName('jp_delete')).forEach(v => {
        v.onblur = e => {
            let text = e.target.value;
            if (text) {
                let textArray = text.split('');
                let resultText = ''
                for (let t = 0; t < textArray.length; t++) {
                    if (!isJp(textArray[t])) {
                        resultText += textArray[t];
                    }
                }
                e.target.value = repFullToHalfAlphNumeric(resultText);
            }
        }
    });
}
////////////////////////////////////////////////
//  数字を３桁区切りにする
////////////////////////////////////////////////
const sep3 = (number) => {
    return Number(number).toLocaleString('ja-JP');
}
////////////////////////////////////////////////
//  //デフォルトの日付　flatpickr_scheduleオプション
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
        console.log(xhr.responseText);
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
    xhr.onprogress = event => {
        // event.loaded - ダウンロードされたバイト
        // event.lengthComputable = サーバが Content-Length ヘッダを送信した場合は true
        // event.total - トータルのバイト数(lengthComputable が true の場合)
        //if(event.lengthComputable)console.log(`${event.loaded}byte`, `${event.total}byte`);
    }
    xhr.onerror = error => {
        result.msg = error;
        if (callback)callback(result);
    }
};
////////////////////////////////////////////////
//  document bodyの位置取得
////////////////////////////////////////////////
const documentElementFunc = () => {
    return 'scrollingElement' in document ? document.scrollingElement : document.body;
}
////////////////////////////////////////////////
//  指定エレメントの位置　top
////////////////////////////////////////////////
const getElementTop = elm => {
    let rect = elm.getBoundingClientRect();
    return (rect.top + window.pageYOffset);
}
////////////////////////////////////////////////
//  textarea auto height
////////////////////////////////////////////////
const textAreaAutoHeight = () => {
    let textareas = [].slice.call(document.querySelectorAll('textarea'));
    textareas.forEach(textarea => {
        textarea.style.height = `${textarea.scrollHeight}px`;
        textarea.addEventListener('keyup', e => {
            e.target.style.height = `${e.target.scrollHeight}px`;
        });
    });
}
////////////////////////////////////////////////
//  文字列ランダム発行
////////////////////////////////////////////////
const getRndStr = (len = 8) => {
    let str = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!#$%&=~/*-+";
    let result = "";
    len.forEach(i => {
        result += str.charAt(Math.floor(Math.random() * str.length));
    });
    return result;
}
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
        to_top.style.right = 0;
        to_top.style.bottom = 0;
        to_top.style.opacity = 0;
        to_top.style.zIndex = 999;
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
//  現在のページ
////////////////////////////////////////////////
const navCurrentLink = () => {
    let id = document.body.id;
    [].slice.call(document.getElementsByClassName('nav-link')).forEach(v => {
        if (id == v.dataset.body) {
            v.classList.add('active');
        }
    });
};
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
//  min-height
////////////////////////////////////////////////
const bodyHeight = () => {
    let h = (window.innerHeight);
    let min_height = document.getElementsByClassName('min_height')[0];
    if (min_height) {
        min_height.style.minHeight = `${(h)}px`;
    }
};
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
//  logout
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
};
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
//  結果データを反映する
////////////////////////////////////////////////
const setRepNumber = () => {
    [].slice.call(document.getElementsByClassName('rep_number')).forEach(rep => {
        let id = rep.dataset.id;
        if (id) {
            rep.innerHTML = document.getElementById(id).innerHTML;
        }
    });
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
};

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


const categoryListNum = () => {
    const changeNum = (orders, table) => {
        let fdo = new FormData();
        fdo.append("id", orders);
        fdo.append("table", table);
        fdo.append("category", category);
        asyncPost(`${asyncUrl}sort_record.php`, fdo, null, json => {
            //console.log(json.result);
        });
    };
    [].slice.call(document.getElementsByClassName('list-group-category')).forEach(elm => {
        Sortable.create(elm, {
            handle:'.sort-target-category',
            animation: 110,
            group: "save",
            store: {
                set: function (sortable) {
                    changeNum(sortable.toArray(), 'category');
                }
            },
            onEnd: function (sortable) {
                
            },
        });
    });
    [].slice.call(document.getElementsByClassName('list-group-menu')).forEach(elm => {
        Sortable.create(document.querySelector('[data-group=' + elm.dataset.group + ']'), {
            handle:'.sort-target-menu',
            animation: 110,
            group: elm.dataset.group,
            store: {
                set: function (sortable) {
                    changeNum(sortable.toArray(), 'menu');
                    //console.log(sortable.toArray());
                }
            },
            onEnd: function (sortable) {
                
            },
        });
        
    });
};
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
