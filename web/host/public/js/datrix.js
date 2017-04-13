
var DanaClient = function (file,doc,dzprogress) {
    var totalSize = 0;
    var currentSize = 0;
    var access_token = '';
    function create(file) {
        var fileid = false;
        $.ajax({
        'url':"/property/upload/create",
        'dataType':'json',
        'data':{
            'filename':file['name'],
            'filesize':file['size'],
            'token' : $.cookie('token')
        },
        'type':'post',
        'async':false,
        'success':function (res) {
            if(res && res.code == 200){
                fileid = res.result.fileid;
                access_token = res.access_token;
            }
            else
                fileid = false;
        },
        'error':function () {
            fileid = false;
        }
        });
        return fileid;
    }

    function writeall(fileid,file) {
        var  success = false;
        var block = 4 * 1024 * 1024;
        totalSize = file.size;
        currentSize = 0;
        if(file.size <= block){
           return write(fileid,file,0,file.size);
        }
        else {
            var times = Math.floor(file.size / block);
            for(var i =0;i<times;i++){
                currentSize = i*block;
                success = write(fileid,file,i*block,(i+1)*block);
                if(success === false)
                    return false;
            }
            if(times*block < file.size) {
                currentSize = times * block;
                return write(fileid, file, times * block, file.size);
            }
        }
        return false;
    }
    function write(fileid,file,start,end) {
        var  success = false;
        var data =new FormData();
        data.append("fileid", fileid);
        var func = (file.mozSlice ? 'mozSlice' : (file.webkitSlice ? 'webkitSlice' : 'slice'));
        data.append("PostData", file[func](start,end));
        data.append("offset", start);
        data.append("length", end - start);
        data.append("filesize",file.size);
        var xhr = new XMLHttpRequest();
        xhr.open("POST", 'http://10.1.7.12/api/cluster/storage/file/write',false);
        xhr.setRequestHeader('Authorization', 'DATATOM DATATOM4UAcOR3uLx3bx49kIDdAhaRCAr:RTlGNjU3N0EyOTM2RkMyRkQxQTAzQkUyN0FBQUJEM0VDMjRBQ0Y0MA==');
        xhr.setRequestHeader('ACCESS-TOKEN', access_token);
        xhr.onreadystatechange = function(){
            if (xhr.readyState === 4 && xhr.status === 200) {
                try {
                    var result = JSON.parse(xhr.responseText);
                    if(result.code === 200){
                        success = true;
                    }
                }catch(e) {}
            }
        }
        xhr.upload.onprogress = progress;
        xhr.send(data);
        return success;
    }
    function progress(event) {
        if (event.lengthComputable && totalSize !== 0) {
            var percentComplete =parseInt(((event.loaded + currentSize)/ totalSize) * 100);
            // $("#progress").find(".progress-bar").width(percentComplete +"%");
            dzprogress.width(percentComplete +"%");
            // $("#progress").show();
            doc.show();
        }
    }
    function finish(fileid) {
        var success = false;
        $.ajax({
            'url':"/property/upload/finish",
            'dataType':'json',
            'data':{
                'fileid':fileid,
                'token' : $.cookie('token')
            },
            'type':'post',
            'async':false,
            'success':function (res) {
                if(res && res.code == 200){
                    success = true;
                }
            },
            'error':function () {
                success = false;
            }
        });
        return success;
    }
    var fileid = create(file);
    if(fileid === false) {
        this.msg = "文件创建失败！";
        this.isOk = false;
        return false;
    }
    if(writeall(fileid,file) === false){
        this.msg = "文件上传失败！";
        this.isOk = false;
        return false;
    }
    if(finish(fileid) === false){
        this.msg = "文件结束失败！";
        this.isOk = false;
        return false;
    }
    this.msg = "文件上传成功！";
    // doc.hide();
    this.fileid = fileid;
    this.isOk = true;
    this.access_token = access_token;
    return true;
}




