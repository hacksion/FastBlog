////////////////////////////////////////////////
//  エディット モーダル表示
////////////////////////////////////////////////
const editModal = (file_size=0) => {
    let edit_modal = document.getElementById('edit_modal');
    if(edit_modal){
        edit_modal.addEventListener('show.bs.modal', function (event) {
            let button = event.relatedTarget;
            let id = button.getAttribute('data-id');
            [].slice.call(document.getElementsByClassName('new_create_modal')).forEach( v => {
                v.classList.add('d-none');
            });
            let table = button.getAttribute('data-table');
            if(id){
                [].slice.call(document.getElementsByClassName('new_create_modal')).forEach( v => {
                    v.classList.remove('d-none');
                });
                let fdo = new FormData();
                fdo.append("id", id);
                fdo.append("table", table);
                asyncPost( `${asyncUrl}get_record.php`, fdo, null, json => {
                    if (json.result > 0) {
                        if(table == 'account'){
                            if(id == 1){
                                json.delete_record = 0;
                            }
                            json.col = 'icon';
                        }
                        setDetailRecord(json);
                    }
                });
            }
            if(table == 'account'){
                let passwd = document.querySelector('input[name=passwd]');
                if(id){
                    passwd.removeAttribute('required');
                    passwd.setAttribute('placeholder', document.getElementById('chg_passwd_text').textContent);
                }else{
                    passwd.setAttribute('required', true);
                    passwd.setAttribute('placeholder', '');
                }
            }

            editCreate();
            selectFile(file_size);
            unslash();
        });
    }
}
////////////////////////////////////////////////
//  edit modal for json
////////////////////////////////////////////////
const setDetailRecord = json => {
    resetArea();
    let del_btn = document.getElementById('delete_edit_record');
    del_btn.classList.add('d-none');
    Object.keys(json).forEach( key => {
        if(key != 'result' && key != 'msg' && key != 'delete_record'){
            switch (json[key].type){
                case 'radio':
                setDetailRadiobox(key, json);
                break;
                case 'checkbox':
                setDetailCheckbox(key, json);
                break;
                case 'id':
                setDetailIdValueSet(key, json);
                break;
                case 'input_array':
                setDetailInputArray(key, json);
                break;
                case 'add_array':
                setDetailAddArray(key, json);
                break;
                case 'base64':
                setDetailBase64Set(key, json);
                break;
                default:
                setDetailNameValueSet(key, json);
            }
        }
        if(key == 'icon' && json['icon'].value != null){
            let icon_area = document.getElementById('icon_area');
            setDetailImage('icon', json, icon_area);
        }
    });
    //削除が可能な場合はボタンの表示がされる
    if(json.delete_record == 1){
        if(del_btn){
            del_btn.setAttribute('data-id', json.id.value);
            del_btn.setAttribute('data-table', json.table_name);
            del_btn.classList.remove('d-none');
        }
        deleteEditRecord();
    } else {
        document.getElementById('delete_not_record').classList.remove('d-none');
    }
}
////////////////////////////////////////////////
//  エディット 登録・更新
////////////////////////////////////////////////
const editCreate = () => {
    let edit_create = document.getElementById('edit_create');
    let forms = document.forms.edit_create;
    if(edit_create && forms){
        edit_create.onclick = e => {
            e.preventDefault();
            e.stopPropagation();
            if (forms.checkValidity()) {
                let id = document.querySelector('input[name=id]').value;
                let table = document.querySelector('input[name=table]').value;
                edit_create.classList.add('d-none');
                let fdo = new FormData(forms);
                fdo.append("table", table);
                fdo.append("html_lang", document.documentElement.lang);
                if (typeof CKEDITOR !== 'undefined') {

                    let html_col = ['html', 'footer_text'];
                    html_col.forEach(elm => {
                        if (document.querySelector('[name = "' + elm + '"]')) {
                            fdo.append(elm, CKEDITOR.instances[elm].getData());
                        }
                    });
                    
                }
                asyncPost( `${asyncUrl}edit.php`, fdo, null, json => {
                    edit_create.classList.remove('d-none');
                    if (json.result > 0) {
                        new Promise( resolve => {
                            resolve();
                        }).then( () => {
                            if(json.result == 1){
                                Swal.fire(swalOption('success', json.msg)).then( ret => {
                                    if (ret.value){
                                        if(json.edit == 'add'){
                                            window.location.href = dataUrl + adminUrl + json.dir;
                                            return;
                                        }
                                        window.location.reload();
                                    }
                                });
                                return;
                            }
                            Swal.fire(swalOption('warning', json.msg));
                        });
                        return;
                    }
                    Swal.fire(swalOption('error', 'Reload Rrowser'));
                });
                return;
            }
            forms.classList.add('was-validated');
        }
    }
};
////////////////////////////////////////////////
//  エディット FILE 更新
////////////////////////////////////////////////
const editFile = () => {
    console.log('edit file');
    let edit_file = document.getElementById('edit_file');
    let forms = document.forms.edit_file;
    if(edit_file && forms){
        edit_file.onclick = e => {
            e.preventDefault();
            e.stopPropagation();
            let table = document.querySelector('input[name=table]').value;
            let type = document.querySelector('input[name=type]').value;
            edit_file.classList.add('d-none');
            let fdo = new FormData(forms);
            fdo.append("type", type);
            fdo.append("table", table);
            asyncPost( `${asyncUrl}edit_file.php`, fdo, null, json => {
                edit_file.classList.remove('d-none');
                if (json.result > 0) {
                    new Promise( resolve => {
                        resolve();
                    }).then( () => {
                        if(json.result == 1){
                            Swal.fire(swalOption('success', json.msg));
                        }else{
                            Swal.fire(swalOption('warning', json.msg));
                        }
                    });
                    return;
                }
                Swal.fire(swalOption('error', 'Reload Rrowser'));
            });
        }
    }
};
////////////////////////////////////////////////
// レコード削除する
////////////////////////////////////////////////
const deleteEditRecord = () => {
    let delete_edit_record = document.getElementById('delete_edit_record');
    if(delete_edit_record){
        delete_edit_record.onclick = e => {
            let id = e.target.dataset.id;
            let table = e.target.dataset.table;
            console.log(id, table);
            Swal.fire({
                title: '削除してもいいですか?',
                showCancelButton: true,
                confirmButtonText: 'Yes',
                allowOutsideClick : false
            }).then( ret => {
                if (ret.value) {
                    let fdo = new FormData();
                    fdo.append("method", "delete");
                    fdo.append("id", id);
                    fdo.append("table", table);
                    asyncPost( `${asyncUrl}delete.php`, fdo, null, json => {
                        if (json.result == 'error') {
                            Swal.fire(swalOption('warning', json.msg));
                            return;
                        }
                        if(parseInt(json.result) > 0){
                            Swal.fire(swalOption('success', json.msg)).then( ret => {
                                if (ret.value) {
                                    window.location.href = dataUrl + adminUrl + json.dir;
                                }
                            });
                            return;
                        }
                        Swal.fire(swalOption('error', 'Reload Rrowser'));
                    });
                }
            });
        }
    }
};

