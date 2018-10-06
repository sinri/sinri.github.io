Vue.directive('highlight', function (el) {
    let blocks = el.querySelectorAll('pre code');
    blocks.forEach((block) => {
        hljs.highlightBlock(block)
    })
});
Vue.component("static-doc-component",{
    template:"<div>"
    +"<row style='width:100%;height: 51px;border-bottom: 1px solid gray;background: white;position: fixed;top:0;z-index: 10;'>"
    +"<template v-if='isMobile()'>"
            +'<i-col span="16">'
                +'<div style="padding-left: 10px">'
                    +'<i-button type="info" icon="md-book" @click="mobileMenuSwitch()" style="margin-top: -12px;padding-left: 10px;padding-right: 10px;"></i-button>'
                    +'<Avatar style="margin-top: -12px;" :src="avatar"></Avatar>'
                    +'<h1 style="line-height: 50px;display: inline">Sinri@GitHub</h1>'
                +'</div>'
            +'</i-col>'
        +'</template>'
        +'<template v-else>'
            +'<i-col span="3">'
                +'<div style="padding-left: 10px;line-height: 50px;">'
                    +'<i-button type="info" icon="md-book" @click="mobileMenuSwitch()" style="padding-left: 10px;padding-right: 10px;"></i-button>'
                +'</div>'
            +'</i-col>'
            +'<i-col span="18">'
                +'<div style="padding-left: 10px;text-align: center">'
                    +'<Avatar style="margin-top: -12px;" :src="avatar"></Avatar>'
                    +'<h1 style="line-height: 50px;display: inline">Sinri@GitHub</h1>'
                +'</div>'
            +'</i-col>'
        +'</template>'
        +'<i-col span="3">'
            +'<div class="sharethis-inline-share-buttons" style="padding-right: 10px;margin-top:10px;"></div>'
        +'</i-col>'
    +'</row>'
    +'<div style="margin-top:50px;">'
    +'<template  v-if="isMobile()">'
        +'<Modal title="Menu" footer-hide mask-closable v-model="isMobileMenuOpen">'
            +'<div style="width:100%;height: 60vh;overflow: auto;" >'
                +'<i-menu accordion width="auto" style="z-index: auto;" @on-select="openMenuItemAsMarkdown">'
                    +'<template v-for="(menu_item,menu_item_index) in menu">'
                        +'<template v-if="menu_item.type===\'markdown\'">'
                            +'<menu-item :name="menu_item.link">{{menu_item.title}}</menu-item>'
                        +'</template>'
                        +'<Submenu v-else-if="menu_item.type===\'folder\'" :name="menu_item_index" style="z-index: auto;">'
                            +'<template slot="title">'
                                +'{{menu_item.title}}'
                            +'</template>'
                            +'<template v-for="menu_item_inside in menu_item.pages">'
                                +'<menu-item v-if="menu_item_inside.type===\'markdown\'" :name="menu_item_inside.link" style="z-index: auto;">{{menu_item_inside.title}}</menu-item>'
                            +'</template>'
                        +'</Submenu>'
                        +'<template v-else>'
                            +'<menu-item :name="menu_item.link">{{menu_item.title}}</menu-item>'
                        +'</template>'
                    +'</template>'
                +'</i-menu>'
            +'</div>'
        +'</Modal>'
        +'<row>'
            +'<i-col span="24">'
                +'<div style="border-bottom: 1px solid gray;padding: 10px;height: 40px;" v-if="in_dir!==\'\'">'
                    +'<h3 style="text-align: center">~ {{in_dir}} ~</h3>'
                +'</div>'
                +'<div :style="{margin: \'24px 0 64px 0\',padding: \'0 24px\', minHeight: \'70vh\', background: \'#fff\'}">'
                    +'<div class="markdown-body" v-html="compiledMarkdown" v-highlight></div>'
                +'</div>'
            +'</i-col>'
        +'</row>'
    +'</template>'
    +'<row v-else>'
        +'<Drawer title="Menu" placement="left" :closable="false" v-model="isMobileMenuOpen" scrollable :width="40">'
            +'<div>'
                +'<ul style="margin-left:20px;">'
                    +'<li v-for="(menu_item,menu_item_index) in menu" :key="menu_item_index">'
                        +'<template v-if="menu_item.type===\'markdown\'">'
                            +'<a class="menu_item" href="javascript:void(0)" v-on:click="openMenuItemAsMarkdown(menu_item.link)">{{menu_item.title}}</a>'
                        +'</template>'
                        +'<template v-else-if="menu_item.type===\'folder\'">'
                            +'<div>'
                                +'<a class="menu_item" href="javascript:void(0)" v-on:click="switchFolder(menu_item_index)">{{menu_item.title}}</a>'
                            +'</div>'
                            +'<ul v-show="folder_switch[menu_item_index]" style="margin-left:20px;">'
                                +'<li v-for="menu_item_inside in menu_item.pages">'
                                    +'<template v-if="menu_item_inside.type===\'markdown\'">'
                                        +'<a class="menu_item" href="javascript:void(0)" v-on:click="openMenuItemAsMarkdown(menu_item_inside.link)">{{menu_item_inside.title}}</a>'
                                    +'</template>'
                                +'</li>'
                            +'</ul>'
                        +'</template>'
                        +'<template v-else>'
                            +'<a class="menu_item" href="javascript:void(0)">{{menu_item.title}}</a>'
                        +'</template>'
                    +'</li>'
                +'</ul>'
            +'</div>'   
        +'</Drawer>'
        +'<i-col span="24">'
            +'<div style="border-bottom: 1px solid gray;padding: 10px;height: 40px;" v-if="in_dir!==\'\'">'
                +'<h3 style="text-align: center">~ {{in_dir}} ~</h3>'
            +'</div>'
            +'<div :style="{margin: \'24px 0 64px 0\',padding: \'0 50px\', minHeight: \'70vh\', background: \'#fff\'}">'
                +'<div class="markdown-body" v-html="compiledMarkdown" v-highlight></div>'
            +'</div>'
        +'</i-col>'
    +'</row>'
    +'</div>'
    +'<div style="height: 40px;width:100%;z-index: 10;position: fixed;bottom:0;background:white;border-top: 1px solid lightgray;">'
        +'<div style="line-height:30px;text-align: center">{{site_declaration}}</div>'
    +'</div>'
+"</div>",
    data: function () {
        return {
            avatar:'',
            site_title:'',
            site_declaration:'',
            menu:[],
            folder_switch:[],
            markdownContent:'',
            isMobileMenuOpen:false,
            in_dir:"",
        }
    },
    props: ['message'],
    computed: {
        compiledMarkdown: function () {
            return marked(this.markdownContent, {sanitize: false})
        }
    },
    methods:{
        loadMenu:function(){
            axios.get('./meta.json')
            .then(response=>{
                console.log(response);
                if(response.status===200){
                    this.menu=response.data.menu;
                    this.folder_switch=[];
                    for(let i=0;i<this.menu.length;i++){
                        if(this.menu[i].type==='folder'){
                            this.folder_switch[i]=false;
                        }else{
                            this.folder_switch[i]=true;
                        }
                    }
                    this.avatar=response.data.avatar;
                    this.site_title=response.data.site_title;
                    this.site_declaration=response.data.site_declaration;
                }else{
                    alert("Failed to load menu!");
                }
            })
            .catch((error) => {
                alert("Failed to call menu data!");
            });
        },
        openMenuItemAsMarkdown:function(link){
            let parts=link.split("/");
            console.log("debug link",link,parts);
            if(parts.length>1){
                this.in_dir=parts[0];
            }else{
                this.in_dir="";
            }
            axios.get('./data/'+link)
            .then(response=>{
                console.log(response);
                if(response.status===200){
                    this.markdownContent=response.data;

                    console.log("debug href",location);
                    window.history.pushState({},0,"./index.html?openPageLink="+encodeURIComponent(link));
                    
                    let tmp=/#\s*(.+)/.exec(this.markdownContent);
                    console.log("title tmp",tmp);
                    if(tmp.length>1){
                        document.title=tmp[1]+" - "+'Sinri@GitHub';
                    }
                    else {
                        document.title='Sinri@GitHub';
                    }

                    this.isMobileMenuOpen=false;
                }else{
                    alert("Failed to call page!");
                }
            })
            .catch((error) => {
                this.markdownContent = "# Target Not Found\n\n"+error+"\n\nTarget: '"+link+"'";
            });
        },
        switchFolder:function(menu_item_index){
            console.log("switchFolder",menu_item_index,this.folder_switch[menu_item_index]);
            Vue.set(this.folder_switch,menu_item_index, !this.folder_switch[menu_item_index]);
            //this.folder_switch[menu_item_index]=!this.folder_switch[menu_item_index];
        },
        isMobile:function(){
            //detection PC and Mobile 
            if(!!navigator.userAgent.match(/AppleWebKit.*Mobile.*/) && !!navigator.userAgent.match(/AppleWebKit/)){ 
                //document.writeln('Browser:'+'Mobile Browser'+'<hr>'); 
                return true;
            }else { 
                //document.writeln('Browser:'+'Desktop Browser'+'<hr>'); 
                return false;
            }
        },
        mobileMenuSwitch:function(){
            this.isMobileMenuOpen=!this.isMobileMenuOpen;
        }
    },
    mounted:function() {
        this.loadMenu();
        this.openMenuItemAsMarkdown('welcome.md');

        console.log("isMobile",this.isMobile());

        console.log('window.location.search',window.location.search);
        if(window.location.search.startsWith("?")){
            let queryString=window.location.search.substring(1);
            if(queryString.length>0){
                let queries={};
                queryString.split("&").forEach((entry)=>{
                    let pair=entry.split("=");
                    queries[pair[0]]=pair[1];
                })
                console.log('queries',queries);

                if(queries.openPageLink){
                    this.openMenuItemAsMarkdown(decodeURIComponent(queries.openPageLink));
                }
            }
        }
    },
});