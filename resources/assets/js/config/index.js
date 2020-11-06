import moment from 'moment'

// Optimized

let defaultConfig = {
    env: 'development',
    appName: 'OC',
    http: {
        defaultRequest: {
            headers: {
                'X-Requested-With':     'XMLHttpRequest',
                'Accept':               'application/json',
                'UTC-Offset':            moment().utcOffset(),
                'Access-Control-Allow-Origin': '*',
                'Content-Type': 'application/json',
            }
        }
    },
    withCredentials: true,
    size: {
        desktop: 1199,
        tablet:  991,
        phone:   767,
    },
    vapidPublicKey: process.env.MIX_VAPID_PUBLIC_KEY,
    vuexLogger: false,
    debug: true,
    defaultRoleName: 'Member',
    defaultPrimaryId: 2,
    defaultTimeZone: 'Europe/Berlin',
    defaultColor: {
        hex: '#194d33',
        hsl: {
            h: 150,
            s: 0.5,
            l: 0.2,
            a: 1
        },
        hsv: {
            h: 150,
            s: 0.66,
            v: 0.30,
            a: 1
        },
        rgba: {
            r: 25,
            g: 77,
            b: 51,
            a: 1
        },
        a: 1
    },
};

export const routeConfig = {
  filter: 'Filter',
  board: 'board',
  deadline: 'Deadline',
  deadlineDay: 'day',
  deadlineWeek: 'week',
};

export default Object.assign(defaultConfig, window.Laravel.config);