////////////////////////////////////////////////
//  エディットモーダルをリセットするエリア
////////////////////////////////////////////////
const resetArea = () => {

    document.forms.edit_create.reset();
    document.forms.edit_create.classList.remove('was-validated');
    let delete_edit_record = document.getElementById('delete_edit_record');
    if(delete_edit_record){
        delete_edit_record.classList.add('d-none');
        delete_edit_record.setAttribute('data-id', '');
    }
    let edit_record_id = document.getElementById('edit_record_id');
    if(edit_record_id){
        edit_record_id.value = '';
    }
    let icon_files = document.getElementById('icon_files');
    if(icon_files){
        icon_files.innerHTML = '';
    }
    let icon_area = document.getElementById('icon_area');
    if(icon_area){
        icon_area.innerHTML = '';
    }
    let icon = document.getElementById('icon');
    if(icon){
        icon.value = '';
    }

    let logo_files = document.getElementById('logo_files');
    if(logo_files){
        logo_files.innerHTML = '';
    }
    let logo_area = document.getElementById('logo_area');
    if(logo_area){
        logo_area.innerHTML = '';
    }
    let logo = document.getElementById('logo');
    if(logo){
        logo.value = '';
    }


    dispControl(0);
}

////////////////////////////////////////////////
//  radio checked for json
////////////////////////////////////////////////
const setDetailRadiobox = (key, json) => {
    [].slice.call(document.querySelectorAll(`input[name=${key}]`)).forEach( v => {
        v.removeAttribute('checked');
        v.checked = v.value == json[key].value ? true:false;
    });
}

////////////////////////////////////////////////
//  checkbox checked for json
////////////////////////////////////////////////
const setDetailCheckbox = (key, json) => {
    let vals = json[key].value != null ? json[key].value.split(','):[];
    [].slice.call(document.getElementsByClassName(key)).forEach( v => {
        vals.forEach( val => {
            if(v.value == val){
                v.checked = true;
            }
        });
    });
}

////////////////////////////////////////////////
//  input array for json
////////////////////////////////////////////////
const setDetailInputArray = (key, json) => {
    if(json[key].value){
        let vals = json[key].value.split(',');
        [].slice.call(document.querySelectorAll(`input[data-array=${key}]`)).forEach( (v,i) => {
            v.value = vals[i];
        });
    }
}

