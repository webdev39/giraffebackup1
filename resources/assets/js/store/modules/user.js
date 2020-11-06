import config from '@config'

const getDefaultState = () => {
    return {
        id:                 null,
        firstName:          null,
        lastName:           null,
        nickname:           null,
        email:              null,

        avatar:             null,
        background:         null,
        timeZone:           null,

        tenantId:           null,
        userTenantId:       null,
        isOwner:            false,
        canInvited:         false,
        tour:               null,
        audio:              null,

        primaryColor:       null,
        secondaryColor:     null,

        language:           {},
        font:               {},

        notifyTypes:        [],

        currentTheme:       0,
        themes:             [],
        view_types:         [],
    }
}

const state = getDefaultState();

const getters = {
    getUserProfile(state) {
        return state;
    },
    getFullName(state) {
        return `${state.firstName} ${state.lastName}`;
    },
    getUserInitials(state) {
        if(state.firstName && state.lastName){
            return state.firstName[0] + state.lastName[0];
        }

        return '';
    },
    getAvatar(state) {
        return state.avatar;
    },
    getBackground(state) {
        return state.background;
    },
    getTimeZone(state) {
        return state.timeZone;
    },
    getFontName(state) {
        if (state.font) {
            return state.font.name;
        }

        return null;
    },
    getTenantId(state) {
        return state.tenantId;
    },
    getUserTenantId(state) {
        return state.userTenantId;
    },
    getUserId(state) {
        return state.id || null;
    },
    getPrimaryColor(state) {
        return state.primaryColor;
    },
    getSecondaryColor(state) {
        return state.secondaryColor;
    },
    isTenant(state) {
        return state.isOwner;
    },
    canInvited(state) {
        return state.canInvited;
    },
    getThemes (state) {
        return state.themes;
    },
    getCurrentTheme (state, getters) {
        return getters.getThemes.find(item => item.id === state.currentTheme)
    },
    getUserViewTypes (state) {
        return state.view_types
    }
};

const actions = {
    setProfile({ commit, dispatch }, profile) {
        commit('setProfile',          profile);

        dispatch('setNotifyTypes',    profile.notify_types);

        dispatch('setAvatar',         profile.avatar);
        dispatch('setBackground',     profile.background);
        dispatch('setTimeZone',       profile.time_zone);

        dispatch('setCurrencyById',   profile.currency_id);
        dispatch('setLanguageById',   profile.language_id);
        dispatch('setFontById',       profile.font_id);

        dispatch('setDateFormat',     profile.date_format);
        dispatch('setMoneyFormat',    profile.money_format);

        dispatch('setPrimaryColor',   profile.primary_color);
        dispatch('setSecondaryColor', profile.secondary_color);

    },
    clearProfile({ commit }) {
        commit('clearProfile');
    },

    setNotifyTypes({ commit }, types) {
        commit('setNotifyTypes', types);
    },

    setAvatar({ commit }, avatar) {
        commit('setAvatar', avatar);
    },
    setBackground({ commit }, background) {
        commit('setBackground', background);
    },
    setAudio({ commit }, audio) {
        commit('setAudio', audio);
    },
    setTimeZone({ commit, dispatch }, timeZone) {
        if (timeZone) {
            commit('setTimeZone', timeZone);
        } else {
            dispatch('setDefaultTimeZone');
        }
    },

    setCurrencyById({ commit, rootGetters }, currencyId) {
        commit('setCurrency', rootGetters['default/getCurrencies'].find(item => item.id === currencyId));
    },
    setLanguageById({ commit, rootGetters }, languageId) {
        commit('setLanguage', rootGetters['default/getLanguages'].find(item => item.id === languageId));
    },
    setFontById({ commit, rootGetters }, fontId) {
        commit('setFont', rootGetters['default/getFonts'].find(item => item.id === fontId));
    },

    setDateFormat({ commit }, dateFormat) {
        commit('setDateFormat', dateFormat);
    },
    setMoneyFormat({ commit }, moneyFormat) {
        commit('setMoneyFormat', moneyFormat);
    },

    setPrimaryColor({ commit }, color) {
        if (!color) {
            color = config.defaultColor;
        }

        commit('setPrimaryColor', color);
    },
    setSecondaryColor({ commit }, color) {
        if (!color) {
            color = config.defaultColor;
        }

        commit('setSecondaryColor', color);
    },
    changeThemes({ commit }, payload) {
        commit('changeThemes', payload);
    },
    setThemeId({ commit }, payload) {
        commit('setThemeId', payload);
    },
    setTheme({ commit }, payload) {
        commit('setTheme', payload);
    },
    addTheme({ commit }, payload) {
        commit('addTheme', payload);
    },
    removeTheme({ commit }, id) {
        commit('removeTheme', id);
    },
    setUserViewTypes({ commit }, payload) {
        commit('setUserViewTypes', payload);
    },
    setDefaultTimeZone({ commit }) {
        commit('setDefaultTimeZone', config.defaultTimeZone);
    }
};

const mutations = {
    setProfile (state, profile) {
        state.id                = profile.id;
        state.firstName         = profile.name;
        state.lastName          = profile.last_name;
        state.nickname          = profile.nickname;
        state.email             = profile.email;

        state.userTenantId      = profile.user_tenant_id;
        state.tenantId          = profile.tenant_id;
        state.isOwner           = profile.is_owner;
        state.canInvited        = profile.can_invited;
        state.tour              = profile.tour;
        state.audio             = JSON.parse(profile.audio);
    },
    clearProfile (state) {
        Object.assign(state, getDefaultState())
        //state = {...initialState};
    },

    setNotifyTypes(state, types) {
        state.notifyTypes = types;
    },

    setAvatar(state, avatar) {
        state.avatar = avatar;
    },

    setBackground(state, background) {
        state.background = background;
    },

    setAudio(state, audio) {
        state.audio = audio;
    },

    setTimeZone(state, timeZone) {
        state.timeZone = timeZone;
    },

    setFont(state, font) {
        state.font = font
    },
    setLanguage(state, language) {
        state.language = language
    },
    setCurrency(state, currency) {
        state.currency = currency
    },

    setDateFormat(state, dateFormat) {
        state.dateFormat = dateFormat
    },
    setMoneyFormat(state, moneyFormat) {
        state.moneyFormat = moneyFormat
    },

    setPrimaryColor(state, color) {
        state.primaryColor = color;
    },
    setSecondaryColor(state, color) {
        state.secondaryColor = color;
    },
    changeThemes(state, payload) {
        state.themes.find(item => {
            if (item.id === payload.id) {
                Object.assign(item, payload);
                return true;
            }
        })
    },
    setThemeId(state, payload) {
        state.currentTheme = payload;
    },
    setTheme(state, payload) {
        state.themes = payload;
    },
    addTheme(state, payload) {
        state.themes.push(payload);
    },
    removeTheme(state, id) {
        state.themes = state.themes.filter(item => item.id !== id);
    },
    setUserViewTypes(state, payload) {

        if (state.view_types.length) {
            let viewType = state.view_types.find(item => {

                if (item.board_id === payload.board_id) {
                    Object.assign(item, payload);
                    return true
                }
            });

            if (viewType) {
                return
            }
        }

        return state.view_types.push(payload);
    },
    setDefaultTimeZone(state, payload) {
        state.timeZone = payload;
    }
};

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations
}
