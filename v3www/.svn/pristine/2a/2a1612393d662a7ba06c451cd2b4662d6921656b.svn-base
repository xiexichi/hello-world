/* eslint-disable */

export default class Time {

    /**
     * 获取时间戳
     * @param [date] yyyy-mm-dd hh:ii:ss
     */
     static timestamp(strdate) {
        let date = new Date();
        if(strdate){
            strdate = this.convertDate(strdate);
            date = new Date(strdate);
        }
        return Math.round(date.getTime()/1000);
    }

    /**
     * 格式化时间戳
     * @param [date] timestamp
     */
    static formatTime(fmt = 'yyyy-MM-dd hh:mm:ss', strdate) { //author: meizz
        let date = new Date();
        if(strdate){
            strdate = this.convertDate(strdate);
            date = new Date(strdate);
        }
        var o = {
            "M+": date.getMonth() + 1,                 //月份
            "d+": date.getDate(),                    //日
            "h+": date.getHours(),                   //小时
            "m+": date.getMinutes(),                 //分
            "s+": date.getSeconds(),                 //秒
            "q+": Math.floor((date.getMonth() + 3) / 3), //季度
            "S": date.getMilliseconds()             //毫秒
        };
        if (/(y+)/.test(fmt))
            fmt = fmt.replace(RegExp.$1, (date.getFullYear() + "").substr(4 - RegExp.$1.length));
        for (var k in o)
            if (new RegExp("(" + k + ")").test(fmt))
                fmt = fmt.replace(RegExp.$1, (RegExp.$1.length == 1) ? (o[k]) : (("00" + o[k]).substr(("" + o[k]).length)));
        return fmt;
    }


    /**
     * 格式化时间，返回数组
     * @param [strdate] yyyy-mm-dd hh:ii:ss
     */
    static getTimeArray(strdate){ 
        var show_day=new Array('周日','周一','周二','周三','周四','周五','周六'); 
        if(strdate){
            strdate = this.convertDate(strdate);
            var time=new Date(strdate);
        }else{
            var time=new Date();
        }
        var year=time.getFullYear();
        var month=time.getMonth()+1; 
        var date=time.getDate(); 
        var day=time.getDay(); 
        var hour=time.getHours(); 
        var minutes=time.getMinutes(); 
        var second=time.getSeconds(); 
        /*  month<10?month='0'+month:month;  */
        hour<10?hour='0'+hour:hour; 
        minutes<10?minutes='0'+minutes:minutes; 
        second<10?second='0'+second:second;
        return {
            year: year,
            month: month,
            day: date,
            hour: hour,
            minutes: minutes,
            second: second,
            week: show_day[day]
        }
    }

    /**
     * 计算两个时间相差天数
     * @param [start] yyyy-mm-dd
     * @param [end] yyyy-mm-dd
     */
    static diffDay (start, end) {
        start = this.convertDate(start);
        end = this.convertDate(end);
        var dateFrom = new Date(start);
        var dateTo = new Date(end);
        var diff = dateTo.valueOf() - dateFrom.valueOf();
        var diff_day = parseInt(diff/(1000*60*60*24));
        return diff_day;
    }

    /**
     * 显示友好天数
     * @param [date] yyyy-mm-dd
     */
    static friendlyDay (date){
        var today = this.formatTime("yyyy-MM-dd");
        var diff_day = this.diffDay(today,date);
        var friendlyStr = '';
        switch(diff_day){
            case 0:
                friendlyStr = '今天';
                break;
            case 1:
                friendlyStr = '明天';
                break;
            case 2:
                friendlyStr = '后天';
                break;
            case -1:
                friendlyStr = '昨天';
                break;
            case -2:
                friendlyStr = '前天';
                break;
            default:
                var arr = this.getTimeArray(date);
                friendlyStr = arr.week;
                break;

        }
        return friendlyStr;
    }

    /**
     * 格式化秒数显示
     * @param [date] yyyy-MM-dd hh:ii:ss
     */
    static sec2time (s, fmt='') {
        let t = '';
        let day = 0,
            hour = 0,
            minutes = 0,
            second = 0;

        if(s > -1){
            day = Math.floor(s/3600/24);
            hour = Math.floor(s/3600);
            minutes = Math.floor(s/60) % 60;
            second = s % 60;
            // 补0
            if(minutes < 10) minutes = "0"+minutes;
            if(second < 10) second = "0"+second;
        }

        // 格式化显示
        if( fmt ){
            if (/(d+)/.test(fmt) && day>0) fmt = fmt.replace(RegExp.$1, day);
            if (/(h+)/.test(fmt) && hour>0) fmt = fmt.replace(RegExp.$1, hour);
            if (/(i+)/.test(fmt)) fmt = fmt.replace(RegExp.$1, minutes);
            if (/(s+)/.test(fmt)) fmt = fmt.replace(RegExp.$1, second);
            return fmt;
        }
        // 返回数组
        return {
            day: day,
            hour: hour,
            minutes: minutes,
            second:second
        };
    }


    /**
     * 转换时间，解决ios new date()问题
     * yyyy-MM-dd 转为 yyyy/MM/dd
     */
    static convertDate(str) {
        if(typeof(str) == 'string' && str.indexOf('-') > -1){
            return str.replace(/-/g, "/");
        }else{
            return str;
        }
    }


    /**
     * 允许退订时间
     */
    static allowRefundTime(checkinTime, allowLimit) {
        let diff = parseInt(checkinTime)-parseInt(allowLimit);
        let day = 0;
        let diffDay = '';
        // 相差天数
        day = parseInt(diff/24);
        // 相差小时数
        diff = diff-(24*day);
        if( day < 0 ){
            diffDay = '前'+Math.abs(day)+'天';
        }else if(day == 0){
            diffDay = '当天';
        }else{
            diffDay = '第'+(day+1)+'天';
        }
        return diffDay+Math.abs(diff)+'点前';
    }
}