////////////////////////////////////////////////
//  element add array for json
////////////////////////////////////////////////
const setDetailAddArray = (key, json) => {
    let elm_t = json[key]['elm_t'].split(',');
    let elm_g = json[key]['elm_g'].split(',');
    let elm_val = {};
    let add = 0;
    for(let i = 0; i < elm_g.length; i++){
        elm_val[elm_g[i]] = json[key][`${elm_g[i]}_value`].split(',');
        if(elm_val[elm_g[i]][0].length > 0){
            add++;
        }
    }
    let add_area_id,tmp_add,set_elm,set_op;
    let area_num = 0;
    let val_num = 1;
    if(add != elm_g.length){
        area_num = 1;
        val_num = 0;
    }
    if(elm_val[elm_g[0]].length > 1){
        for(let i = 0; i < elm_g.length; i++){
            tmp_add = '';
            add_area_id = document.getElementById(`${elm_g[i]}_add_area`);
            for(let v = 0; v < (elm_val[elm_g[i]].length - area_num); v++){
                tmp_add += document.getElementById(`${elm_g[i]}_tmp`).innerHTML
            }
            add_area_id.innerHTML = tmp_add;
        }
    }
    for(let i = 0; i < elm_g.length; i++){
        set_elm = document.getElementsByClassName(elm_g[i]);
        if(set_elm.length > 0){
            for(let s = (set_elm.length - 2); s >= 0; s--){
                if(elm_t[i] == 'input'){
                    set_elm[s].value = elm_val[elm_g[i]][s - val_num];
                }
                if(elm_t[i] == 'select'){
                    set_op = set_elm[s].options;
                    Object.keys(set_op).forEach( o => {
                        if(set_op[o].value == elm_val[elm_g[i]][s - val_num]){
                            set_op[o].selected = true;
                            //set_op[o].removeAttribute('disabled');
                        }
                    });
                }
            }
        }
    }
}

////////////////////////////////////////////////
//  input value set for json
////////////////////////////////////////////////
const setDetailNameValueSet = (key, json) => {
    let elm = document.querySelector(`${json[key].type}[name=${key}]`);
    if(elm){
        elm.value = json[key].value;
        if(key == 'page' && json[key].value == 'index'){
            elm.readOnly = true;
        }
    }
}


////////////////////////////////////////////////
//  taget id set for json
////////////////////////////////////////////////
const setDetailIdValueSet = (key, json) => {
    let elm = document.querySelector(`#${key}`);
    if(elm){
        elm.innerHTML = json[key].value;
    }
}

////////////////////////////////////////////////
//  taget base64 set for json
////////////////////////////////////////////////
const setDetailBase64Set = (key, json) => {
    let elm = document.querySelector(`#${key}`);
    if(elm){
        elm.setAttribute('src', json[key].value);
    }
}

////////////////////////////////////////////////
//  modal new clear
////////////////////////////////////////////////
const newCreate = () => {
    [].slice.call(document.getElementsByClassName('new_create')).forEach( v => {
        v.onclick = e => {
            e.preventDefault();
            e.stopPropagation();
            resetArea();
        }
    });
    [].slice.call(document.getElementsByClassName('new_create_modal')).forEach( v => {
        v.onclick = e => {
            e.preventDefault();
            e.stopPropagation();
            resetArea();
            v.classList.add('d-none');
        }
    });
}
////////////////////////////////////////////////
//  edit fixed page
////////////////////////////////////////////////
const newCreateFixed = () => {
    [].slice.call(document.getElementsByClassName('new_create_fixed')).forEach( v => {
        v.onclick = e => {
            e.preventDefault();
            e.stopPropagation();
            location.href = dataUrl + adminUrl + e.target.dataset.adminurl;
        }
    });
}
////////////////////////////////////////////////
// SELECT 新規作成時だけには表示させない option 1 : data-disp=1だけを表示 0 : 解除
////////////////////////////////////////////////
const dispControl = (disp=1) => {
    let cls = 'd-none';
    [].slice.call(document.getElementsByClassName('disp_cnt')).forEach( v => {
        [].slice.call(v.options).forEach( op => {
            if(op.dataset.disp != undefined){
                (disp == 1 && op.dataset.disp == 0) ? op.classList.add(cls):op.classList.remove(cls);
            }
        });
    });
}

