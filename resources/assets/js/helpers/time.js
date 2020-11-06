let getTimeMinutes = (budget) => {

    if (!budget) {
        budget = "00:00"
    }

    let [hours, minutes] = budget.split(':');

    return Math.floor(+hours * 60) + +minutes;
};

/**
 * getLoggedTime
 *
 * @param time {object} example {h:2,i:10,s:3}
 * @returns {string} example '2 h 10 m 3 s'
 * *
 */

let getLoggedTime = (time) => {
    let loggedTime = '';

    if (time.h) {
        loggedTime += time.h + ' h'
    }

    if (time.i) {
        loggedTime += ' ' + time.i + ' m'
    }

    if (time.s) {
        loggedTime += ' ' + time.s + ' s'
    }

    return loggedTime.trim();
};

let secondsToHHMM = (interval) => {
    var sec_num = parseInt(interval, 10);
    var hours   = Math.floor(sec_num / 3600);
    var minutes = Math.floor((sec_num - (hours * 3600)) / 60);

    if (hours   < 10) {hours   = "0"+hours;}
    if (minutes < 10) {minutes = "0"+minutes;}

    return hours+':'+minutes;
};

let secondsToHHMMSS = (interval) => {
    var sec_num = parseInt(interval, 10);
    var hours   = Math.floor(sec_num / 3600);
    var minutes = Math.floor((sec_num - (hours * 3600)) / 60);
    var seconds = sec_num - (hours * 3600) - (minutes * 60);

    if (hours   < 10) {hours   = "0"+hours;}
    if (minutes < 10) {minutes = "0"+minutes;}
    if (seconds < 10) {seconds = "0"+seconds;}

    return hours+':'+minutes+':'+seconds;
};

export {getTimeMinutes, getLoggedTime, secondsToHHMMSS, secondsToHHMM}
