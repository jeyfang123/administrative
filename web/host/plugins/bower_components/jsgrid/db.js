(function() {

    var db = {

        loadData: function(filter) {
            return $.grep(this.clients, function(client) {
                return (!filter.姓名 || client.姓名.indexOf(filter.姓名) > -1)
                    && (!filter.处理时间 || client.处理时间 === filter.处理时间)
                    && (!filter.处理内容 || client.处理内容.indexOf(filter.处理内容) > -1)
                    && (!filter.事件状态 || client.事件状态 === filter.事件状态)
                    && (filter.处理图片 === undefined || client.处理图片 === filter.处理图片);
            });
        },

        insertItem: function(insertingClient) {
            this.clients.push(insertingClient);
        },

        updateItem: function(updatingClient) { },

        deleteItem: function(deletingClient) {
            var clientIndex = $.inArray(deletingClient, this.clients);
            this.clients.splice(clientIndex, 1);
        }

    };

    //window.db = db;
    db.countries = [
        { 事件状态: "", Id: 0 },
        { 事件状态: "调度中", Id: 1 },
        { 事件状态: "处理中", Id: 2 },
        { 事件状态: "待审核", Id: 3 },
        { 事件状态: "处理完成", Id: 4 },
        { 事件状态: "无效", Id: 5 }
    ];
var i=0;
var num1=10*i;
var atte=[];
$.ajax({
        type:"POST",
         url:"/admin/EventHandler/getEventList",
         dataType:"json",
         data:{
            columns:{},
            offset:0,
            size:1000,
            token:'3a8b68c6fd2dc6d368dab0a6eca65c89'

         },
         success:function(data){
            if(data.code==200){
                  console.log(data);
                  var result=data.data.rows;
                 console.log(data.data.rows);
                 for (var i = 0; i < result.length; i++) {
                    var userid=result[i].userid;
                    var eventaddr=result[i].eventaddr;
                    var eventcontent=result[i].eventcontent;
                    var eventtime=result[i].eventtime;
                    var status=result[i].status;
                    console.log(status);
                    var dd = {
                        "上报人员ID": userid,
                        "事件地址": eventaddr,
                        "事件内容": eventcontent,
                        "处理时间": eventtime,
                        "事件状态": status,
                        "详情":"查看"
                    };                    
                    atte.push(dd);
                 }                
                db.clients=atte;
               createTable(document, window, jQuery,db);
                
            }
         }
    })

    
    
                


    /*db.clients = [
        {
            "姓名": "测试人员1",
            "事件详情": "电梯故障",
            "处理时间": 58,
            "事件状态": 1,
            "处理内容": "847-4303 Dictum Av.",
            "处理图片": true,
            "详情":"查看"
        },s
        {
            "姓名": "测试人员1",
            "事件详情": "电梯故障",
            "处理时间": 74,
            "事件状态": 4,
            "处理内容": "792-6145 Mauris St.",
            "处理图片": true,
            "详情":"查看"
        }
    ];
    */

}());