////////////////////////////////////////////////
//  file
////////////////////////////////////////////////
const selectFile = (size) => {
    //fileuplad
    [].slice.call(document.getElementsByClassName('fileuplad')).forEach( v => {
        v.onchange = e => {
            let area = document.getElementById(v.dataset.area);
            let size = v.dataset.size;
            let width = v.dataset.width;
            area.innerHTML = '';
            let files = e.target.files;
            for (let i = 0, f; f = files[i]; i++) {
                if(files[i].size > size){
                    Swal.fire(swalOption('error', 'File size is too large Please change to '+size+'byte'));
                    break;
                }else{
                    let fr = new FileReader;
                    fr.readAsDataURL(f);
                    fr.onload = ( theFile => {
                        return function (e) {
                            let img = document.createElement('img');
                            img.setAttribute('src', fr.result);
                            img.setAttribute('style', 'width:' + width);
                            area.appendChild(img);
                        }
                    })(f);
                }
            }
        };
    });
}
////////////////////////////////////////////////
//  image files set for json
////////////////////////////////////////////////
const setDetailImage = (key, json, area) => {
    //画像ファイルがあればimgタグ追加
    let names = json[key].value.split(',');
    names.forEach( val => {
        //col
        let card = document.createElement('div');
        card.setAttribute('class', 'card p-1');
        card.setAttribute('style', 'width: 100px');
        card.setAttribute('data-id', json['id'].value);
        card.setAttribute('data-filename', val);
        card.setAttribute('data-table', json['table_name']);
        //img
        let img = document.createElement('img');
        img.setAttribute('src', `${imagesurl}${json['table_name']}/${json['id'].value}/${val}`);
        img.setAttribute('class', 'w100');
        img.setAttribute('style', 'background-color:white');
        card.appendChild(img);
        //card-body
        let card_body = document.createElement('div');
        card_body.setAttribute('class', 'card-body p-1 text-right text-danger');
        //trash
        let trash = document.createElement('i');
        trash.setAttribute('class', 'fas fa-trash file_trash');
        trash.setAttribute('data-id', json['id'].value);
        trash.setAttribute('data-name', val);
        trash.setAttribute('data-table', json['table_name']);
        trash.setAttribute('data-col', json['col']);
        trash.setAttribute('data-type', 'images');
        trash.setAttribute('role', 'button');
        //エレメントセット
        card_body.appendChild(trash);
        card.appendChild(card_body);
        area.appendChild(card);
    });
    fileTrash();
}
////////////////////////////////////////////////
//  delete file
////////////////////////////////////////////////
const fileTrash = () => {
    [].slice.call(document.getElementsByClassName('file_trash')).forEach( v => {
        v.onclick = e => {
            let id = e.target.dataset.id;
            let file = e.target.dataset.name;
            let box = e.target.parentNode.parentNode;
            let table = e.target.dataset.table;
            let col = e.target.dataset.col;
            let type = e.target.dataset.type;
            Swal.fire({
                title: 'Delete ?',
                showCancelButton: true,
                confirmButtonText: 'Yes',
                allowOutsideClick : false
            }).then( ret => {
                if (ret.value) {
                    let fdo = new FormData();
                    fdo.append("method", "delete_file");
                    fdo.append("id", id);
                    fdo.append("name", file);
                    fdo.append("table", table);
                    fdo.append("col", col);
                    fdo.append("type", type);
                    asyncPost( `${asyncUrl}delete_file.php`, fdo, null, json => {
                        if (json.result == 'error') {
                            Swal.fire(swalOption('warning', json.msg));
                            return;
                        }
                        if(parseInt(json.result) > 0){
                            Swal.fire(swalOption('success', json.msg)).then( ret => {
                                if (ret.value) {
                                    box.remove();
                                    document.getElementById(json.column).value = json.files;
                                }
                            });
                            return;
                        }
                        Swal.fire(swalOption('error', 'Reload Rrowser'));
                    });
                }
            });
        }
    });
}
////////////////////////////////////////////////
//  keyup edit
////////////////////////////////////////////////
const keyupEdit = () => {
    [].slice.call(document.getElementsByClassName('keyup_edit')).forEach( v => {
        v.onkeyup = e => {
            let fdo = new FormData();
            fdo.append("value", e.target.value);
            fdo.append("name", e.target.name);
            fdo.append("table", e.target.dataset.table);
            asyncPost( `${asyncUrl}keyup_edit.php`, fdo, null, json => {
                if (json.result == 0) {
                    console.log(json.msg);
                }
            });
        }
    });
}
////////////////////////////////////////////////
//  contents page name disabled
////////////////////////////////////////////////
const contentsDeisabled = () => {
    let page = document.querySelector('input[name=page]');
    let access_count = document.getElementById('access_count');
    if(page && access_count && parseInt(access_count.textContent) > 0){
        page.readOnly = true;
    }
}
