const RecordList = class {
    init = {
        'url': `${dataUrl}`,
        'imagesurl' : `${dataImagesUrl}`,
        'post_file' : `${asyncUrl}list_controller.php`,
        'reset_file' : `${asyncUrl}reset.php`,
        'form_name' : 'search',
        'list_area_id' : 'list_area',
        'nav_class' : 'list_nav',
        'input_sp' : 'input_sp',
        'input_p' : 'input_p',
        'input_c' : 'input_c',
        'input_s' : 'input_s',
        'sort_btn_class' : 'sort_btn',
        'all_records' : 'all_records',
        'current_page' : 'current_page',
        'set_view_count' : 'set_view_count',
        'page_btn_class' : 'p_link_nav',
        'search_btn_class' : 'search_btn',
        'search_reset_class' : 'search_reset',
        'lang' : 'ja',
        'page' : 'public',
        'method' : '',
        'file_size': 0
    }
    constructor(config, callback=null){
        for (let key in config) {
            this.init[key] = config[key];
        }
        this.init.input_sp = document.querySelector(`form[name=${this.init.form_name}] input[name=${this.init.input_sp}]`).value;
        this.init.input_p = document.querySelector(`form[name=${this.init.form_name}] input[name=${this.init.input_p}]`).value;
        this.init.input_c = document.querySelector(`form[name=${this.init.form_name}] input[name=${this.init.input_c}]`).value;
        this.init.input_s = document.querySelector(`form[name=${this.init.form_name}] input[name=${this.init.input_s}]`).value;
        this.input_c_elm = document.querySelector(`form[name=${this.init.form_name}] input[name=${this.init.input_c}]`);
        this.input_s_elm = document.querySelector(`form[name=${this.init.form_name}] input[name=${this.init.input_s}]`);
        this.callback = callback;
    }
    getList() {
        let form_elm = document.querySelector(`form[name=${this.init.form_name}]`);
        let list_area = document.getElementById(this.init.list_area_id);
        if(list_area && form_elm){
            let form = createFormData(form_elm);
            form.append("form_name", this.init.form_name);
            form.append("lang", this.init.lang);
            form.append("method", this.init.method);
            form.append("page", this.init.page);
            form.append("url", this.init.url);
            form.append("imagesurl", this.init.imagesurl);
            list_area.innerHTML = this.loadingHTML();
            if(this.init.post_file){
                let xhr = XMLHttpRequestCreate();
                xhr.open('POST', this.init.post_file);
                xhr.send(form);
                xhr.onload = () => {
                    if(xhr.status == 200){
                        new Promise( (resolve, reject) => {
                            list_area.innerHTML = xhr.responseText;
                            resolve(1);
                            reject(0);
                        }).then( e => {
                            this.sortBtn();
                            this.createPagerDom();
                            this.pageLinkEvt();
                            this.listResultNumbers();
                            if(this.callback)this.callback();
                        }).catch( reason => {
                            console.log(reason);
                        });
                    }
                }
                xhr.onerror = error => {
                    console.log(error);
                }
                xhr.onprogress = e => {
                }
            }
        }
    }

    loadingHTML(){
        return '<p class="text-center"><i class="fad fa-spinner-third fa-spin fa-3x" style="color:#6259ca"></i></p>';
    }

    listResultNumbers(){
        let s = document.getElementById('result_records_num_view');
        if(s)s.innerHTML = '';
        let g = document.getElementById('all_records');
        if(s && g){
            s.innerHTML = `${g.textContent} Records`;
        }
    }

    sortBtn(){
        let sort_btn = [].slice.call(document.getElementsByClassName(this.init.sort_btn_class));
        sort_btn.forEach( v => {
            v.style.cursor = 'pointer';
            v.onclick = e => {
                new Promise( resolve => {
                    sort_btn.forEach( vv => {
                        vv.classList.remove('text-danger');
                    });
                    e.target.classList.add('text-danger');
                    this.input_c_elm.value = e.target.dataset.column;
                    if(e.target.classList.contains('fa-sort-down')){
                        //change up
                        e.target.classList.remove('fa-sort-down');
                        e.target.classList.add('fa-sort-up');
                        this.input_s_elm.value = 'asc';

                    }else{
                        //change down
                        e.target.classList.remove('fa-sort-up');
                        e.target.classList.add('fa-sort-down');
                        this.input_s_elm.value = 'desc';
                    }
                    this.sortBtnReset(v);
                    resolve();
                }).then( () => {
                    this.listPreviewEvt();
                });
            };
        });
    }

    sortBtnReset(current){
        [].slice.call(document.getElementsByClassName(this.init.sort_btn_class)).forEach( v => {
            if(current != v){
                v.classList.remove('fa-sort-up');
                v.classList.add('fa-sort-down');
            }
        });
    }

    listPreviewEvt(){
        let column = document.querySelector(`[data-column=${this.input_c_elm.value}]`);
        if(column){
            let order = this.input_s_elm.value;
            if(order.toUpperCase() == 'DESC'){
                column.classList.remove('fa-sort-up');
                column.classList.add('fa-sort-down');
            }else{
                column.classList.remove('fa-sort-down');
                column.classList.add('fa-sort-up');
            }
            column.classList.add('text-danger');
        }
        this.getList();
    }

    createPagerDom(){
        let all_records = document.getElementById(this.init.all_records);
        let current_page = document.getElementById(this.init.current_page);
        let set_view_count = document.getElementById(this.init.set_view_count);
        let ul,a = 0,c = 0,s = 0;
        [].slice.call(document.getElementsByClassName(this.init.nav_class)).forEach( v => {
            v.innerHTML = '';
        });
        [].slice.call(document.getElementsByClassName(this.init.nav_class)).forEach( v => {
            a = parseInt(all_records.textContent);
            c = parseInt(current_page.textContent) + 1;
            s = parseInt(set_view_count.textContent);
            let all_p_num = a < s ? 1 : Math.ceil(a / s);
            
            if(all_p_num > 1){
                ul = document.createElement('ul');
                ul.setAttribute('class', 'pagination pagination-sm my-2');
                let ii = 1;
                let last = ii + 9;
                if(c > 10){
                    ii = c;
                    last = (ii + 9) > all_p_num ? all_p_num:(ii + 9);
                }else{
                    ii = all_p_num > 10 ? c:1;
                    last = (all_p_num - c) > 10 ? (c + 9):all_p_num;
                }
                //　previous button
                if(c > 1){
                    // first page
                    let li = document.createElement('li');
                    li.setAttribute('class', 'page-item');
                    let span = document.createElement('i');
                    span.setAttribute('class', `${this.init.page_btn_class} page-link fas fa-angle-double-left`);
                    span.setAttribute('data-pnum', 0);
                    li.appendChild(span);
                    ul.appendChild(li);
                    //　previous page
                    li = document.createElement('li');
                    li.setAttribute('class', 'page-item');
                    span = document.createElement('i');
                    span.setAttribute('class', `${this.init.page_btn_class} page-link fas fa-angle-left`);
                    span.setAttribute('data-pnum', (c - 2));
                    li.appendChild(span);
                    ul.appendChild(li);
                }
                //　page-number button
                for(let i = ii; i <= last; i++){
                    let li = document.createElement('li');
                    li.setAttribute('class', 'page-item');
                    if(c == i){
                        li.classList.add('active');
                    }
                    let span = document.createElement('span');
                    span.setAttribute('class', `${this.init.page_btn_class} page-link`);
                    span.setAttribute('data-pnum', (i - 1));
                    span.textContent = i;
                    li.appendChild(span);
                    ul.appendChild(li);
                }
                // next page
                if(c < all_p_num){
                    let li = document.createElement('li');
                    li.setAttribute('class', 'page-item');
                    let span = document.createElement('i');
                    span.setAttribute('class', `${this.init.page_btn_class} page-link fas fa-angle-right`);
                    span.setAttribute('data-pnum', c);
                    li.appendChild(span);
                    ul.appendChild(li);
                    //　last page
                    li = document.createElement('li');
                    li.setAttribute('class', 'page-item');
                    span = document.createElement('i');
                    span.setAttribute('class', `${this.init.page_btn_class} page-link fas fa-angle-double-right`);
                    span.setAttribute('data-pnum', (all_p_num - 1));
                    li.appendChild(span);
                    ul.appendChild(li);
                }
                v.appendChild(ul);
            }
        });
    }

    pageLinkEvt(){
        [].slice.call(document.getElementsByClassName(this.init.page_btn_class)).forEach( v => {
            v.onclick = e => {
                e.stopPropagation();
                e.preventDefault();
                new Promise( resolve => {
                    let pnum = e.target.dataset.pnum;
                    let input_p = document.querySelector(`form[name=${this.init.form_name}] input[name=${this.init.input_p}]`);
                    if(input_p){
                        input_p.value = pnum;
                    }
                    resolve();
                }).then( () => {
                    window.scrollTo({top: 0, behavior: 'smooth'});
                    this.listPreviewEvt();
                });
            }
        });
    }

    searchBtnHideEvt(bool){
        [].slice.call(document.getElementsByClassName(this.init.search_btn_class)).forEach( v => {
            if(bool){
                v.classList.add('d-none');
            }else{
                v.classList.remove('d-none');
            }
        });
    }

    searchReset(){
        [].slice.call(document.getElementsByClassName(this.init.search_reset_class)).forEach( v => {
            v.onclick = e => {
                e.stopPropagation();
                e.preventDefault();
                let fdo = new FormData();
                fdo.append('reset', v.dataset.reset);
                if(this.init.reset_file){
                    asyncPost(this.init.reset_file, fdo, null, json => {
                        new Promise( resolve => {
                            if (json.result > 0) {
                                resolve();
                            }
                        }).then( () => {
                            location.href = location.href.replace(/\?.*$/, '');
                        });
                    });
                }

            }
        });
    }

    searchBtnEvt(){
        [].slice.call(document.getElementsByClassName(this.init.search_btn_class)).forEach( v => {
            v.onclick = e => {
                e.preventDefault();
                e.stopPropagation();
                new Promise( resolve => {
                    this.searchBtnHideEvt(true);
                    let input_p = document.querySelector(`form[name=${this.init.form_name}] input[name=${this.init.input_p}]`);
                    if(input_p){
                        input_p.value = 0;
                    }
                    resolve();
                }).then( () => {
                    this.getList();
                    this.searchBtnHideEvt(false);
                });
            }
        });
    }
}
